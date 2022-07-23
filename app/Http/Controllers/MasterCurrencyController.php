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
        Alert::success('Success Title', 'Data Currency Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function hapus(Request $request)
    {
        $item = MasterCurrency::find($request->currency_delete_id);
        $item->delete();
        
        Alert::success('Success Title', 'Data Currency Berhasil Terhapus');
        return redirect()->route('master-currency');
    }

    public function update(Request $request)
    {
        # code...
    }
}
