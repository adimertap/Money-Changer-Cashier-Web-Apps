<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ModalTransaksi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ApprovalModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modal = ModalTransaksi::where('status_modal','Pending')->get();

        return view('pages.modal.approval', compact('modal'));
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
        $modal = ModalTransaksi::where('id_modal', $request->modal_id)->first();
        $modal->status_modal = $request->status_modal;
        if($request->status_modal == "Terima"){
                $penamabahan_modal = $modal->jumlah_modal + $modal->pengajuan_tambah;
                $penambahan_riwayat = $modal->riwayat_modal + $modal->pengajuan_tambah;
                $modal->jumlah_modal = $penamabahan_modal;
                $modal->riwayat_modal = $penambahan_riwayat;
        }
        $modal->keterangan_approval = $request->keterangan_approval;
        $modal->update();
        if($request->status_modal == 'Terima'){
            Alert::success('Success Title', 'Data Modal Berhasil Diapprove');
            return redirect()->back();
        }else{
            Alert::success('Success Title', 'Data Modal Ditolak dan Diberi Keterangan');
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
