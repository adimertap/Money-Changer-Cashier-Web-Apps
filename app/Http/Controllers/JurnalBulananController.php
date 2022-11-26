<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalBulananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaksi = Transaksi::selectRaw('SUM(total) as grand_total, DATE_FORMAT(tanggal_transaksi, "%M") as month, YEAR(tanggal_transaksi) as year, COUNT(id_transaksi) as jumlah_transaksi')
        ->groupBy('month','year')
        ->orderBy('month','DESC')
        ->get();

        // return $transaksi;

        return view('pages.jurnal.bulan.index', compact('transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
    public function show(Request $request,$month)
    {
        $transaksi = Transaksi::where(DB::raw("(DATE_FORMAT(tanggal_transaksi,'%M'))"),'=', $month)
        ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi')
        ->groupBy('tanggal_transaksi')
        ->get();

        $transaksi_seluruh = Transaksi::with('Pegawai')->where(DB::raw("(DATE_FORMAT(tanggal_transaksi,'%M'))"),'=', $month);
        if($request->from){
            $transaksi_seluruh->where('tanggal_transaksi', '>=', $request->from);
        }
        if($request->to){
            $transaksi_seluruh->where('tanggal_transaksi', '<=', $request->to);
        }
        $transaksi_seluruh = $transaksi_seluruh->get();

        return view('pages.jurnal.bulan.detail', compact('transaksi','transaksi_seluruh'));

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($tanggal_transaksi)
    {
        $transaksi = Transaksi::where('tanggal_transaksi', $tanggal_transaksi)->get();
        return view('pages.jurnal.bulan.detailtanggal', compact('transaksi'));
    }
    
    public function DetailTransaksi($id)
    {
        $transaksi = Transaksi::with('Pegawai','detailTransaksi.Currency')->find($id);
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        return view('pages.jurnal.bulan.detailtransaksi', compact('transaksi','detail'));
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
