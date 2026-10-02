<?php

namespace App\Http\Controllers;

use App\Models\MasterTerduga;
use App\Models\MasterTerdugaHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterTerdugaExcel;
use ResourceBundle;
use Locale;
use RealRashid\SweetAlert\Facades\Alert;

class MasterTerdugaController extends Controller
{
    private function headerRules()
    {
        return [
            'tahun' => 'required|integer|min:1900|max:2100',
            'is_active' => 'nullable|boolean',
        ];
    }

    private function detailRules()
    {
        return [
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'terduga_type' => 'nullable|string|max:30',
            'kode_densus' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'wn' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'is_clear' => 'nullable|boolean',
        ];
    }

    private function countries()
    {
        return collect(ResourceBundle::getLocales(''))
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
    }

    private function syncJumlah(MasterTerdugaHeader $header)
    {
        $header->update(['jumlah' => $header->terduga()->count()]);
    }

    private function cell(array $row, $index)
    {
        if (!array_key_exists($index, $row)) {
            return null;
        }

        $value = $row[$index];
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_array($value)) {
            $value = implode(' ', $value);
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function importDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && (float) $value > 20000) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $value = str_ireplace(
            ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'],
            ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
            (string) $value
        );
        $timestamp = strtotime((string) $value);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function importKey(array $data)
    {
        return strtolower(implode('|', array_map(function ($value) {
            return preg_replace('/\s+/', ' ', trim((string) $value));
        }, [$data['name'], $data['terduga_type'], $data['kode_densus'], $data['wn']])));
    }

    private function splitNameAlias($value)
    {
        $parts = preg_split('/\s+alias\s+/i', trim((string) $value));
        $name = trim(array_shift($parts));
        $aliases = array_values(array_filter(array_map('trim', $parts)));

        return [$name ?: null, $aliases ? implode(';', $aliases) : null];
    }

    private function importedRows($rows)
    {
        if (count($rows) < 2) {
            throw new \RuntimeException('File Excel tidak memiliki baris data.');
        }

        $header = array_map(function ($value) {
            return strtolower(trim((string) $value));
        }, array_slice($rows[0], 0, 8));
        $expected = ['nama', 'deskripsi', 'terduga', 'kode densus', 'tpt lahir', 'tgl lahir', 'wn', 'alamat'];
        if ($header !== $expected) {
            throw new \RuntimeException('Format Excel harus A1:H1: Nama, Deskripsi, Terduga, Kode Densus, Tpt Lahir, Tgl Lahir, WN, Alamat.');
        }

        $records = [];
        $seen = [];
        foreach (array_slice($rows, 1) as $row) {
            $name = $this->cell($row, 0);
            if (!$name) {
                continue;
            }

            [$name, $alias] = $this->splitNameAlias($name);
            if (!$name) {
                continue;
            }

            $record = [
                'name' => $name,
                'alias' => $alias,
                'description' => $this->cell($row, 1),
                'terduga_type' => $this->cell($row, 2),
                'kode_densus' => $this->cell($row, 3),
                'tempat_lahir' => $this->cell($row, 4),
                'tanggal_lahir' => $this->importDate($this->cell($row, 5)),
                'wn' => $this->cell($row, 6),
                'alamat' => $this->cell($row, 7),
                'is_clear' => 0,
            ];
            $key = $this->importKey($record);
            if (!isset($seen[$key])) {
                $records[] = $record;
                $seen[$key] = true;
            }
        }

        if (!$records) {
            throw new \RuntimeException('Tidak ada data terduga yang dapat diimport.');
        }

        return $records;
    }

    public function index()
    {
        $headers = MasterTerdugaHeader::withCount('terduga')->orderByDesc('tahun')->get();
        return view('pages.masterterduga.index', compact('headers'));
    }

