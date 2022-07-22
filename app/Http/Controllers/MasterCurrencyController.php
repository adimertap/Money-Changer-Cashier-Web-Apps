<?php

namespace App\Http\Controllers;

use App\Models\MasterCurrency;
use Illuminate\Http\Request;

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

        return redirect()->back()->with('messageberhasil','Data Currency Berhasil Ditambahkan');
    }

    public function hapus(Request $request)
    {
        $item = MasterCurrency::find($request->currency_delete_id);
        $item->delete();

        return redirect()->route('master-currency')->with('messageberhasil','Data Currency Berhasil Dihapus');
    }

    public function update(Type $var = null)
    {
        # code...
    }
}
