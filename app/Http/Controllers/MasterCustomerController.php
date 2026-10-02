<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use App\Models\MasterCustomer;
use App\Models\MasterTerduga;
use Illuminate\Http\Request;
use ResourceBundle;
use Locale;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCustomerController extends Controller
{
    private function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:150',
            'passport' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'nik' => 'nullable|string|max:100',
            'tanggal_terdaftar' => 'nullable|date',
            'alamat' => 'nullable|string|max:150',
            'is_terduga' => 'nullable|boolean',
            'kode_densus' => 'nullable|string|max:100',
            'alias' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'cabang_terdaftar' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ];
    }

    public function index()
    {
        $customer = MasterCustomer::with('cabang')->orderBy('name')->get();
        $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get();
        $countries = collect(ResourceBundle::getLocales(''))
            ->map(function ($locale) {
                $parts = explode('_', $locale);
                return end($parts);
            })
            ->filter(fn ($code) => strlen($code) === 2 && ctype_alpha($code))
            ->unique()
            ->mapWithKeys(fn ($code) => [$code => Locale::getDisplayRegion('und_' . strtoupper($code), 'en')])
            ->filter()
            ->sort()
            ->all();

        return view('pages.mastercustomer.index', compact('customer', 'cabang', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['created_by'] = Auth::id();
        $data['is_active'] = $data['is_active'] ?? 1;
        $data['cabang_terdaftar'] = $data['cabang_terdaftar'] ?? session('cabang_aktif');
        $customer = MasterCustomer::create($data);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['customer' => $customer]);
        }

        Alert::success('Berhasil', 'Data Customer Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function show($id)
    {
        return response()->json(MasterCustomer::findOrFail($id));
    }

    public function search(Request $request)
    {
        $term = trim($request->input('q', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        return response()->json(MasterCustomer::query()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('alias', 'like', "%{$term}%")
                    ->orWhere('passport', 'like', "%{$term}%")
                    ->orWhere('nik', 'like', "%{$term}%");
            })
            ->where('is_active', 1)
            ->when(session('cabang_aktif'), function ($query, $cabangId) {
                $query->where('cabang_terdaftar', $cabangId);
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['customer_id', 'name', 'alias', 'country', 'passport', 'nik', 'alamat']));
    }

    public function screen(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
        ]);
        $match = $this->screeningQuery($data['name'], $data['alias'] ?? null)
            ->with('header')
            ->first();

        return response()->json([
            'matched' => (bool) $match,
            'item' => $match,
        ]);
    }

    private function screeningQuery($name, $alias = null)
    {
        $terms = collect([$name, $alias])
            ->filter()
            ->flatMap(function ($value) {
                return preg_split('/\\s*;\\s*/', $value);
            })
            ->map(function ($term) {
                return preg_replace('/\\s+/', ' ', trim($term));
            })
            ->filter(function ($term) {
                return mb_strlen($term) >= 2;
            })
            ->unique(function ($term) {
                return mb_strtolower($term);
            })
            ->values()
            ->all();

        if (!$terms) {
            return MasterTerduga::query()->whereRaw('1 = 0');
        }

        return MasterTerduga::query()
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $term = mb_strtolower($term);
                    $query->orWhereRaw('LOWER(name) LIKE ?', ['%' . $term . '%'])
                        ->orWhereRaw('LOWER(alias) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->where(function ($query) {
                $query->whereNull('is_clear')->orWhere('is_clear', '!=', 1);
            })
            ->whereHas('header', function ($query) {
                $query->where('is_active', 1);
            });
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules());
        $data['updated_by'] = Auth::id();
        MasterCustomer::findOrFail($id)->update($data);
        Alert::success('Berhasil', 'Data Customer Berhasil Diedit');
        return redirect()->back();
    }

    public function destroy($id)
    {
        MasterCustomer::findOrFail($id)->delete();
        Alert::success('Berhasil', 'Data Customer Berhasil Dihapus');
        return redirect()->back();
    }
}
