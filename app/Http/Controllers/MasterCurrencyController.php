<?php

namespace App\Http\Controllers;

use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCurrencyController extends Controller
{
    public function index()
    {
        $currency = MasterCurrency::orderBy('jenis_kurs','ASC')->get();
        $currency_edit = MasterCurrency::orderBy('urutan','DESC')->get();
        $lembar = MasterCurrency::where('jenis_kurs','Lembar')->count();
        $coins = MasterCurrency::where('jenis_kurs','Coins')->count();

        return view('pages.mastercurrency.index', compact('currency','lembar','coins','currency_edit'));
    }

    public function store(Request $request)
    {
        $check = MasterCurrency::where('nama_currency', $request->nama_currency)->where('jenis_kurs', $request->jenis_kurs)->first();
        if(!$check){
            $item = new MasterCurrency;
            $item->nama_currency = $request->nama_currency;
            $item->country = $request->country;
            $item->nilai_kurs = $request->nilai_kurs;
            $item->jenis_kurs = $request->jenis_kurs;
            $item->keterangan = $request->keterangan;
            $item->urutan = $request->urutan;
    
            // STORE IMAGE ONLINE
            $image  = $request->file('img_flag');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName()); 
            $item->img_flag = $result;
            $item->save();
    
            Alert::success('Berhasil', 'Data Currency Berhasil Ditambahkan');
            return redirect()->back();
        }else{
            Alert::warning('Gagal', 'Data Currency Tersebut Telah Ada');
            return redirect()->back();
        }
    }

    public function hapus(Request $request)
    {
        $item = MasterCurrency::find($request->currency_delete_id);
        CloudinaryStorage::delete($item->img_flag);
        $item->delete();
        
        Alert::success('Berhasil', 'Data Currency Berhasil Terhapus');
        return redirect()->back();
    }

    public function updatedata(Request $request)
    {
        $item = MasterCurrency::find($request->edit_currency_id);
        if($request->img_flag){
            $image  = $request->file('img_flag');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName()); 
            $item->img_flag = $result;
        }
        $item->update();
        Alert::success('Berhasil', 'Data Currency Berhasil Diedit');
        return redirect()->back();
    }

    public function updatekurs(Request $request)
    {
        $item = MasterCurrency::find($request->id);
        if($request->nilai_kurs){
            $item->nilai_kurs = $request->nilai_kurs;
        }
        if($request->keterangan){
            $item->keterangan = $request->keterangan;
        }
        if($request->urutan){
            $item->urutan = $request->urutan;
        }
        if($request->nama){
            $item->nama_currency = $request->nama;
        }
        if($request->country){
            $item->country = $request->country;
        }
        if($request->jenis){
            $item->jenis_kurs = $request->jenis;
        }
        $item->save();
        return $request;
    }
}
