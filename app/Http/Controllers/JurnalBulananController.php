<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalBulananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $transaksi = Transaksi::selectRaw('SUM(total) as grand_total, tanggal_transaksi, DATE_FORMAT(tanggal_transaksi, "%m") as month, YEAR(tanggal_transaksi) as year, COUNT(id_transaksi) as jumlah_transaksi')
            ->groupBy('month','year')
            ->orderBy('month','ASC')
            ->get();
    
            return view('pages.jurnal.bulan.index', compact('transaksi'));
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
       
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
        try {
            $transaksi = Transaksi::whereMonth('tanggal_transaksi', '=', $month)
            ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi')
            ->groupBy('tanggal_transaksi')
            ->orderBy('tanggal_transaksi','DESC')
            ->get();
    
            $transaksi_seluruh = Transaksi::with('Pegawai')->whereMonth('tanggal_transaksi', '=', $month);
            if($request->from){
                $transaksi_seluruh->where('tanggal_transaksi', '>=', $request->from);
            }
            if($request->to){
                $transaksi_seluruh->where('tanggal_transaksi', '<=', $request->to);
            }
            $transaksi_seluruh = $transaksi_seluruh->orderBy('tanggal_transaksi','DESC')->get();
            $bulan = $month;
    
            return view('pages.jurnal.bulan.detail', compact('transaksi','transaksi_seluruh','bulan'));
    
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
     

        
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
