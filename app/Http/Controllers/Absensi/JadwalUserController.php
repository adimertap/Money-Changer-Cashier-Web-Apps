<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;
use App\Models\JadwalKerja;
use App\Models\MasterCabang;
use App\Models\MasterShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          // Set the timezone to Asia/Makassar
          $timezone = new \DateTimeZone('Asia/Makassar');
          $todayDate = Carbon::today($timezone)->toDateString();

          $startOfMonth = Carbon::now($timezone)->startOfMonth()->toDateString();
          $endOfMonth = Carbon::now($timezone)->endOfMonth()->toDateString();

          $jadwal = JadwalKerja::withoutGlobalScope('cabang')
              ->with('Shift', 'User', 'Cabang')
              ->where('id', Auth::user()->id)
              ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
              ->get()
              ->sortBy('tanggal');

          $jadwalToday = JadwalKerja::withoutGlobalScope('cabang')
              ->with('Shift', 'User', 'Cabang')
              ->where('id', Auth::user()->id)
              ->whereDate('tanggal', $todayDate)
              ->whereHas('Shift')
              ->get();

          $countTodayStatusX = JadwalKerja::withoutGlobalScope('cabang')
              ->where('id', Auth::user()->id)
              ->whereDate('tanggal', $todayDate)
              ->whereHas('Shift')
              ->whereIn('status', ['X', 'T'])
              ->count();

          $today = Carbon::today($timezone);
          $jadwalTodayCount = $jadwalToday->count();
          $currentMonth = Carbon::now($timezone)->format('F');

          // Button Absen Setelah 4 Jam Masuk
          $jadwalMasukFilled = JadwalKerja::withoutGlobalScope('cabang')
              ->where('id', Auth::user()->id)
              ->whereDate('tanggal', $todayDate)
              ->whereHas('Shift')
              ->where('jam_masuk','!=', null)
              ->where('status', 'X')
              ->first();

          // Penentuan Cabang untuk Absensi:
          // 1. Dari jadwal hari ini jika memiliki cabang_id
          $cabangTarget = null;
          if ($jadwalToday->isNotEmpty() && $jadwalToday->first()->cabang_id) {
              $cabangTarget = MasterCabang::find($jadwalToday->first()->cabang_id);
          }

          // 2. Jika tidak ada di jadwal, cek session cabang aktif
          if (!$cabangTarget && session('cabang_aktif')) {
              $cabangTarget = MasterCabang::find(session('cabang_aktif'));
          }

          // 3. Jika tidak ada di session, ambil cabang yang di-assign ke user
          if (!$cabangTarget && Auth::check()) {
              $cabangTarget = Auth::user()->cabangs()->where('is_active', 1)->first()
                  ?? Auth::user()->cabangs()->first();
          }

          // 4. Fallback ke cabang aktif pertama di database
          if (!$cabangTarget) {
              $cabangTarget = MasterCabang::where('is_active', 1)->first() ?? MasterCabang::first();
          }

          $cabangName = $cabangTarget ? $cabangTarget->cabang_name : 'Kantor Pusat';
          $cabangAlamat = $cabangTarget ? ($cabangTarget->alamat ?: '-') : '-';
          $absenRadiusActive = $cabangTarget ? (bool) ($cabangTarget->absen_radius_active ?? true) : true;
          // Koordinat default PT Riasta Valasindo jika belum diset di cabang
          $fixedLatitude = (float) ($cabangTarget ? ($cabangTarget->latitude ?? $cabangTarget->lat ?? -8.701647497474847) : -8.701647497474847);
          $fixedLongitude = (float) ($cabangTarget ? ($cabangTarget->longitude ?? $cabangTarget->lng ?? 115.16637512084526) : 115.16637512084526);
          $cabangRadius = (int) ($cabangTarget && $cabangTarget->radius ? $cabangTarget->radius : 50);

          return view('absensi.absen', compact(
              'jadwalMasukFilled',
              'countTodayStatusX',
              'jadwal',
              'jadwalToday',
              'currentMonth',
              'jadwalTodayCount',
              'today',
              'fixedLatitude',
              'fixedLongitude',
              'cabangRadius',
              'cabangName',
              'cabangAlamat',
              'absenRadiusActive'
          ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $timezone = new \DateTimeZone('Asia/Makassar');
            $todayDate = Carbon::today($timezone)->toDateString();
            $currentTime = Carbon::now($timezone)->toTimeString();

            // Prioritaskan jadwal hari ini yang memiliki shift valid
            $jadwalQuery = JadwalKerja::withoutGlobalScope('cabang')
                ->with('Shift', 'User')
                ->where('id', Auth::user()->id)
                ->whereDate('tanggal', $todayDate)
                ->whereIn('status', ['X', 'T'])
                ->whereHas('Shift');

            if ($request->filled('jadwal_id')) {
                $jadwal = (clone $jadwalQuery)->where('jadwal_id', $request->jadwal_id)->first();
            } else {
                // Prioritas: yang sudah absen masuk (untuk absen pulang), lalu yang belum absen masuk
                $jadwal = (clone $jadwalQuery)->whereNotNull('jam_masuk')->whereNull('jam_keluar')->first()
                    ?: (clone $jadwalQuery)->whereNull('jam_masuk')->first()
                    ?: $jadwalQuery->first();
            }

            if (!$jadwal) {
                Alert::warning('Warning', 'Absensi Gagal, Tidak Terdapat Jadwal Hari Ini!');
                return redirect()->back();
            }

            $shift = MasterShift::withoutGlobalScope('cabang')->where('shift_id', $jadwal->shift_id)->first();
            if (!$shift || empty($shift->shift_in) || empty($shift->shift_out)) {
                Alert::warning('Warning', 'Absensi Gagal, Shift Kerja tidak valid atau tidak ditemukan!');
                return redirect()->back();
            }

            DB::beginTransaction();

            $shiftIn = $shift->shift_in;
            $shiftOut = $shift->shift_out;

            $shiftInTime = Carbon::createFromFormat('H:i:s', $shiftIn, $timezone);
            $shiftOutTime = Carbon::createFromFormat('H:i:s', $shiftOut, $timezone);
            $currentTimeObj = Carbon::createFromFormat('H:i:s', $currentTime, $timezone);
            // dd([
            //     'Shift In Time' => $shiftInTime->toTimeString(),
            //     'Shift Out Time' => $shiftOutTime->toTimeString(),
            //     'Current Time' => $currentTimeObj->toTimeString(),
            //     'Flexible Shift In Start' => $currentTimeObj->lessThanOrEqualTo($shiftInTime->copy()->addMinutes(15)),
            //     'Flexible Shift In End' => $currentTimeObj->lessThanOrEqualTo($shiftInTime),
            // ]);
            if ($jadwal->jam_masuk == '') {
                $absen = ($currentTimeObj->lessThanOrEqualTo($shiftInTime->copy()->addMinutes(15))) ? "Absen" : "Terlambat";
                $jadwal->jam_masuk = $currentTime;
                $jadwal->status_absen_in = $absen;
            } else {
                $absen = ($currentTimeObj->lessThan($shiftOutTime)) ? "Pulang Cepat" : "Absen";
                $jadwal->jam_keluar = $currentTime;
                $jadwal->status_absen_out = $absen;
            }

            $jadwal->update();

            if ($jadwal->jam_masuk != null && $jadwal->jam_keluar != null) {
                $jadwal->status = 'Y';
                $jadwal->update();
            }

            DB::commit();

            if ($jadwal->jam_keluar == null) {
                $message = ($absen === "Terlambat") ?
                    "Sukses Absen, Anda Terlambat {$currentTime}" :
                    "Sukses Absen, {$currentTime}";
            } else {
                $message = ($absen === "Pulang Cepat") ?
                    "Sukses Absen, Anda Pulang Lebih Cepat {$currentTime}" :
                    "Sukses Absen, {$currentTime}";
            }

            Alert::success('Success', $message);
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $jadwal = JadwalKerja::withoutGlobalScope('cabang')->where('jadwal_id', $request->jadwalId)->first();
            if(!$jadwal){
                Alert::warning('Warning', 'Tukar Jadwal Gagal, Internal Server Error!');
                return redirect()->back();
            }

            $jadwal->keterangan = $request->keterangan;
            $jadwal->status = 'T';
            $jadwal->update();
            DB::commit();
            Alert::success('Success', 'Penukaran Masih Di Periksa oleh Owner');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
