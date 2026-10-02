<?php

namespace App\Http\Controllers;

use App\Models\MasterThreshold;
use App\Models\MasterCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MasterThresholdController extends Controller
{
    private function rules()
    {
        return [
            'bulan' => 'required|string|max:20|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'tahun' => 'required|digits:4|integer|min:1900|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'currency_id' => 'required|integer|exists:tb_currency,id_currency',
            'nominal' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ];
    }

    public function index()
    {
        $currencies = MasterCurrency::orderBy('urutan')->orderBy('nama_currency')->get();
        $thresholds = MasterThreshold::orderByDesc('tahun')
            ->orderByRaw("FIELD(bulan, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')")
            ->get();

        return view('pages.masterthreshold.index', compact('thresholds', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['created_by'] = Auth::id();
        MasterThreshold::create($data);

        Alert::success('Berhasil', 'Preset batas atas transaksi berhasil ditambahkan');
        return redirect()->back();
    }

    public function show($id)
    {
        return response()->json(MasterThreshold::findOrFail($id));
    }

    public function status(Request $request, $id)
    {
        $data = $request->validate(['is_active' => 'required|boolean']);
        $threshold = MasterThreshold::findOrFail($id);
        $threshold->update(array_merge($data, ['updated_by' => Auth::id()]));

        return response()->json(['is_active' => (int) $threshold->is_active]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules());
        $data['updated_by'] = Auth::id();
        MasterThreshold::findOrFail($id)->update($data);

        Alert::success('Berhasil', 'Preset batas atas transaksi berhasil diperbarui');
        return redirect()->back();
    }

    public function destroy($id)
    {
        MasterThreshold::findOrFail($id)->delete();

        Alert::success('Berhasil', 'Preset batas atas transaksi berhasil dihapus');
        return redirect()->back();
    }
}
