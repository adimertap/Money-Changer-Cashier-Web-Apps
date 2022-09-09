<?php

namespace App\Http\Controllers;

use App\Exports\ExcelHarian;
use App\Exports\ExcelHarianOwner;
use App\Exports\ExcelHarianView;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\MasterCurrency;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
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
            $transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->orderBy('updated_at')->get();
            $count = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
            $today =  Carbon::now()->format('Y-m-d');
            $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
            $currency = MasterCurrency::get();

            return view('pages.transaksi.index', compact('transaksi','count','today','total_transaksi','currency'));
        }else{
            $transaksi = Transaksi::with('Pegawai')->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->orderBy('updated_at')->get();
            $count = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
            $today =  Carbon::now()->format('Y-m-d');
            $total_transaksi = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
            $currency = MasterCurrency::get();
            $pegawai = User::where('role','!=','Owner')->get();

            return view('pages.transaksi.owner', compact('transaksi', 'count','today','total_transaksi','currency','pegawai'));
        }       
    }

    public function Export_dokumen(Request $request)
    {
        if(Auth::user()->role == 'Pegawai'){
            $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi','tb_transaksi.id_transaksi','tb_detail_transaksi.id_transaksi')
            ->join('tb_currency','tb_detail_transaksi.currency_id','tb_currency.id_currency')->where('id_pegawai', Auth::user()->id);
            if($request->id_currency){
                $transaksi->where('currency_id', $request->id_currency);
            }
            $transaksi = $transaksi->where('tanggal_transaksi', Carbon::today())->get();
            $total = $transaksi->sum('total');
            $jumlah = $transaksi->count();
            $today = Carbon::now()->format('d-M-Y');
            // return $transaksi;
    
            if(count($transaksi) == 0){
                Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                return redirect()->back();
            }else{
                if($request->radio_input == 'pdf'){
                    $pdf = Pdf::loadview('export.pdf-harian',['transaksi'=>$transaksi, 'total' =>$total,'jumlah' => $jumlah, 'today'=> $today]);
                    return $pdf->download('report-harian '.$today.' '.Auth::user()->name.' .pdf');
                    Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                }else{
                    return new ExcelHarian($transaksi);
                }
            }
        }else{
            $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi','tb_transaksi.id_transaksi','tb_detail_transaksi.id_transaksi')
            ->join('tb_currency','tb_detail_transaksi.currency_id','tb_currency.id_currency')
            ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->OrderBy('tb_transaksi.updated_at');
            if($request->id_currency){
                $transaksi->where('currency_id', $request->id_currency);
            }
            if($request->id_pegawai){
                $transaksi->where('id_pegawai', $request->id_pegawai);
            }
            $transaksi = $transaksi->get();
            $total = $transaksi->sum('total');
            $jumlah = $transaksi->count();
            $today = Carbon::now()->format('d-M-Y');
            
            if(count($transaksi) == 0){
                Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                return redirect()->back();
            }else{
                if($request->radio_input == 'pdf'){
                    $pdf = Pdf::loadview('export.pdf-harian-owner',['transaksi'=>$transaksi, 'total' =>$total,'jumlah' => $jumlah, 'today' => $today]);
                    if($request->id_pegawai){
                        return $pdf->download('report-harian '.$today.' '.$transaksi[0]->Pegawai->name.' .pdf');
                    }
                        return $pdf->download('report-harian '.$today.' .pdf');
                    Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                }else{
                    return new ExcelHarianOwner($transaksi);
                }
            }
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
        if(empty($tes)){
            Alert::warning('Belum Mengajukan Modal', 'Anda Belum Mengajukan Modal');
            return redirect()->route('modal.index');
        }else{
            if($tes->status_modal == 'Pending'){
                Alert::warning('Modal Diproses, Mohon Menunggu', 'Pengajuan Modal Anda Hari Ini Belum Diproses oleh Owner');
                return redirect()->route('modal.index');
            }elseif($tes->status_modal == 'Tolak'){
                Alert::warning('Modal Ditolak', 'Pengajuan Modal Anda Hari Ini Ditolak oleh Owner, Edit Data Modal');
                return redirect()->route('modal.index');
            }else{
                $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
            }
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

        $kode_transaksi = 'RV'.$blt.'-'.$idbaru;

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

        $transaksi->detailTransaksi()->insert($request->detail);
        
        foreach($request->detail as $key){
            $jurnal = new Jurnal();
            $jurnal->id_transaksi = $transaksi->id_transaksi;
            $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
            $jurnal->id_currency = $key['currency_id'];
            $jurnal->kurs = $key['jumlah_currency'];
            $jurnal->jumlah_tukar = $key['jumlah_tukar'];
            $jurnal->total_tukar = $key['total_tukar'];
            $jurnal->jenis_jurnal = 'Debit';
            $jurnal->id_pegawai = Auth::user()->id;
            $jurnal->save();
        }

        $modal = ModalTransaksi::find($request->id_modal);
        $perhitungan = $modal->riwayat_modal - $request->total;
        $modal->riwayat_modal = $perhitungan;
        $modal->save();

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
        // return $transaksi;
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

        $transaksi->detailTransaksi()->delete();
        $transaksi->detailTransaksi()->insert($request->detail);

        foreach($request->detail as $key){
            $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->first();
            $jurnal->id_transaksi = $transaksi->id_transaksi;
            $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
            $jurnal->id_currency = $key['currency_id'];
            $jurnal->kurs = $key['jumlah_currency'];
            $jurnal->jumlah_tukar = $key['jumlah_tukar'];
            $jurnal->total_tukar = $key['total_tukar'];
            $jurnal->jenis_jurnal = 'Debit';
            $jurnal->id_pegawai = Auth::user()->id;
            $jurnal->save();
        }
        $modal = ModalTransaksi::find($request->id_modal);
        $modal->riwayat_modal = $request->jumlah_modal;
        $modal->save();


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
        $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->get();
        foreach($jurnal as $tes){
            $tes->delete();
        }
        $detail = DetailTransaksi::where('id_transaksi', $transaksi->id_transaksi)->get();
        foreach($detail as $s){
            $s->delete();
        }
        $modal = ModalTransaksi::where('id_modal', $transaksi->id_modal)->first();
        $perhitungan = $modal->riwayat_modal + $transaksi->total;
        $modal->riwayat_modal = $perhitungan;
        $modal->save();
        $transaksi->delete();
      
        Alert::success('Berhasil', 'Data Transaksi Berhasil Terhapus');
        return redirect()->back();

    }
}
