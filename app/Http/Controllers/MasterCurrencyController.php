<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCurrencyController extends Controller
{
    private function cabangIds(): array
    {
        if (Auth::user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->all();
        }

        return array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
    }

    private function cabangs()
    {
        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $this->cabangIds())
            ->orderBy('cabang_name')
            ->get();
    }

    private function currencyQuery($cabangId = null)
    {
        return MasterCurrency::withoutGlobalScope('cabang')
            ->whereIn('cabang_id', $this->cabangIds())
            ->when($cabangId, fn ($query) => $query->where('cabang_id', $cabangId));
    }

    public function index(Request $request)
    {
        $cabangs = $this->cabangs();
        $allowedCabangIds = $this->cabangIds();
        $cabangId = $request->filled('cabang_id')
            ? (int) $request->input('cabang_id')
            : (int) (session('cabang_aktif') ?: optional($cabangs->first())->cabang_id);

        if (!$cabangId || !in_array($cabangId, $allowedCabangIds, true)) {
            $cabangId = (int) optional($cabangs->first())->cabang_id;
        }

        $currency = $this->currencyQuery($cabangId)
            ->with('Cabang')
            ->orderBy('jenis_kurs', 'ASC')
            ->get();
        $currency_edit = $this->currencyQuery($cabangId)
            ->with('Cabang')
            ->orderBy('urutan', 'DESC')
            ->get();
        $lembar = $this->currencyQuery($cabangId)->where('jenis_kurs', 'Lembar')->count();
        $coins = $this->currencyQuery($cabangId)->where('jenis_kurs', 'Coins')->count();

        return view('pages.mastercurrency.index', compact(
            'currency',
            'lembar',
            'coins',
            'currency_edit',
            'cabangs',
            'cabangId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cabang_id' => ['required', 'integer', Rule::in($this->cabangIds())],
            'nama_currency' => 'required|string|max:100',
            'country' => 'required|string|max:150',
            'nilai_kurs' => 'required|numeric|min:0',
            'jenis_kurs' => 'required|in:Lembar,Coins',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'required|integer|min:1',
            'img_flag' => 'required|image|max:2048',
        ]);

        $check = $this->currencyQuery($data['cabang_id'])
            ->where('nama_currency', $data['nama_currency'])
            ->where('jenis_kurs', $data['jenis_kurs'])
            ->first();
        if ($check) {
            Alert::warning('Gagal', 'Data Currency tersebut sudah ada pada cabang ini.');
            return redirect()->back()->withInput();
        }

        $item = new MasterCurrency;
        $item->fill($data);
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
            $item->update(['img_flag' => $result]);
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
            $item->update($data);
        }

        $item->load('Cabang');

        return response()->json([
            'nama_currency' => $item->nama_currency,
            'cabang_name' => optional($item->Cabang)->cabang_name ?: '-',
        ]);
    }
}
