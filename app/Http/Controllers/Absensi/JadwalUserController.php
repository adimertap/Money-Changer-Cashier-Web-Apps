<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Alert;
use App\Models\JadwalKerja;
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

          $startOfMonth = Carbon::now($timezone)->startOfMonth();
          $endOfMonth = Carbon::now($timezone)->endOfMonth();

          $jadwal = JadwalKerja::with('Shift', 'User')->where('id', Auth::user()->id)
              ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
              ->get()
              ->sortBy('tanggal');

          $jadwalToday = JadwalKerja::with('Shift', 'User')->where('id', Auth::user()->id)
              ->where('tanggal', Carbon::today($timezone))
              ->get();

          $countTodayStatusX = JadwalKerja::where('id', Auth::user()->id)
          ->where('tanggal', Carbon::today($timezone))
          ->whereIn('status', ['X', 'T'])
          ->count();

          $today = Carbon::today($timezone);
          $jadwalTodayCount = $jadwalToday->count();
          $currentMonth = Carbon::now($timezone)->format('F');

          // Button Absen Setelah 4 Jam Masuk
          $jadwalMasukFilled = JadwalKerja::where('id', Auth::user()->id)
          ->where('tanggal', Carbon::today($timezone))
          ->where('jam_masuk','!=', null)
          ->where('status', 'X')
          ->first();

         // $shiftActual = MasterShift::where('shift_id', $jadwalMasukFilled->shift_id)->value('shift_in');
         // $shiftCheck = Carbon::createFromFormat('H:i:s', $jadwalMasukFilled->jam_masuk, $timezone);
         // dd([
         //         'Actual' => $shiftActual,
         //         '+ 2 Jam' => $shiftCheck->toTimeString()
         //     ]);


          // Radius Salah
          //  -8.035976527872002, 114.38504957190355

          // Radius Basurra
          // -6.200000, 106.816666

          // Radius PT Riastavalasindo on googlemaps
          //  -8.701647497474847, 115.16637512084526

          $fixedLatitude = -6.200000; // Example fixed latitude
          $fixedLongitude =  106.816666; // Example fixed longitude

          return view('absensi.absen', compact('jadwalMasukFilled','countTodayStatusX','jadwal', 'jadwalToday', 'currentMonth', 'jadwalTodayCount', 'today', 'fixedLatitude', 'fixedLongitude'));
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
            $currentTime = Carbon::now($timezone)->toTimeString();
            $jadwal = JadwalKerja::with('Shift', 'User')
                ->where('id', Auth::user()->id)
                ->where('tanggal', Carbon::today($timezone))
                ->whereIn('status', ['X', 'T'])
                ->first();

            if (!$jadwal) {
                Alert::warning('Warning', 'Absensi Gagal, Tidak Terdapat Jadwal Hari Ini!');
                return redirect()->back();
            }

            DB::beginTransaction();

            $shiftIn = MasterShift::where('shift_id', $jadwal->shift_id)->value('shift_in');
            $shiftOut = MasterShift::where('shift_id', $jadwal->shift_id)->value('shift_out');

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
            $jadwal = JadwalKerja::where('jadwal_Id', $request->jadwalId)->first();
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
