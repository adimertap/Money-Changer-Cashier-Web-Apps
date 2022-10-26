<?php

namespace App\Http\Controllers;

use App\Models\CurrencyDetail;
use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CurrencyDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kurs = MasterCurrency::orderBy('jenis_kurs','DESC')->get();
        $kurs_detail = CurrencyDetail::with('Kurs')->get();

        return view('pages.detailcurrency.index',compact('kurs','kurs_detail'));

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
        $det = new CurrencyDetail;
        $det->id_currency = $request->id_currency;
        $det->nilai_baru = $request->nilai_baru;
        $det->keterangan = $request->keterangan;
        $det->save();

        Alert::success('Berhasil', 'Data Detail Currency Berhasil Ditambahkan');
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
        $det = CurrencyDetail::find($request->detail_id);
        $det->nilai_baru = $request->nilai_baru;
        $det->keterangan = $request->keterangan;
        $det->update();

        Alert::success('Berhasil', 'Data Detail Currency Berhasil Ditambahkan');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_detail_kurs)
    {
        $item = CurrencyDetail::find($id_detail_kurs);
        $item->delete();
        
        Alert::success('Berhasil', 'Data Currency Berhasil Terhapus');
        return redirect()->back();
    }
}
