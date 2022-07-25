<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\MasterCurrency;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'Pegawai'){
            $transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->get();
            $count = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
            
            return view('pages.transaksi.index', compact('transaksi','count'));
        }else{
            $transaksi = Transaksi::with('Pegawai')->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->get();
            $count = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
            return view('pages.transaksi.owner', compact('transaksi', 'count'));
        }

        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currency = MasterCurrency::get();
        $tes = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        if($tes->status_modal == 'Pending'){
            Alert::warning('Modal Diproses, Mohon Menunggu', 'Pengajuan Modal Anda Hari Ini Belum Diproses oleh Owner');
            return redirect()->route('modal.index');
        }elseif($tes->status_modal == 'Tolak'){
            Alert::warning('Modal Ditolak', 'Pengajuan Modal Anda Hari Ini Ditolak oleh Owner, Edit Data Modal');
            return redirect()->route('modal.index');
        }else{
            $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        }
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');

        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');

        $id = Transaksi::getId();
        foreach($id as $value);
        $idlama = $value->id_transaksi;
        $idbaru = $idlama + 1;
        $blt = date('ymd');
        $id = Auth::user()->id;

        $kode_transaksi = 'RV'.$blt.$id.'-'.$idbaru;

        return view('pages.transaksi.create', compact('currency','modal','today','kode_transaksi','today_format','idbaru','jumlah_transaksi','total_transaksi'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transaksi = new Transaksi();
        $transaksi->kode_transaksi = $request->kode_transaksi;
        $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
        $transaksi->id_modal = $request->id_modal;
        $transaksi->total = $request->total;
        $transaksi->id_pegawai = Auth::user()->id;
        $transaksi->save();

        $modal = ModalTransaksi::find($request->id_modal);
        $perhitungan = $modal->riwayat_modal - $request->total;
        $modal->riwayat_modal = $perhitungan;
        $modal->save();

        $transaksi->detailTransaksi()->insert($request->detail);
        Alert::success('Berhasil', 'Data Transaksi Berhasil Ditambahkan');
        return $request;
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
        $transaksi = Transaksi::with('detailTransaksi.Currency')->find($id);
       

        $currency = MasterCurrency::get();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');

        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');

        return view('pages.transaksi.edit', compact('transaksi','currency','modal','today','today_format','jumlah_transaksi','total_transaksi'));
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
        $transaksi = Transaksi::find($id);
        $transaksi->total = $request->total;
        $transaksi->id_pegawai = Auth::user()->id;
        $transaksi->save();

        $modal = ModalTransaksi::find($request->id_modal);
        $modal->riwayat_modal = $request->jumlah_modal;
        $modal->save();

        $transaksi->detailTransaksi()->delete();
        $transaksi->detailTransaksi()->insert($request->detail);
        Alert::success('Berhasil', 'Data Transaksi Berhasil Diedit');
        return $request;
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

    public function hapus(Request $request)
    {
        $transaksi = Transaksi::find($request->transaksi_id);

        $modal = ModalTransaksi::where('id_modal', $transaksi->id_modal)->first();
        $perhitungan = $modal->riwayat_modal + $transaksi->total;
        $modal->riwayat_modal = $perhitungan;
        $modal->save();

        $transaksi->delete();
      
        Alert::success('Berhasil', 'Data Transaksi Berhasil Terhapus');
        return redirect()->back();

    }
}
