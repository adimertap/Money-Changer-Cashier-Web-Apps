<?php

namespace App\Http\Controllers;

use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCurrencyController extends Controller
{
    public function index()
    {
        $currency = MasterCurrency::get();
        $jumlah = MasterCurrency::count();

        return view('pages.mastercurrency.index', compact('currency','jumlah'));
    }

    public function store(Request $request)
    {
        $item = new MasterCurrency;
        $item->nama_currency = $request->nama_currency;
        $item->country = $request->country;
        $item->save();
        Alert::success('Berhasil', 'Data Currency Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function hapus(Request $request)
    {
        $item = MasterCurrency::find($request->currency_delete_id);
        $item->delete();
        
        Alert::success('Berhasil', 'Data Currency Berhasil Terhapus');
        return redirect()->back();
    }

    public function updatedata(Request $request)
    {
        $item = MasterCurrency::find($request->edit_currency_id);
        $item->nama_currency = $request->nama_currency;
        $item->country = $request->country;
        $item->update();

        Alert::success('Berhasil', 'Data Currency Berhasil Diedit');
        return redirect()->back();
    }
}
