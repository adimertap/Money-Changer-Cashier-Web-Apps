<?php

namespace App\Http\Controllers;

use App\Exports\ExcelDebitKredit;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use App\Models\MasterCurrency;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalKreditDebitController extends Controller
{
    private function legacyConnections(): array
    {
        $map = [];
        foreach (['mysql_cabang_2', 'mysql_cabang_4', 'mysql_cabang_5'] as $conn) {
            $cfg = config("database.connections.{$conn}");
            if (!empty($cfg['cabang_id']) && !empty($cfg['database'])) {
                $map[(int) $cfg['cabang_id']] = [
                    'connection' => $conn,
                    'cabang_name' => $cfg['cabang_name'] ?? '-',
                ];
            }
        }
        return $map;
    }

    public function index(Request $request)
    {
        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $perPage = max(1, (int) $request->input('per_page', 10));
            $user = Auth::user();
            $isOwner = $user->role === 'Owner';
            $limit = $isOwner ? 200 : 300;

            $jurnalQuery = Jurnal::with(['Cabang', 'Transaksi', 'Currency'])
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                })
                ->orderBy('updated_at', 'DESC');
            if (!$isOwner) {
                $jurnalQuery->where('id_pegawai', $user->id);
            }
            $merged = $jurnalQuery->take($limit)->get();

            $legacyMap = $this->legacyConnections();
            $targetLegacyIds = $request->filled('cabang_id')
                ? (isset($legacyMap[(int) $request->cabang_id]) ? [(int) $request->cabang_id] : [])
                : array_keys($legacyMap);

            $cabangMap = !empty($targetLegacyIds)
                ? MasterCabang::whereIn('cabang_id', $targetLegacyIds)->get()->keyBy('cabang_id')
                : collect();

            foreach ($targetLegacyIds as $legacyCabangId) {
                $connection = $legacyMap[$legacyCabangId]['connection'];
                $fallbackName = $legacyMap[$legacyCabangId]['cabang_name'];
                try {
                    $cabangModel = $cabangMap->get($legacyCabangId) ?: new MasterCabang(['cabang_name' => $fallbackName]);
                    $legacyQuery = Jurnal::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->with([
                            'Transaksi' => fn ($q) => $q->withoutGlobalScope('cabang'),
                            'Currency',
                        ])
                        ->when(!$isOwner, function ($q) use ($user) {
                            $q->where('id_pegawai', $user->id);
                        })
                        ->orderBy('updated_at', 'DESC')
                        ->take($limit);

                    $legacyRows = $legacyQuery->get()->each(function ($item) use ($cabangModel) {
                        $item->setRelation('Cabang', $cabangModel);
                    });
                    $merged = $merged->concat($legacyRows);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil jurnal debit kredit legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $merged = $merged->sortByDesc(function ($item) {
                return strtotime((string) ($item->updated_at ?? $item->created_at ?? $item->tanggal_jurnal));
            })->values();

            $currentPage = Paginator::resolveCurrentPage();
            $jurnal = new LengthAwarePaginator(
                $merged->forPage($currentPage, $perPage)->values(),
                $merged->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $currency = MasterCurrency::get();
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            return view('pages.jurnal.kredit&debit.index', compact('jurnal', 'currency', 'cabang'));
        } catch (\Throwable $th) {
            Log::error('JurnalKreditDebitController index error: ' . $th->getMessage());
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);

        $user = Auth::user();
        $isOwner = $user->role === 'Owner';

        $applyJurnalFilters = function ($query) use ($request, $isOwner, $user) {
            if ($request->from_date_export) {
                $query->where('tanggal_jurnal', '>=', $request->from_date_export);
            }
            if ($request->to_date_export) {
                $query->where('tanggal_jurnal', '<=', $request->to_date_export);
            }
            if ($request->filter_jenis) {
                $query->where('jenis_jurnal', $request->filter_jenis);
            }
            if (!$isOwner) {
                $query->where('id_pegawai', $user->id);
            }
            return $query->orderBy('updated_at');
        };

        $mainBase = Jurnal::with(['Currency', 'Cabang'])->when($request->filled('cabang_id'), fn ($q) => $q->where('cabang_id', $request->cabang_id));
        $jurnal = $applyJurnalFilters(clone $mainBase)->get();
        $totalDebit = $applyJurnalFilters(clone $mainBase)->where('jenis_jurnal', 'Debit')->get();
        $totalKredit = $applyJurnalFilters(clone $mainBase)->where('jenis_jurnal', 'Kredit Jual')->get();
        $totalModal = $applyJurnalFilters(clone $mainBase)->where('jenis_jurnal', 'Kredit')->get();

        $currencyQuery = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
            ->leftJoin('tb_master_cabang', 'tb_jurnal.cabang_id', '=', 'tb_master_cabang.cabang_id')
            ->selectRaw('nama_currency as nama, SUM(jumlah_tukar) as total, kurs as jumlah_kurs, COALESCE(tb_master_cabang.cabang_name, "-") as cabang_name')
            ->groupBy('nama_currency', 'kurs', 'tb_master_cabang.cabang_name');
        if ($request->from_date_export) {
            $currencyQuery->where('tb_jurnal.tanggal_jurnal', '>=', $request->from_date_export);
        }
        if ($request->to_date_export) {
            $currencyQuery->where('tb_jurnal.tanggal_jurnal', '<=', $request->to_date_export);
        }
        if ($request->filled('cabang_id')) {
            $currencyQuery->where('tb_jurnal.cabang_id', $request->cabang_id);
        }
        if ($request->filter_jenis) {
            $currencyQuery->where('tb_jurnal.jenis_jurnal', $request->filter_jenis);
        }
        if (!$isOwner) {
            $currencyQuery->where('tb_jurnal.id_pegawai', $user->id);
        }
        $currency = $currencyQuery->get();

        $legacyMap = $this->legacyConnections();
        $targetLegacyIds = $request->filled('cabang_id')
            ? (isset($legacyMap[(int) $request->cabang_id]) ? [(int) $request->cabang_id] : [])
            : array_keys($legacyMap);

        $legacyCabangMap = !empty($targetLegacyIds)
            ? MasterCabang::whereIn('cabang_id', $targetLegacyIds)->get()->keyBy('cabang_id')
            : collect();

        foreach ($targetLegacyIds as $legacyCabangId) {
            $connection = $legacyMap[$legacyCabangId]['connection'];
            $legacyCabangModel = $legacyCabangMap->get($legacyCabangId)
                ?: new MasterCabang(['cabang_name' => $legacyMap[$legacyCabangId]['cabang_name'] ?? '-']);
            try {
                $legacyBase = Jurnal::on($connection)->withoutGlobalScope('cabang')->with('Currency');
                $legacyJurnal = $applyJurnalFilters(clone $legacyBase)->get()->each(function ($row) use ($legacyCabangModel) {
                    $row->setRelation('Cabang', $legacyCabangModel);
                });
                $jurnal = $jurnal->concat($legacyJurnal);
                $totalDebit = $totalDebit->concat($applyJurnalFilters(clone $legacyBase)->where('jenis_jurnal', 'Debit')->get());
                $totalKredit = $totalKredit->concat($applyJurnalFilters(clone $legacyBase)->where('jenis_jurnal', 'Kredit Jual')->get());
                $totalModal = $totalModal->concat($applyJurnalFilters(clone $legacyBase)->where('jenis_jurnal', 'Kredit')->get());

                $legacyCurrQuery = Jurnal::on($connection)
                    ->withoutGlobalScope('cabang')
                    ->join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                    ->selectRaw('nama_currency as nama, SUM(jumlah_tukar) as total, kurs as jumlah_kurs')
                    ->groupBy('nama_currency', 'kurs');
                if ($request->from_date_export) {
                    $legacyCurrQuery->where('tanggal_jurnal', '>=', $request->from_date_export);
                }
                if ($request->to_date_export) {
                    $legacyCurrQuery->where('tanggal_jurnal', '<=', $request->to_date_export);
                }
                if ($request->filter_jenis) {
                    $legacyCurrQuery->where('jenis_jurnal', $request->filter_jenis);
                }
                if (!$isOwner) {
                    $legacyCurrQuery->where('id_pegawai', $user->id);
                }
                $legacyCurrRows = $legacyCurrQuery->get()->each(function ($row) use ($legacyCabangModel) {
                    $row->cabang_name = $legacyCabangModel->cabang_name ?? '-';
                });
                $currency = $currency->concat($legacyCurrRows);
            } catch (\Throwable $e) {
                Log::warning("Gagal export jurnal debit kredit legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
            }
        }

        $jurnal = $jurnal->sortBy('updated_at')->values();
        $groupedCurrency = [];
        foreach ($currency as $c) {
            $cab = $c->cabang_name ?? '-';
            $k = $cab . '|' . $c->nama . '|' . $c->jumlah_kurs;
            if (!isset($groupedCurrency[$k])) {
                $groupedCurrency[$k] = clone $c;
                $groupedCurrency[$k]->cabang_name = $cab;
                $groupedCurrency[$k]->total = (float) $c->total;
            } else {
                $groupedCurrency[$k]->total += (float) $c->total;
            }
        }
        $currency = collect(array_values($groupedCurrency))
            ->sortBy(fn ($c) => [$c->cabang_name, $c->nama, (float) $c->jumlah_kurs])
            ->values();

        $cabangName = $request->filled('cabang_id')
            ? (MasterCabang::where('cabang_id', $request->cabang_id)->value('cabang_name')
                ?: ($legacyMap[(int) $request->cabang_id]['cabang_name'] ?? '-'))
            : 'Semua Cabang';

        if (count($jurnal) == 0) {
            Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
            return redirect()->back();
        } else {
            if ($request->radio_input == 'pdf') {
                $pdf = Pdf::loadview('pages.jurnal.kredit&debit.pdf', ['jurnal' => $jurnal, 'kurs' => $currency, 'totalDebit' => $totalDebit, 'totalKredit' => $totalKredit, 'totalModal' => $totalModal, 'cabangName' => $cabangName]);
                if ($request->from_date_export && $request->to_date_export) {
                    return $pdf->download('report-jurnal ' . $request->from_date_export . ' ' . $request->to_date_export . ' .pdf');
                } else {
                    return $pdf->download('report-jurnal.pdf');
                }
            } else {
                $export = new ExcelDebitKredit($jurnal, $currency, $totalDebit, $totalKredit, $totalModal, $cabangName);
                if ($request->from_date_export && $request->to_date_export) {
                    return Excel::download($export, 'report-jurnal' . $request->from_date_export . ' ' . $request->to_date_export . ' .xlsx');
                } else {
                    return Excel::download($export, 'report-jurnal.xlsx');
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $id;
    }

    public function hapus(Request $request)
    {
        $jurnal = Jurnal::find($request->jurnal_id);
        $jurnal->delete();

        Alert::success('Berhasil', 'Data Jurnal Berhasil Terhapus');
        return redirect()->back();
    }
}