    private function detailQuery($headerId, Request $request)
    {
        return MasterTerduga::where('terduga_header_id', $headerId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . $request->search . '%';
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('alias', 'like', $search)
                        ->orWhere('terduga_type', 'like', $search)
                        ->orWhere('kode_densus', 'like', $search)
                        ->orWhere('wn', 'like', $search);
                });
            })
            ->when($request->filled('terduga_type'), function ($query) use ($request) {
                $query->where('terduga_type', $request->terduga_type);
            })
            ->orderBy('name');
    }

    private function detailTypes($headerId)
    {
        return MasterTerduga::where('terduga_header_id', $headerId)
            ->whereNotNull('terduga_type')
            ->where('terduga_type', '!=', '')
            ->distinct()
            ->orderBy('terduga_type')
            ->pluck('terduga_type');
    }

    public function show(Request $request, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($id);
        $header->setRelation('terduga', $this->detailQuery($id, $request)->get());
        $types = $this->detailTypes($id);
        $countries = $this->countries();
        return view('pages.masterterduga.detail', compact('header', 'countries', 'types'));
    }

    public function exportExcel(Request $request, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($id);
        $header->setRelation('terduga', $this->detailQuery($id, $request)->get());
        return Excel::download(new MasterTerdugaExcel($header), 'master-terduga-' . $header->tahun . '.xlsx');
    }

    public function exportPdf(Request $request, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($id);
        $header->setRelation('terduga', $this->detailQuery($id, $request)->get());
        return Pdf::loadView('pages.masterterduga.export-pdf', compact('header'))
            ->setPaper('a4', 'landscape')
            ->download('master-terduga-' . $header->tahun . '.pdf');
    }

    public function create()
    {
        return redirect()->route('master-terduga.index');
    }

    public function edit($id)
    {
        return response()->json(MasterTerdugaHeader::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->headerRules());
        $data['is_active'] = $data['is_active'] ?? 1;
        $data['jumlah'] = 0;
        $data['created_by'] = Auth::id();
        MasterTerdugaHeader::create($data);
        Alert::success('Berhasil', 'Header Terduga Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->headerRules());
        $data['updated_by'] = Auth::id();
        MasterTerdugaHeader::findOrFail($id)->update($data);
        Alert::success('Berhasil', 'Header Terduga Berhasil Diedit');
        return redirect()->back();
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'tahun' => 'required|integer|min:1900|max:2100',
            'file' => 'required|file|mimes:xls,xlsx,csv|max:10240',
        ]);

        try {
            $rows = Excel::toArray([], $request->file('file'))[0] ?? [];
            $records = $this->importedRows($rows);
            $imported = 0;
            $skipped = 0;

            DB::transaction(function () use ($data, $request, $records, &$imported, &$skipped) {
                $header = MasterTerdugaHeader::firstOrCreate(
                    ['tahun' => (string) $data['tahun']],
                    [
                        'is_active' => 1,
                        'jumlah' => 0,
                        'created_by' => Auth::id(),
                    ]
                );

                $existingKeys = [];
                $header->terduga()->select(['name', 'alias', 'terduga_type', 'kode_densus', 'wn'])->get()->each(function ($item) use (&$existingKeys) {
                    $existingKeys[$this->importKey([
                        'name' => $item->name,
                        'alias' => $item->alias,
                        'terduga_type' => $item->terduga_type,
                        'kode_densus' => $item->kode_densus,
                        'wn' => $item->wn,
                    ])] = true;
                });

                foreach ($records as $record) {
                    $key = $this->importKey($record);
                    if (isset($existingKeys[$key])) {
                        $skipped++;
                        continue;
                    }

                    $record['terduga_header_id'] = $header->terduga_header_id;
                    $record['created_by'] = Auth::id();
                    MasterTerduga::create($record);
                    $existingKeys[$key] = true;
                    $imported++;
                }

                $header->file_name = $request->file('file')->getClientOriginalName();
                $header->updated_by = Auth::id();
                $header->save();
                $this->syncJumlah($header);
            });

            Alert::success('Berhasil', "Import selesai. Ditambahkan: {$imported}, dilewati: {$skipped}.");
        } catch (\Throwable $e) {
            Alert::warning('Gagal', $e->getMessage());
        }

        return redirect()->route('master-terduga.index');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $header = MasterTerdugaHeader::findOrFail($id);
            $header->terduga()->delete();
            $header->delete();
        });
        Alert::success('Berhasil', 'Header dan Detail Terduga Berhasil Dihapus');
        return redirect()->route('master-terduga.index');
    }

    public function createDetail($headerId)
    {
        $header = MasterTerdugaHeader::findOrFail($headerId);
        $countries = $this->countries();
        return view('pages.masterterduga.create', compact('header', 'countries'));
    }

    public function storeDetail(Request $request, $headerId)
    {
        $header = MasterTerdugaHeader::findOrFail($headerId);
        $data = $request->validate($this->detailRules());
        $data['terduga_header_id'] = $header->terduga_header_id;
        $data['created_by'] = Auth::id();
        DB::transaction(function () use ($header, $data) {
            MasterTerduga::create($data);
            $this->syncJumlah($header);
        });
        Alert::success('Berhasil', 'Data Terduga Berhasil Ditambahkan');
        return redirect()->route('master-terduga.show', $header->terduga_header_id);
    }

    public function editDetail($headerId, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($headerId);
        return response()->json($header->terduga()->findOrFail($id));
    }

    public function updateDetail(Request $request, $headerId, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($headerId);
        $terduga = $header->terduga()->findOrFail($id);
        $data = $request->validate($this->detailRules());
        $data['updated_by'] = Auth::id();
        $terduga->update($data);
        Alert::success('Berhasil', 'Data Terduga Berhasil Diedit');
        return redirect()->route('master-terduga.show', $header->terduga_header_id);
    }

    public function destroyDetail($headerId, $id)
    {
        $header = MasterTerdugaHeader::findOrFail($headerId);
        $header->terduga()->findOrFail($id)->delete();
        $this->syncJumlah($header);
        Alert::success('Berhasil', 'Data Terduga Berhasil Dihapus');
        return redirect()->route('master-terduga.show', $header->terduga_header_id);
    }
}
