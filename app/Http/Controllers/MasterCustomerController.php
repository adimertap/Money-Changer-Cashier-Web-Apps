<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use App\Models\MasterCustomer;
use App\Models\MasterTerduga;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCustomerController extends Controller
{
    private function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:150',
            'passport' => 'nullable|string|max:100',
            'nik' => 'nullable|string|max:100',
            'is_terduga' => 'nullable|boolean',
            'kode_densus' => 'nullable|string|max:100',
            'alias' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'cabang_terdaftar' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            'npwp' => 'nullable|string|max:50',
            'domicile' => 'nullable|string|max:150',
            'income' => 'nullable|string|max:100',
            'job' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:150',
            'company_form' => 'nullable|string|max:150',
            'position' => 'nullable|string|max:100',
            'business_sector' => 'nullable|string|max:100',
            'transaction_purpose' => 'nullable|string|max:150',
            'relationship' => 'nullable|string|max:100',
            'source_of_funds' => 'nullable|string|max:100',
        ];
    }

    private function statusRules()
    {
        return ['is_active' => ['required', 'boolean']];
    }

    public function index(Request $request)
    {
        $customer = MasterCustomer::with('cabang')
            ->when(session('cabang_aktif'), function ($query, $cabangId) {
                $query->where('cabang_terdaftar', $cabangId);
            })
            ->orderBy('name')
            ->get();
        $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get();
        $countries = [];
        $countriesPath = base_path('countries.json');
        if (is_file($countriesPath)) {
            $countries = json_decode(file_get_contents($countriesPath), true) ?: [];
        }
        asort($countries);

        return view('pages.mastercustomer.index', compact('customer', 'cabang', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['nik'] = $data['nik'] ?? null;
        $data['created_by'] = Auth::id();
        $data['is_active'] = $data['is_active'] ?? 1;
        $data['cabang_terdaftar'] = $data['cabang_terdaftar'] ?? session('cabang_aktif');
        if (!$data['cabang_terdaftar'] && Auth::user()->role !== 'Owner') {
            abort(422, 'Cabang customer wajib ditentukan.');
        }
        $customer = MasterCustomer::create($data);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['customer' => $customer]);
        }

        Alert::success('Berhasil', 'Data Customer Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function show(Request $request, $id)
    {
        $customer = MasterCustomer::with('cabang')->findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($customer);
        }

        $passport = trim((string) ($customer->passport ?? ''));
        $nik = trim((string) ($customer->nik ?? ''));
        $name = trim((string) ($customer->name ?? ''));

        $transaksiHistory = Transaksi::withoutGlobalScope('cabang')
            ->with(['Cabang', 'Pegawai', 'detailTransaksi.Currency'])
            ->where(function ($q) use ($passport, $nik, $name) {
                $hasCond = false;
                if (!empty($passport)) {
                    $cleanPassport = preg_replace('/[^a-zA-Z0-9]/', '', $passport);
                    $q->where(function ($sub) use ($passport, $cleanPassport) {
                        $sub->whereRaw('LOWER(TRIM(nomor_passport)) = ?', [mb_strtolower($passport)])
                            ->orWhereRaw("REPLACE(REPLACE(LOWER(nomor_passport), ' ', ''), '-', '') = ?", [mb_strtolower($cleanPassport)]);
                    });
                    $hasCond = true;
                }
                if (!empty($nik)) {
                    if ($hasCond) {
                        $q->orWhereRaw('LOWER(TRIM(nomor_passport)) = ?', [mb_strtolower($nik)]);
                    } else {
                        $q->whereRaw('LOWER(TRIM(nomor_passport)) = ?', [mb_strtolower($nik)]);
                        $hasCond = true;
                    }
                }
                if (!empty($name)) {
                    if ($hasCond) {
                        $q->orWhereRaw('LOWER(TRIM(nama_customer)) = ?', [mb_strtolower($name)]);
                    } else {
                        $q->whereRaw('LOWER(TRIM(nama_customer)) = ?', [mb_strtolower($name)]);
                        $hasCond = true;
                    }
                }
                if (!$hasCond) {
                    $q->whereRaw('1 = 0');
                }
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('id_transaksi', 'desc')
            ->get();

        $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get();
        $countries = [];
        $countriesPath = base_path('countries.json');
        if (is_file($countriesPath)) {
            $countries = json_decode(file_get_contents($countriesPath), true) ?: [];
        }
        asort($countries);

        return view('pages.mastercustomer.detail', compact('customer', 'transaksiHistory', 'cabang', 'countries'));
    }

    public function downloadDokumen($id)
    {
        $customer = MasterCustomer::findOrFail($id);
        $path = $customer->supporting_document_file;
        if (empty($path)) {
            Alert::warning('Perhatian', 'Dokumen lampiran profil customer tidak ditemukan.');
            return redirect()->back();
        }

        if (!Storage::disk('public')->exists($path)) {
            Alert::warning('Perhatian', 'File dokumen tidak ditemukan di penyimpanan server.');
            return redirect()->back();
        }

        return Storage::disk('public')->response($path);
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
            ->get([
                'customer_id', 'name', 'alias', 'country', 'passport', 'nik', 'alamat',
                'npwp', 'domicile', 'income', 'job', 'company', 'company_form', 'position',
                'business_sector', 'transaction_purpose', 'relationship', 'source_of_funds'
            ]));
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
        $data['nik'] = $data['nik'] ?? null;
        $data['updated_by'] = Auth::id();
        $customer = MasterCustomer::findOrFail($id);
        $customer->update($data);
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }
        Alert::success('Berhasil', 'Data Customer Berhasil Diedit');
        return redirect()->back();
    }

    public function updateDocument(Request $request, $id)
    {
        $customer = MasterCustomer::findOrFail($id);
        $data = $request->validate([
            'npwp' => 'nullable|string|max:50',
            'domicile' => 'nullable|string|max:150',
            'income' => 'nullable|string|max:100',
            'job' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:150',
            'company_form' => 'nullable|string|max:150',
            'position' => 'nullable|string|max:100',
            'business_sector' => 'nullable|string|max:100',
            'transaction_purpose' => 'nullable|string|max:150',
            'relationship' => 'nullable|string|max:100',
            'source_of_funds' => 'nullable|string|max:100',
        ]);
        $data['updated_by'] = Auth::id();
        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data Document CDD / KYC customer berhasil diperbarui.',
            'customer' => $customer,
        ]);
    }

    public function status(Request $request, $id)
    {
        $data = $request->validate($this->statusRules());
        MasterCustomer::findOrFail($id)->update(['is_active' => (bool) $data['is_active']]);

        Alert::success('Berhasil', 'Status Customer Berhasil Diubah');
        return redirect()->back();
    }

    public function destroy($id)
    {
        MasterCustomer::findOrFail($id)->delete();
        Alert::success('Berhasil', 'Data Customer Berhasil Dihapus');
        return redirect()->back();
    }
}
