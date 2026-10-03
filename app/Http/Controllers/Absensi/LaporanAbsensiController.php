<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\MasterCabang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanAbsensiController extends Controller
{
    private function allowedCabangIds()
    {
        if (Auth::user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->map(function ($id) {
                return (int) $id;
            })->all();
        }

        return session('cabang_aktif') ? [(int) session('cabang_aktif')] : [];
    }

    private function resolveCabangId(Request $request, $required = false)
    {
        $cabangId = $request->input('cabang_id');
        if ($cabangId === null || $cabangId === '') {
            if ($required && Auth::user()->role !== 'Owner') {
                return session('cabang_aktif');
            }
            return null;
        }

        abort_unless(in_array((int) $cabangId, $this->allowedCabangIds(), true), 403, 'Cabang tidak valid.');
        return (int) $cabangId;
    }

    private function cabangs()
    {
        return MasterCabang::whereIn('cabang_id', $this->allowedCabangIds())
            ->where('is_active', 1)
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate(['cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id']);
        $cabangId = $this->resolveCabangId($request);
        $query = JadwalKerja::query();
        if ($cabangId) {
            $query->where('tb_jadwal_kerja.cabang_id', $cabangId);
        }

        if ($request->has('statusFilter') && !empty($request->statusFilter)) {
            if ($request->statusFilter == 'Terlambat') {
                $query->where('status_absen_in', "Terlambat");
            } else if ($request->statusFilter == 'Pulang Lebih Cepat') {
                $query->where('status_absen_out', "Pulang Cepat");
            } else if ($request->statusFilter == 'InComplete') {
                $query->where('status', "X");
            }
        }

        if ($request->has('monthFilter') && !empty($request->monthFilter)) {
            $query->whereMonth('tanggal', $request->monthFilter);
        }

        if ($request->has('yearFilter') && !empty($request->yearFilter)) {
            $query->whereYear('tanggal', $request->yearFilter);
        }

        $userid = $request->query('userid');
        if ($userid) {
            $user = User::find($userid);
            $displayText = $user ? $user->name : 'User not found';
        } else {
            $userid = Auth::user()->id;
            $displayText = Auth::user()->name;
        }

        // return $userid;

        $jadwal = $query->with('Shift', 'User')->where('id', $userid)->orderBy('tanggal', 'DESC')->get();

        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $selectedMonth = $request->has('monthFilter') && !empty($request->monthFilter)
            ? $monthNames[$request->monthFilter]
            : null;

        $selectedYear = $request->has('yearFilter') && !empty($request->yearFilter)
            ? $request->yearFilter
            : null;

        $selectedStatus = $request->has('statusFilter') && !empty($request->statusFilter)
            ? $request->statusFilter
            : null;

        $queryParams = http_build_query([
            'userid' => $userid,
            'statusFilter' => $selectedStatus,
            'monthFilter' => $selectedMonth,
            'yearFilter' => $selectedYear,
            'cabang_id' => $cabangId,
        ]);

        $url = url('/jadwal-laporan?' . $queryParams);

        $cabangs = $this->cabangs();
        return view('absensi.report', compact('jadwal', 'selectedMonth', 'selectedYear', 'selectedStatus', 'displayText', 'url', 'cabangId', 'cabangs'));
    }


    public function today(Request $request)
    {
        try {
            $request->validate(['cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id']);
            $cabangId = $this->resolveCabangId($request);
            $today = Carbon::now()->format('Y-m-d');
            $jadwal = JadwalKerja::where('tanggal', $today)
                ->when($cabangId, function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                })->get();
            $cabangs = $this->cabangs();
            return view('absensi.reportToday', compact('jadwal','today','cabangs','cabangId'));
        } catch (\Throwable $th) {
            return $th;
        }

    }

    public function getUser(Request $request){
        $request->validate(['cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id']);
        $cabangId = $this->resolveCabangId($request);
        $user = User::query()
            ->when($cabangId, function ($query) use ($cabangId) {
                $query->whereHas('cabangs', function ($query) use ($cabangId) {
                    $query->where('tb_master_cabang.cabang_id', $cabangId);
                });
            })
            ->get();
        $cabangs = $this->cabangs();
        return view('absensi.reportAll', compact('user', 'cabangs', 'cabangId'));
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
        //
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
        //
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
