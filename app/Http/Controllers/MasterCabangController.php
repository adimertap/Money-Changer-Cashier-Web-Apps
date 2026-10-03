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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'nullable|integer|min:1',
            'absen_radius_active' => 'nullable|boolean',
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
        $data['absen_radius_active'] = isset($data['absen_radius_active']) ? (int) $data['absen_radius_active'] : 1;

        // Sinkronisasi latitude & longitude dengan lat & lng
        if (isset($data['latitude']) && !isset($data['lat'])) {
            $data['lat'] = $data['latitude'];
        } elseif (isset($data['lat']) && !isset($data['latitude'])) {
            $data['latitude'] = $data['lat'];
        }
        if (isset($data['longitude']) && !isset($data['lng'])) {
            $data['lng'] = $data['longitude'];
        } elseif (isset($data['lng']) && !isset($data['longitude'])) {
            $data['longitude'] = $data['lng'];
        }
        $data['radius'] = $data['radius'] ?? 50;

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
        $data = $request->validate($this->rules());

        if (array_key_exists('absen_radius_active', $data)) {
            $data['absen_radius_active'] = (int) $data['absen_radius_active'];
        }

        if (array_key_exists('latitude', $data) && !array_key_exists('lat', $data)) {
            $data['lat'] = $data['latitude'];
        } elseif (array_key_exists('lat', $data) && !array_key_exists('latitude', $data)) {
            $data['latitude'] = $data['lat'];
        }
        if (array_key_exists('longitude', $data) && !array_key_exists('lng', $data)) {
            $data['lng'] = $data['longitude'];
        } elseif (array_key_exists('lng', $data) && !array_key_exists('longitude', $data)) {
            $data['longitude'] = $data['lng'];
        }

        MasterCabang::findOrFail($id)->update($data);
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
