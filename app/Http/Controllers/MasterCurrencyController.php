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
        $lembar = MasterCurrency::where('jenis_kurs','Lembar')->count();
        $coins = MasterCurrency::where('jenis_kurs','Coins')->count();

        return view('pages.mastercurrency.index', compact('currency','lembar','coins'));
    }

    public function store(Request $request)
    {
    //    return $request;
        $item = new MasterCurrency;
        $item->nama_currency = $request->nama_currency;
        $item->country = $request->country;
        $item->nilai_kurs = $request->nilai_kurs;
        $item->jenis_kurs = $request->jenis_kurs;
        
        // STORE IMAGE LOCAL
        // if ($request->file('img_flag')) {
        //     $imagePath = $request->file('img_flag');
        //     $imageName = $imagePath->getClientOriginalName();
           
        //     $imagePath->move(public_path().'/img/', $imageName); 
        //     $data[] = $imageName;
        //   }
  
        //   $item->img_flag = $imageName;

        // STORE IMAGE ONLINE
        $image  = $request->file('img_flag');
        $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName()); 
        $item->img_flag = $result;
        $item->save();

        Alert::success('Berhasil', 'Data Currency Berhasil Ditambahkan');
        return redirect()->back();
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
        $item->nama_currency = $request->nama_currency;
        $item->country = $request->country;
        $item->nilai_kurs = $request->nilai_kurs;
        $item->jenis_kurs = $request->jenis_kurs;

        if($request->img_flag){
            $image  = $request->file('img_flag');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName()); 
            $item->img_flag = $result;
        }

        $item->update();

        Alert::success('Berhasil', 'Data Currency Berhasil Diedit');
        return redirect()->back();
    }
}
