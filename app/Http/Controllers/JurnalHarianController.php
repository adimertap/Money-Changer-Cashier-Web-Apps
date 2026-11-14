<?php

namespace App\Http\Controllers;

use App\Exports\ExcelHarianView;
use App\Exports\ExcelTransaksi;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MasterCurrency;
use App\Models\MasterCabang;
use App\Models\DetailTransaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalHarianController extends Controller
{
    /**
     * Map cabang_id => ['connection' => ..., 'cabang_name' => ...] from config/database.php (.env).
     */
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->renderJurnal($request, 'Beli', 'pages.jurnal.harian.index');
    }

    public function jual(Request $request)
    {
        return $this->renderJurnal($request, 'Jual', 'pages.jurnal.harian.jual.index');
    }

    private function renderJurnal(Request $request, string $jenisTransaksi, string $view)
    {
        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);

            $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name']);
            $merged = $this->fetchMergedTransaksi($request, $jenisTransaksi);

            $jumlah = $merged->count();
            $total = $merged->sum('total');

            $perPage = 10;
            $currentPage = Paginator::resolveCurrentPage();
            $transaksi = new LengthAwarePaginator(
                $merged->forPage($currentPage, $perPage)->values(),
                $jumlah,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
            $pegawai = User::where('role', '!=', 'Owner')->get();

            return view($view, compact('pegawai', 'transaksi', 'jumlah', 'total', 'currency', 'cabang'));
        } catch (\Throwable $th) {
            Log::error('JurnalHarian renderJurnal error: ' . $th->getMessage());
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    private function fetchMergedTransaksi(Request $request, string $jenisTransaksi): Collection
    {
        $hasDateFilter = $request->filled('from') || $request->filled('to');

        $mainQuery = Transaksi::with(['Pegawai', 'Cabang'])
            ->where('jenis_transaksi', $jenisTransaksi)
            ->orderBy('updated_at', 'DESC');

        if ($hasDateFilter) {
            if ($request->filled('from')) {
                $mainQuery->where('tanggal_transaksi', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $mainQuery->where('tanggal_transaksi', '<=', $request->to);
            }
        } else {
            $mainQuery->where('tanggal_transaksi', '>=', now()->subDays(60));
        }

        if ($request->filled('cabang_id')) {
            $mainQuery->where('cabang_id', $request->cabang_id);
        }

        $merged = $mainQuery->get();

        $legacyMap = $this->legacyConnections();
        $targetCabangIds = $request->filled('cabang_id')
            ? (isset($legacyMap[(int) $request->cabang_id]) ? [(int) $request->cabang_id] : [])
            : array_keys($legacyMap);

        if (empty($targetCabangIds)) {
            return $merged;
        }

        $cabangMap = MasterCabang::whereIn('cabang_id', $targetCabangIds)->get()->keyBy('cabang_id');

        foreach ($targetCabangIds as $cabangId) {
            $connection = $legacyMap[$cabangId]['connection'];
            $fallbackCabangName = $legacyMap[$cabangId]['cabang_name'];
            try {
                $legacyQuery = Transaksi::on($connection)
                    ->withoutGlobalScope('cabang')
                    ->leftJoin('users', 'tb_transaksi.id_pegawai', '=', 'users.id')
                    ->select('tb_transaksi.*', 'users.name as legacy_pegawai_name')
                    ->where('tb_transaksi.jenis_transaksi', $jenisTransaksi)
                    ->orderBy('tb_transaksi.updated_at', 'DESC');

                if ($hasDateFilter) {
                    if ($request->filled('from')) {
                        $legacyQuery->where('tb_transaksi.tanggal_transaksi', '>=', $request->from);
                    }
                    if ($request->filled('to')) {
                        $legacyQuery->where('tb_transaksi.tanggal_transaksi', '<=', $request->to);
                    }
                } else {
                    $legacyQuery->where('tb_transaksi.tanggal_transaksi', '>=', now()->subDays(60));
                }

                $cabangModel = $cabangMap->get($cabangId) ?: new MasterCabang(['cabang_name' => $fallbackCabangName]);
                $legacyRows = $legacyQuery->get()->each(function ($item) use ($cabangId, $cabangModel) {
                    $pegawaiName = $item->legacy_pegawai_name ?: '-';
                    $item->setRelation('Pegawai', new User(['name' => $pegawaiName]));
                    $item->setRelation('Cabang', $cabangModel);
                    $item->legacy_cabang_id = $cabangId;
                });

                $merged = $merged->concat($legacyRows);
            } catch (\Throwable $e) {
                Log::warning("Gagal mengambil data legacy cabang {$cabangId} ({$connection}): " . $e->getMessage());
            }
        }

        return $merged->sortByDesc(function ($item) {
            return strtotime((string) ($item->updated_at ?? $item->created_at ?? $item->tanggal_transaksi));
        })->values();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function Export_dokumen(Request $request)
    {
        return $this->exportByJenis($request, 'Beli');
    }

    public function Export_dokumen_jual(Request $request)
    {
        return $this->exportByJenis($request, 'Jual');
    }

    private function exportByJenis(Request $request, string $jenisTransaksi)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);

        try {
            $pegawaiFilterName = $request->id_pegawai ? User::where('id', $request->id_pegawai)->value('name') : null;

            $applyFilters = function ($query, bool $isLegacy = false) use ($request, $jenisTransaksi, $pegawaiFilterName) {
                if ($request->from_date_export) {
                    $query->where('tb_transaksi.tanggal_transaksi', '>=', $request->from_date_export);
                }
                if ($request->to_date_export) {
                    $query->where('tb_transaksi.tanggal_transaksi', '<=', $request->to_date_export);
                }
                if ($request->id_pegawai) {
                    if ($isLegacy && $pegawaiFilterName) {
                        $query->where(function ($q) use ($request, $pegawaiFilterName) {
                            $q->where('tb_transaksi.id_pegawai', $request->id_pegawai)
                                ->orWhere('users.name', $pegawaiFilterName);
                        });
                    } else {
                        $query->where('tb_transaksi.id_pegawai', $request->id_pegawai);
                    }
                }
                if ($request->id_currency) {
                    $query->where('tb_detail_transaksi.currency_id', $request->id_currency);
                }
                return $query->where('tb_transaksi.jenis_transaksi', $jenisTransaksi)
                    ->orderBy('tb_transaksi.created_at', 'DESC');
            };

            $mainQuery = Transaksi::with('Pegawai')
                ->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency');
            if ($request->filled('cabang_id')) {
                $mainQuery->where('tb_transaksi.cabang_id', $request->cabang_id);
            }
            $transaksi = $applyFilters($mainQuery, false)->get();

            $legacyMap = $this->legacyConnections();
            $targetCabangIds = $request->filled('cabang_id')
                ? (isset($legacyMap[(int) $request->cabang_id]) ? [(int) $request->cabang_id] : [])
                : array_keys($legacyMap);

            foreach ($targetCabangIds as $cabangId) {
                $connection = $legacyMap[$cabangId]['connection'];
                try {
                    $legacyQuery = Transaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->leftJoin('users', 'tb_transaksi.id_pegawai', '=', 'users.id')
                        ->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                        ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')
                        ->select('tb_transaksi.*', 'tb_detail_transaksi.*', 'tb_currency.*', 'users.name as legacy_pegawai_name');

                    $legacyRows = $applyFilters($legacyQuery, true)->get()->each(function ($item) {
                        $item->setRelation('Pegawai', new User(['name' => $item->legacy_pegawai_name ?: '-']));
                    });
                    $transaksi = $transaksi->concat($legacyRows);
                } catch (\Throwable $e) {
                    Log::warning("Gagal export data legacy cabang {$cabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $transaksi = $transaksi->sortByDesc('created_at')->values();
            $total = $transaksi->sum('total');
            $jumlah = $transaksi->count();

            if ($jumlah === 0) {
                Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                return redirect()->back();
            }

            $cabangName = $request->filled('cabang_id')
                ? (MasterCabang::where('cabang_id', $request->cabang_id)->value('cabang_name')
                    ?: ($legacyMap[(int) $request->cabang_id]['cabang_name'] ?? '-'))
                : 'Semua Cabang';

            if ($request->radio_input == 'pdf') {
                $pdf = Pdf::loadview('export.pdf', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'cabangName' => $cabangName]);
                $pegawaiName = optional($transaksi[0]->Pegawai)->name ?: '-';

                if ($request->from_date_export && $request->to_date_export && $request->id_pegawai && $request->id_currency) {
                    return $pdf->download('laporan-transaksi ' . $request->from_date_export . ' Sampai ' . $request->to_date_export . ' ' . $pegawaiName . ' ' . $transaksi[0]->nama_currency . ' .pdf');
                }
                if ($request->from_date_export && $request->to_date_export && $request->id_pegawai) {
                    return $pdf->download('laporan-transaksi ' . $request->from_date_export . ' Sampai ' . $request->to_date_export . ' ' . $pegawaiName . ' .pdf');
                }
                if ($request->id_pegawai) {
                    return $pdf->download('laporan-transaksi ' . $pegawaiName . ' .pdf');
                }
                if ($request->from_date_export && $request->to_date_export) {
                    return $pdf->download('laporan-transaksi ' . $request->from_date_export . ' Sampai ' . $request->to_date_export . ' .pdf');
                }

                return $pdf->download('laporan-transaksi-keseluruhan.pdf');
            }

            return new ExcelTransaksi($transaksi, $cabangName);
        } catch (\Throwable $th) {
            Log::error('JurnalHarian exportByJenis error: ' . $th->getMessage());
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
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
    public function show(Request $request, $id)
    {
        $legacyMap = $this->legacyConnections();
        $sourceCabang = (int) $request->query('source_cabang');
        if ($sourceCabang && isset($legacyMap[$sourceCabang])) {
            $connection = $legacyMap[$sourceCabang]['connection'];
            $transaksi = Transaksi::on($connection)->withoutGlobalScope('cabang')->find($id);
            if (!$transaksi) {
                Alert::warning('Error', 'Transaksi tidak ditemukan');
                return redirect()->back();
            }

            $pegawaiName = DB::connection($connection)->table('users')->where('id', $transaksi->id_pegawai)->value('name') ?: '-';
            $transaksi->setRelation('Pegawai', new User(['name' => $pegawaiName]));
            $cabangModel = MasterCabang::find($sourceCabang) ?: new MasterCabang(['cabang_name' => $legacyMap[$sourceCabang]['cabang_name']]);
            $transaksi->setRelation('Cabang', $cabangModel);

            $detail = DetailTransaksi::on($connection)->with('Currency')->where('id_transaksi', $id)->get();
            return view('pages.transaksi.detail', compact('transaksi', 'detail'));
        }

        $transaksi = Transaksi::with('Pegawai', 'Cabang', 'detailTransaksi.Currency')->find($id);
        if (!$transaksi) {
            Alert::warning('Error', 'Transaksi tidak ditemukan');
            return redirect()->back();
        }

        // Auto-heal jika customer di database belum lengkap
        $transaksi->healCustomerData();

        $detail = DetailTransaksi::where('id_transaksi', $id)->get();

        return view('pages.transaksi.detail', compact('transaksi', 'detail'));
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
        //
    }
}
