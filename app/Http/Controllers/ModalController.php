<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MailModal;
use App\Mail\MailModalTambah;
use App\Mail\MailTransfer;
use App\Models\Jurnal;
use App\Models\ModalTransaksi;
use App\Models\RiwayatModal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;


class ModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'Owner'){
            $modal = ModalTransaksi::orderBy('created_at', 'DESC')->get();
        }else{
            $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->orWhere('riwayat_modal','>', '0')->get();
        }
        $modal_today = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->get();
        $modal_tf = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        $jumlah_modal_today = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        $today = Carbon::now()->format('Y-m-d');
        if(count($modal_today) == 0){
            Alert::warning('Modal Belum Diinput', 'Anda Belum Menginputkan Modal Hari Ini, Lakukan Inputan atau Transfer');
        }
        return view('pages.modal.index', compact('modal','modal_today','today','jumlah_modal_today','modal_tf'));
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
        $modal = new ModalTransaksi();
        $modal->tanggal_modal = Carbon::now();
        $modal->jumlah_modal = $request->jumlah_modal;
        $modal->status_modal = 'Pending';
        $modal->id_pegawai = Auth::user()->id;
        $modal->riwayat_modal = $request->jumlah_modal;
        $modal->jenis_modal = 'MOdal Awal';
        $modal->save();

        // $user = User::where('role','Owner')->get();
        // foreach ($user as $tes) {
        //     Mail::to($tes->email)->send(new MailModal($modal));
        // }

       

        Alert::success('Berhasil', 'Data Modal Berhasil Ditambahkan');
        return redirect()->back();
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
        $modal = ModalTransaksi::where('id_modal', $request->modal_edit_id)->first();
        $modal->jumlah_modal = $request->jumlah_modal;
        $modal->riwayat_modal = $request->jumlah_modal;
        $modal->status_modal = 'Pending';
        $modal->jenis_modal = 'Edit Modal';
        $modal->update();

        Alert::success('Berhasil', 'Data Modal Berhasil Diedit');
        return redirect()->back();
    }

    public function tambah(Request $request)
    {
        $item = ModalTransaksi::find($request->ajukan_modal_id);
        $item->pengajuan_tambah = $request->jumlah_modal;
        $item->status_modal = 'Pending';
        $item->jenis_modal = 'Penambahan Modal';
        $item->save();

        // $user = User::where('role','Owner')->get();
        // foreach ($user as $tes) {
        //     Mail::to($tes->email)->send(new MailModalTambah($item));
        // }

        Alert::success('Berhasil', 'Data Modal Berhasil Diajukan, Mohon Tunggu');
        return redirect()->back();
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function hapus(Request $request)
    {
        $modal = ModalTransaksi::find($request->modal_delete_id);
       

        $jurnal = Jurnal::where('id_modal', $request->modal_delete_id);
        $jurnal->delete();
        $modal->delete();

        Alert::success('Berhasil', 'Data Modal Berhasil Terhapus');
        return redirect()->back();
    }

    public function transfer(Request $request)
    {
        $modal = ModalTransaksi::find($request->modal_transfer_id);
        $modal_tuju = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        if(empty($modal_tuju)){
            $modal_baru = new ModalTransaksi();
            $modal_baru->tanggal_modal = Carbon::now();
            $modal_baru->jumlah_modal = $modal->riwayat_modal;
            $modal_baru->status_modal = 'Pending';
            $modal_baru->id_pegawai = Auth::user()->id;
            $modal_baru->riwayat_modal = $modal->riwayat_modal;
            $modal_baru->jenis_modal = 'Transfer Modal';
            $modal_baru->save();
        }else{
            $perhitungan = $modal->riwayat_modal + $modal_tuju->riwayat_modal;
            $modal_tuju->jenis_modal = 'Transfer Modal';
            $modal_tuju->riwayat_modal = $perhitungan;
            $modal_tuju->save();
        }
        $modal->jenis_modal = 'Transfer Modal';
        $modal->riwayat_modal = 0;
        $modal->save();

        // $user = User::where('role','Owner')->get();
        // foreach ($user as $tes) {
        //     Mail::to($tes->email)->send(new MailTransfer($modal));
        // }

       

      
        Alert::success('Berhasil', 'Data Transfer Modal Berhasil Diajukan, Mohon Menunggu Approval');
        return redirect()->back();

    }
}
