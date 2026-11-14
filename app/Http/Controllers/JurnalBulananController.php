<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\MasterCabang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalBulananController extends Controller
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
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            $transaksiQuery = Transaksi::with('Cabang')
                ->selectRaw(
                    'SUM(total) as grand_total,
                    DATE_FORMAT(tanggal_transaksi, "%m") as month,
                    YEAR(tanggal_transaksi) as year,
                    cabang_id'
                )
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                })
                ->groupBy('year', 'month', 'cabang_id')
                ->orderBy('year', 'DESC')
                ->orderByRaw("FIELD(month, '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12')");
            $transaksi = $transaksiQuery->get();

            $legacyMap = $this->legacyConnections();
            $targetLegacyIds = $request->filled('cabang_id')
                ? (isset($legacyMap[(int) $request->cabang_id]) ? [(int) $request->cabang_id] : [])
                : array_keys($legacyMap);

            $cabangMap = MasterCabang::whereIn('cabang_id', array_keys($legacyMap))->get()->keyBy('cabang_id');

            foreach ($targetLegacyIds as $legacyCabangId) {
                $connection = $legacyMap[$legacyCabangId]['connection'];
                $fallbackName = $legacyMap[$legacyCabangId]['cabang_name'];
                try {
                    $cabangModel = $cabangMap->get($legacyCabangId) ?: new MasterCabang(['cabang_name' => $fallbackName]);
                    $legacyRows = Transaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->selectRaw(
                            'SUM(total) as grand_total,
                            DATE_FORMAT(tanggal_transaksi, "%m") as month,
                            YEAR(tanggal_transaksi) as year,
                            ? as cabang_id',
                            [$legacyCabangId]
                        )
                        ->groupBy('year', 'month')
                        ->get()
                        ->each(function ($row) use ($cabangModel) {
                            $row->setRelation('Cabang', $cabangModel);
                        });

                    $transaksi = $transaksi->concat($legacyRows);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil jurnal bulanan legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $transaksi = $transaksi->sortBy([
                ['year', 'desc'],
                ['month', 'asc'],
            ])->values();

            $months = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $years = $transaksi->pluck('year')->filter()->unique()->sort()->values()->all();
            $data = [];
            foreach ($transaksi as $item) {
                if (!$item->month || !isset($months[$item->month])) {
                    continue;
                }
                $key = $item->year . '-' . $item->month . '-' . ($item->cabang_id ?: 'all');
                if (!isset($data[$key])) {
                    $data[$key] = [
                        'month_name' => $months[$item->month],
                        'year' => $item->year,
                        'cabang_id' => $item->cabang_id,
                        'cabang_name' => optional($item->Cabang)->cabang_name ?: ($legacyMap[(int) $item->cabang_id]['cabang_name'] ?? 'Semua Cabang'),
                        'totals' => array_fill_keys($years, 0),
                    ];
                }
                $data[$key]['totals'][$item->year] += (float) $item->grand_total;
            }

            return view('pages.jurnal.bulan.index', compact('data', 'years', 'cabang'));
        } catch (\Throwable $th) {
            Log::error('JurnalBulananController index error: ' . $th->getMessage());
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
    public function show(Request $request, $month)
    {
        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            $transaksiQuery = Transaksi::with('Cabang')->whereMonth('tanggal_transaksi', '=', $month)
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                });
            $transaksi = $transaksiQuery
                ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi, jenis_transaksi as jenis, cabang_id')
                ->groupBy('tanggal_transaksi', 'jenis_transaksi', 'cabang_id')
                ->orderBy('tanggal_transaksi', 'DESC')
                ->get();

            $transaksi_seluruh = Transaksi::with(['Pegawai', 'Cabang'])
                ->whereMonth('tanggal_transaksi', '=', $month)
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                });
            if ($request->from) {
                $transaksi_seluruh->where('tanggal_transaksi', '>=', $request->from);
            }
            if ($request->to) {
                $transaksi_seluruh->where('tanggal_transaksi', '<=', $request->to);
            }
            $transaksi_seluruh = $transaksi_seluruh->orderBy('tanggal_transaksi', 'DESC')->get();

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

                    $legacyRekap = Transaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->whereMonth('tanggal_transaksi', '=', $month)
                        ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi, jenis_transaksi as jenis, ? as cabang_id', [$legacyCabangId])
                        ->groupBy('tanggal_transaksi', 'jenis_transaksi')
                        ->orderBy('tanggal_transaksi', 'DESC')
                        ->get()
                        ->each(fn ($r) => $r->setRelation('Cabang', $cabangModel));
                    $transaksi = $transaksi->concat($legacyRekap);

                    $legacyDetailQuery = Transaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->leftJoin('users', 'tb_transaksi.id_pegawai', '=', 'users.id')
                        ->select('tb_transaksi.*', 'users.name as legacy_pegawai_name')
                        ->whereMonth('tb_transaksi.tanggal_transaksi', '=', $month);
                    if ($request->from) {
                        $legacyDetailQuery->where('tb_transaksi.tanggal_transaksi', '>=', $request->from);
                    }
                    if ($request->to) {
                        $legacyDetailQuery->where('tb_transaksi.tanggal_transaksi', '<=', $request->to);
                    }
                    $legacyDetail = $legacyDetailQuery->orderBy('tb_transaksi.tanggal_transaksi', 'DESC')->get()->each(function ($item) use ($legacyCabangId, $cabangModel) {
                        $item->setRelation('Pegawai', new \App\Models\User(['name' => $item->legacy_pegawai_name ?: '-']));
                        $item->setRelation('Cabang', $cabangModel);
                        $item->legacy_cabang_id = $legacyCabangId;
                    });
                    $transaksi_seluruh = $transaksi_seluruh->concat($legacyDetail);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil detail bulanan legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $transaksi = $transaksi->sortByDesc('tanggal_transaksi')->values();
            $transaksi_seluruh = $transaksi_seluruh->sortByDesc('tanggal_transaksi')->values();
            $bulan = $month;

            return view('pages.jurnal.bulan.detail', compact('transaksi', 'transaksi_seluruh', 'bulan', 'cabang'));

        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tanggal_transaksi)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);
        $transaksi = Transaksi::with('Cabang')
            ->where('tanggal_transaksi', $tanggal_transaksi)
            ->when($request->filled('cabang_id'), function ($query) use ($request) {
                $query->where('cabang_id', $request->cabang_id);
            })
            ->get();
        return view('pages.jurnal.bulan.detailtanggal', compact('transaksi'));
    }

    public function DetailTransaksi(Request $request, $id)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);

        $legacyMap = $this->legacyConnections();
        $sourceCabang = (int) $request->query('source_cabang');
        if ($sourceCabang && isset($legacyMap[$sourceCabang])) {
            $connection = $legacyMap[$sourceCabang]['connection'];
            $transaksi = Transaksi::on($connection)->withoutGlobalScope('cabang')->findOrFail($id);
            $pegawaiName = DB::connection($connection)->table('users')->where('id', $transaksi->id_pegawai)->value('name') ?: '-';
            $transaksi->setRelation('Pegawai', new \App\Models\User(['name' => $pegawaiName]));
            $cabangModel = MasterCabang::find($sourceCabang) ?: new MasterCabang(['cabang_name' => $legacyMap[$sourceCabang]['cabang_name']]);
            $transaksi->setRelation('Cabang', $cabangModel);

            $detail = DetailTransaksi::on($connection)->with('Currency')->where('id_transaksi', $id)->get();
            return view('pages.jurnal.bulan.detailtransaksi', compact('transaksi', 'detail'));
        }

        $transaksi = Transaksi::with('Pegawai','Cabang','detailTransaksi.Currency')
            ->where('id_transaksi', $id)
            ->when($request->filled('cabang_id'), function ($query) use ($request) {
                $query->where('cabang_id', $request->cabang_id);
            })
            ->firstOrFail();

        $transaksi->healCustomerData();

        $detail = DetailTransaksi::where('id_transaksi', $id)->get();
        return view('pages.jurnal.bulan.detailtransaksi', compact('transaksi','detail'));
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
