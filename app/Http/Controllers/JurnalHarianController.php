<?php

namespace App\Http\Controllers;

use App\Exports\ExcelTransaksi;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\MasterCurrency;
use App\Models\DetailTransaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transaksi = Transaksi::with([
            'Pegawai',
        ]);        
        if($request->from){
            $transaksi->where('tanggal_transaksi', '>=', $request->tanggal_mulai);
        }
        if($request->to){
            $transaksi->where('tanggal_transaksi', '<=', $request->tanggal_selesai);
        }
        $transaksi = $transaksi->get();
        
        $jumlah = Transaksi::count();
        $total = Transaksi::sum('total');
        $currency = MasterCurrency::get();
        $pegawai = User::where('role','!=','Owner')->get();

        return view('pages.jurnal.harian.index', compact('pegawai','transaksi','jumlah','total','currency'));
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

    public function Export_dokumen(Request $request)
    {
        $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi','tb_transaksi.id_transaksi','tb_detail_transaksi.id_transaksi')
        ->join('tb_currency','tb_detail_transaksi.currency_id','tb_currency.id_currency');
        if($request->from_date_export){
            $transaksi->where('tanggal_transaksi', '>=', $request->from_date_export);
        }
        if($request->to_date_export){
            $transaksi->where('tanggal_transaksi', '<=', $request->to_date_export);
        }
        if($request->id_pegawai){
            $transaksi->where('id_pegawai', $request->id_pegawai);
        }
        if($request->id_currency){
            $transaksi->where('currency_id', $request->id_currency);
        }
        $transaksi = $transaksi->get();
        $total = $transaksi->sum('total');
        $jumlah = $transaksi->count();

        if(count($transaksi) == 0){
            Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
            return redirect()->back();
        }else{
            if($request->radio_input == 'pdf'){
                $pdf = Pdf::loadview('export.pdf',['transaksi'=>$transaksi, 'total' =>$total,'jumlah' => $jumlah]);
                return $pdf->download('laporan-transaksi.pdf');
                Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
            }else{
                return new ExcelTransaksi($transaksi);
            }
        }

       
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
        $transaksi = Transaksi::with('Pegawai','detailTransaksi.Currency')->find($id);
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        return view('pages.transaksi.detail', compact('transaksi','detail'));
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
