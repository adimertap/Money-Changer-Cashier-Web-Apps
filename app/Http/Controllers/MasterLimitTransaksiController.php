<?php

namespace App\Http\Controllers;

use App\Models\MasterLimitTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MasterLimitTransaksiController extends Controller
{
    public function index()
    {
        $limit = MasterLimitTransaksi::getActiveLimit();
        return view('pages.masterlimittransaksi.index', compact('limit'));
    }

    public function update(Request $request, $id)
    {
        $limit = MasterLimitTransaksi::findOrFail($id);

        $data = $request->validate([
            'nominal_limit_idr' => 'required|numeric|min:0',
            'ekuivalen_usd' => 'required|numeric|min:0',
            'periode_hari' => 'required|integer|min:1|max:365',
            'is_active' => 'nullable|boolean',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['updated_by'] = Auth::id();

        $limit->update($data);

        Alert::success('Berhasil', 'Batas transaksi paspor berhasil diperbarui.');
        return redirect()->back();
    }
}
