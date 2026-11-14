<?php

namespace App\Http\Controllers;

use App\Models\LogEdit;
use App\Models\LogEditDetail;
use App\Models\MasterCabang;
use App\Models\ModalTransaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogEditController extends Controller
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
        $isOwner = auth()->user()->role === 'Owner';
        $allowedCabangIds = $isOwner
            ? MasterCabang::where('is_active', 1)->pluck('cabang_id')->all()
            : array_column(session('cabangs', []), 'cabang_id');
        $cabangId = $isOwner ? $request->input('cabang_id') : session('cabang_aktif');
        if ($cabangId !== null && !in_array((int) $cabangId, array_map('intval', $allowedCabangIds), true)) {
            abort(403);
        }

        $log = LogEdit::with([
            'detailLog',
            'Pegawai',
            'Modal.Cabang',
        ]);
        if($request->from){
            $log->where('tanggal_transaksi', '>=', $request->from);
        }
        if($request->to){
            $log->where('tanggal_transaksi', '<=', $request->to);
        }
        if($request->jenis){
            $log->where('jenis_log', '=', $request->jenis);
        }
        if ($isOwner) {
            $log->whereHas('Modal', function ($query) use ($cabangId) {
                $query->withoutGlobalScope('cabang');
                if ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                }
            });
        } elseif ($cabangId) {
            $log->whereHas('Modal', function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId);
            });
        }
        $log = $log->orderBy('updated_at','DESC')->get();

        $legacyMap = $this->legacyConnections();
        $allowedIntIds = array_map('intval', $allowedCabangIds);
        $targetLegacyIds = $cabangId
            ? (isset($legacyMap[(int) $cabangId]) ? [(int) $cabangId] : [])
            : array_values(array_intersect(array_keys($legacyMap), $allowedIntIds));

        $cabangMap = !empty($targetLegacyIds)
            ? MasterCabang::whereIn('cabang_id', $targetLegacyIds)->get()->keyBy('cabang_id')
            : collect();

        foreach ($targetLegacyIds as $legacyCabangId) {
            $connection = $legacyMap[$legacyCabangId]['connection'];
            $fallbackName = $legacyMap[$legacyCabangId]['cabang_name'];
            try {
                $cabangModel = $cabangMap->get($legacyCabangId) ?: new MasterCabang(['cabang_name' => $fallbackName]);
                $legacyQuery = LogEdit::on($connection)
                    ->leftJoin('users', 'new_log_edit.id_pegawai', '=', 'users.id')
                    ->select('new_log_edit.*', 'users.name as legacy_pegawai_name');

                if ($request->from) {
                    $legacyQuery->where('new_log_edit.tanggal_transaksi', '>=', $request->from);
                }
                if ($request->to) {
                    $legacyQuery->where('new_log_edit.tanggal_transaksi', '<=', $request->to);
                }
                if ($request->jenis) {
                    $legacyQuery->where('new_log_edit.jenis_log', '=', $request->jenis);
                }

                $legacyRows = $legacyQuery->orderBy('new_log_edit.updated_at', 'DESC')->get()->each(function ($item) use ($legacyCabangId, $cabangModel) {
                    $item->setRelation('Pegawai', new User(['name' => $item->legacy_pegawai_name ?: '-']));
                    $modalModel = new ModalTransaksi();
                    $modalModel->setRelation('Cabang', $cabangModel);
                    $item->setRelation('Modal', $modalModel);
                    $item->legacy_cabang_id = $legacyCabangId;
                });

                $log = $log->concat($legacyRows);
            } catch (\Throwable $e) {
                Log::warning("Gagal mengambil log edit legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
            }
        }

        $log = $log->sortByDesc(function ($item) {
            return strtotime((string) ($item->updated_at ?? $item->created_at ?? $item->tanggal_transaksi));
        })->values();

        $cabangs = $isOwner
            ? MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name'])
            : collect();
        return view('pages.log.index', compact('log', 'cabangs', 'cabangId'));
    }

    public function filterLog(Request $request)
    {
        $log = LogEdit::with('detailLog');
        if($request->from_date_export){
            $log->where('tanggal_transaksi', '>=', $request->from_date_export);
        }
        if($request->to_date_export){
            $log->where('tanggal_transaksi', '<=', $request->to_date_export);
        }
        if($request->jenis_log){
            $log->where('jenis_log', '=', $request->jenis_log);
        }
        $log = $log->get();

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
            $log = LogEdit::on($connection)->find($id);
            $detail = LogEditDetail::on($connection)->where('id_log', $id)->get();
            return view('pages.log.detail', compact('log', 'detail'));
        }

        $log = LogEdit::find($id);
        $detail = LogEditDetail::where('id_log', $id)->get();
        return view('pages.log.detail', compact('log','detail'));
    }

    public function getdetail(Request $request, $id)
    {
        $legacyMap = $this->legacyConnections();
        $sourceCabang = (int) $request->query('source_cabang');
        $query = ($sourceCabang && isset($legacyMap[$sourceCabang]))
            ? LogEditDetail::on($legacyMap[$sourceCabang]['connection'])
            : LogEditDetail::query();

        $log = $query->join('new_log_edit', 'new_detail_log_edit.id_log', 'new_log_edit.id_log')
            ->leftjoin('tb_currency', 'new_detail_log_edit.currency_id', 'tb_currency.id_currency')
            ->where('new_detail_log_edit.id_log', $id)
            ->get();
        return $log;
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
