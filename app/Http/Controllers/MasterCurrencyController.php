<?php

namespace App\Http\Controllers;

use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCurrencyController extends Controller
{
    /*
     * Branch-aware currency handling is intentionally disabled for now.
     * Currency is a global master and applies to every branch.
     * The existing cabang_id column and branch rules remain in the repository
     * history for a future branch-specific currency decision.
     */
    // use App\Models\MasterCabang;
    // private function allowedCabangIds() { ... }
    // private function resolveCabangId($requestedCabangId = null, $required = true) { ... }
    // private function currencyQuery($cabangId = null) { ... }

    private function currencyQuery()
    {
        return MasterCurrency::withoutGlobalScope('cabang');
    }

    public function index(Request $request)
    {
        // cabang_id filtering is intentionally disabled; all currency applies to every branch.
        $currency = $this->currencyQuery()
            ->orderBy('jenis_kurs', 'ASC')
            ->get();
        $currency_edit = $this->currencyQuery()
            ->orderBy('urutan', 'DESC')
            ->get();
        $lembar = $this->currencyQuery()->where('jenis_kurs', 'Lembar')->count();
        $coins = $this->currencyQuery()->where('jenis_kurs', 'Coins')->count();

        return view('pages.mastercurrency.index', compact(
            'currency',
            'lembar',
            'coins',
            'currency_edit'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_currency' => 'required|string|max:100',
            'country' => 'required|string|max:150',
            'nilai_kurs' => 'required|numeric|min:0',
            'jenis_kurs' => 'required|in:Lembar,Coins',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1',
            'img_flag' => 'required|image|max:2048',
        ]);

        $check = $this->currencyQuery()
            ->where('nama_currency', $data['nama_currency'])
            ->where('jenis_kurs', $data['jenis_kurs'])
            ->first();
        if ($check) {
            Alert::warning('Gagal', 'Data Currency tersebut sudah ada.');
            return redirect()->back()->withInput();
        }

        $item = new MasterCurrency;
        $item->fill($data);
        // $item->cabang_id = ...; // disabled: currency is global for all branches.
        $item->img_flag = CloudinaryStorage::upload(
            $request->file('img_flag')->getRealPath(),
            $request->file('img_flag')->getClientOriginalName()
        );
        $item->save();

        Alert::success('Berhasil', 'Data Currency Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function hapus(Request $request)
    {
        $item = $this->currencyQuery()->findOrFail($request->currency_delete_id);
        CloudinaryStorage::delete($item->img_flag);
        $item->delete();

        Alert::success('Berhasil', 'Data Currency Berhasil Terhapus');
        return redirect()->back();
    }

    public function updatedata(Request $request)
    {
        $item = $this->currencyQuery()->findOrFail($request->edit_currency_id);
        if ($request->hasFile('img_flag')) {
            $image = $request->file('img_flag');
            $result = CloudinaryStorage::upload($image->getRealPath(), $image->getClientOriginalName());
            $this->currencyQuery()
                ->where('nama_currency', $item->nama_currency)
                ->where('jenis_kurs', $item->jenis_kurs)
                ->update(['img_flag' => $result]);
        }
        Alert::success('Berhasil', 'Data Currency Berhasil Diedit');
        return redirect()->back();
    }

    public function updatekurs(Request $request)
    {
        $item = $this->currencyQuery()->findOrFail($request->id);
        $data = [];

        if ($request->has('nama')) {
            $data['nama_currency'] = $request->input('nama');
        }
        if ($request->has('country')) {
            $data['country'] = $request->input('country');
        }
        if ($request->has('jenis')) {
            $data['jenis_kurs'] = $request->input('jenis');
        }
        foreach (['nilai_kurs', 'jumlah_valas', 'last_nilai_jual', 'urutan', 'keterangan'] as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($data) {
            $this->currencyQuery()
                ->where('nama_currency', $item->nama_currency)
                ->where('jenis_kurs', $item->jenis_kurs)
                ->update($data);
        }

        return $request;
    }
}
