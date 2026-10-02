<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCabangController extends Controller
{
    private function rules()
    {
        return [
            'cabang_name' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function index()
    {
        $cabang = MasterCabang::orderBy('cabang_name')->get();
        return view('pages.mastercabang.index', compact('cabang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['is_active'] = $data['is_active'] ?? 1;
        MasterCabang::create($data);
        Alert::success('Berhasil', 'Data Cabang Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function show($id)
    {
        return response()->json(MasterCabang::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        MasterCabang::findOrFail($id)->update($request->validate($this->rules()));
        Alert::success('Berhasil', 'Data Cabang Berhasil Diedit');
        return redirect()->back();
    }

    public function status(Request $request, $id)
    {
        $data = $request->validate(['is_active' => 'required|boolean']);
        $item = MasterCabang::findOrFail($id);
        $item->update($data);
        return response()->json(['is_active' => (int) $item->is_active]);
    }

    public function destroy($id)
    {
        try {
            MasterCabang::findOrFail($id)->delete();
        } catch (QueryException $e) {
            // FK: cabang masih dipakai transaksi/pegawai/dll
            Alert::warning('Gagal', 'Cabang masih digunakan, nonaktifkan saja');
            return redirect()->back();
        }
        Alert::success('Berhasil', 'Data Cabang Berhasil Dihapus');
        return redirect()->back();
    }
}
