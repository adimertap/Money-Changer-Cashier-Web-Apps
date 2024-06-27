<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanAbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = JadwalKerja::query();
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

        if ($request->query->keys()) {
            $id = $request->query->keys()[0];
            $user = User::find($id);
            $displayText = $user ? $user->name : 'User not found';
        } else {
            $id = Auth::user()->id;
            $displayText = Auth::user()->name;
        }

        // Use the determined ID in the query
        // $jadwal = $query->with('Shift', 'User')->where('id', $id)->get()->sortBy('tanggal');
        $jadwal = $query->with('Shift', 'User')->where('id', $id)->orderBy('tanggal')->paginate(10);

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

        return view('absensi.report', compact('jadwal', 'selectedMonth', 'selectedYear', 'selectedStatus', 'displayText'));
    }

    public function getUser(){
        $user = User::get();
        return view('absensi.reportAll', compact('user'));
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
