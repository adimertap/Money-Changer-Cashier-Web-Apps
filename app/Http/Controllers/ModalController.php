<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\MailModal;
use App\Mail\MailModalTambah;
use App\Mail\MailTransfer;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use App\Models\ModalTransaksi;
use App\Models\RiwayatModal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;


class ModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

    public function index(Request $request)
    {
        try {
            $perPage = max(1, (int) $request->input('per_page', 10));
            $cabangId = $request->input('cabang_id');
            $isOwner = Auth::user()->role === 'Owner';
            $allowedCabangIds = $isOwner
                ? MasterCabang::where('is_active', 1)->pluck('cabang_id')->all()
                : array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
            $modalQuery = ($isOwner ? ModalTransaksi::withoutGlobalScope('cabang') : ModalTransaksi::query())
                ->when(!$isOwner, function ($query) {
                    $query->where(function ($query) {
                        $query->where('tanggal_modal', Carbon::now()->format('Y-m-d'))
                            ->orWhere('riwayat_modal', '>', 0);
                    });
                })
                ->when($cabangId && in_array((int) $cabangId, $allowedCabangIds, true), function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                })
                ->orderByDesc('created_at');

            $merged = $modalQuery->with(['Pegawai', 'Cabang'])->get();

            $legacyMap = $this->legacyConnections();
            $targetLegacyIds = ($cabangId && in_array((int) $cabangId, $allowedCabangIds, true))
                ? (isset($legacyMap[(int) $cabangId]) ? [(int) $cabangId] : [])
                : array_values(array_intersect(array_keys($legacyMap), array_map('intval', $allowedCabangIds)));

            $cabangMap = !empty($targetLegacyIds)
                ? MasterCabang::whereIn('cabang_id', $targetLegacyIds)->get()->keyBy('cabang_id')
                : collect();

            foreach ($targetLegacyIds as $legacyCabangId) {
                $connection = $legacyMap[$legacyCabangId]['connection'];
                $fallbackCabangName = $legacyMap[$legacyCabangId]['cabang_name'];
                try {
                    $legacyQuery = ModalTransaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->leftJoin('users', 'tb_modal_transaksi.id_pegawai', '=', 'users.id')
                        ->select('tb_modal_transaksi.*', 'users.name as legacy_pegawai_name')
                        ->when(!$isOwner, function ($query) {
                            $query->where(function ($query) {
                                $query->where('tb_modal_transaksi.tanggal_modal', Carbon::now()->format('Y-m-d'))
                                    ->orWhere('tb_modal_transaksi.riwayat_modal', '>', 0);
                            });
                        })
                        ->orderByDesc('tb_modal_transaksi.created_at');

                    $cabangModel = $cabangMap->get($legacyCabangId) ?: new MasterCabang(['cabang_name' => $fallbackCabangName]);
                    $legacyRows = $legacyQuery->get()->each(function ($item) use ($cabangModel) {
                        $item->setRelation('Pegawai', new User(['name' => $item->legacy_pegawai_name ?: '-']));
                        $item->setRelation('Cabang', $cabangModel);
                        $item->is_legacy = true;
                    });
                    $merged = $merged->concat($legacyRows);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil modal legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $merged = $merged->sortByDesc(function ($item) {
                return strtotime((string) ($item->created_at ?? $item->tanggal_modal));
            })->values();

            $currentPage = Paginator::resolveCurrentPage();
            $modal = new LengthAwarePaginator(
                $merged->forPage($currentPage, $perPage)->values(),
                $merged->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $cabangs = MasterCabang::where('is_active', 1)
                ->whereIn('cabang_id', $allowedCabangIds)
                ->orderBy('cabang_name')
                ->get();

            $activeCabangId = ($cabangId && in_array((int) $cabangId, $allowedCabangIds, true))
                ? (int) $cabangId
                : (session('cabang_aktif') ? (int) session('cabang_aktif') : null);

            $todayBase = ($isOwner ? ModalTransaksi::withoutGlobalScope('cabang') : ModalTransaksi::query())
                ->where('tanggal_modal', Carbon::now()->format('Y-m-d'))
                ->when($activeCabangId, fn ($q) => $q->where('cabang_id', $activeCabangId));

            $modal_today = (clone $todayBase)->get();
            $modal_tf = (clone $todayBase)->first();
            $jumlah_modal_today = (clone $todayBase)->where('status_modal', 'Terima')->first();
            $today = Carbon::now()->format('Y-m-d');
            if(count($modal_today) == 0){
                Alert::warning('Modal Belum Diinput', 'Anda Belum Menginputkan Modal Hari Ini, Lakukan Inputan atau Transfer');
            }
            return view('pages.modal.index', compact('modal','modal_today','today','jumlah_modal_today','modal_tf','cabangs','cabangId'));
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Error Server: ' . $th->getMessage());
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
        //
    }

    private function resolveCabangId($requestedCabangId, $isOwner, $activeCabangId, array $allowedCabangIds)
    {
        $allowedCabangIds = array_map('intval', $allowedCabangIds);
        $cabangId = $isOwner ? $requestedCabangId : $activeCabangId;

        if ($cabangId === null || $cabangId === '' || !in_array((int) $cabangId, $allowedCabangIds, true)) {
            return null;
        }

        return (int) $cabangId;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $isOwner = Auth::user()->role === 'Owner';
        $allowedCabangIds = $isOwner
            ? MasterCabang::where('is_active', 1)->pluck('cabang_id')->all()
            : array_column(session('cabangs', []), 'cabang_id');
        $cabangId = $this->resolveCabangId(
            $request->input('cabang_id'),
            $isOwner,
            session('cabang_aktif'),
            $allowedCabangIds
        );

        abort_unless($cabangId !== null, 403, 'Cabang tidak valid.');

        $existsToday = ModalTransaksi::withoutGlobalScope('cabang')
            ->where('cabang_id', $cabangId)
            ->whereDate('tanggal_modal', Carbon::today())
            ->where('jenis_modal', 'Modal Awal')
            ->exists();
        if ($existsToday) {
            Alert::warning('Modal Sudah Ada', 'Modal awal hari ini untuk cabang tersebut sudah diinput.');
            return redirect()->back();
        }

        try {
            $modal = new ModalTransaksi();
            $modal->tanggal_modal = Carbon::now();
            $modal->jumlah_modal = $request->jumlah_modal;
            $modal->status_modal = 'Pending';
            $modal->id_pegawai = Auth::user()->id;
            $modal->cabang_id = $cabangId;
            $modal->riwayat_modal = $request->jumlah_modal;
            $modal->total_modal_backup = $request->jumlah_modal;
            $modal->jenis_modal = 'Modal Awal';
            $modal->save();

            if (app()->environment('production')) {
                $user = User::where('role','Owner')->get();
                foreach ($user as $tes) {
                    Mail::to($tes->email)->send(new MailModal($modal));
                }
            }

            Alert::success('Berhasil', 'Data Modal Berhasil Ditambahkan');
            return redirect()->back();
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

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
        try {
            $modal = ModalTransaksi::where('id_modal', $request->modal_edit_id)->first();
            if($modal->status_modal == 'Tolak' && $modal->jenis_modal == 'Edit Modal' || $modal->status_modal == 'Tolak' && $modal->jenis_modal == 'Penambahan Modal'){
                $modal->pengajuan_tambah = $request->jumlah_modal;
                $tambah = $request->jumlah_modal;

                // $modal->jumlah_modal = $modal->jumlah_modal + $tambah;
                $modal->riwayat_modal = $modal->riwayat_modal + $tambah;
                $modal->total_modal_backup = $modal->total_modal_backup + $tambah;
            }else{
                $modal->jumlah_modal = $request->jumlah_modal;
                $modal->riwayat_modal = $request->jumlah_modal;
                $modal->total_modal_backup = $request->jumlah_modal;
            }

            $modal->status_modal = 'Pending';
            $modal->jenis_modal = 'Edit Modal';
            $modal->update();

            Alert::success('Berhasil', 'Data Modal Berhasil Diedit');
            return redirect()->back();
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }

    public function tambah(Request $request)
    {
        try {
            $item = ModalTransaksi::find($request->ajukan_modal_id);
            $item->pengajuan_tambah = $request->jumlah_modal;

            $tambah = $request->jumlah_modal;
            // $item->jumlah_modal = $item->jumlah_modal + $tambah;
            $item->riwayat_modal = $item->riwayat_modal + $tambah;
            $item->total_modal_backup = $item->total_modal_backup + $tambah;

            $item->status_modal = 'Pending';
            $item->jenis_modal = 'Penambahan Modal';
            $item->save();

            if (app()->environment('production')) {
                $user = User::where('role','Owner')->get();
                foreach ($user as $tes) {
                    Mail::to($tes->email)->send(new MailModalTambah($item));
                }
            }

            Alert::success('Berhasil', 'Data Modal Berhasil Diajukan, Mohon Tunggu');
            return redirect()->back();
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function hapus(Request $request)
    {
        try {
            $modal = ModalTransaksi::find($request->modal_delete_id);
            $jurnal = Jurnal::where('id_modal', $request->modal_delete_id);
            $jurnal->delete();
            $modal->delete();

            Alert::success('Berhasil', 'Data Modal Berhasil Terhapus');
            return redirect()->back();
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }

    public function transfer(Request $request)
    {
        try {
            $modal = ModalTransaksi::find($request->modal_transfer_id);
            $modal_tuju = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
            if(empty($modal_tuju)){
                $modal_baru = new ModalTransaksi();
                $modal_baru->tanggal_modal = Carbon::now();
                $modal_baru->jumlah_modal = $modal->riwayat_modal;
                $modal_baru->status_modal = 'Pending';
                $modal_baru->id_pegawai = Auth::user()->id;
                $modal_baru->cabang_id = $modal->cabang_id;
                $modal_baru->riwayat_modal = $modal->riwayat_modal;
                $modal_baru->total_modal_backup = $modal->riwayat_modal;
                $modal_baru->jenis_modal = 'Transfer Modal';
                $modal_baru->save();
            }else{
                $perhitungan = $modal->riwayat_modal + $modal_tuju->riwayat_modal;
                // $perhitungan_awal = $modal->riwayat_modal + $modal_tuju->jumlah_modal;
                $perhitungan_total = $modal->total_modal_backup + $modal_tuju->total_modal_backup;
                $modal_tuju->jenis_modal = 'Transfer Modal';
                $modal_tuju->riwayat_modal = $perhitungan;
                // $modal_tuju->jumlah_modal = $perhitungan_awal;
                $modal_tuju->total_modal_backup = $perhitungan_total;
                $modal_tuju->save();
            }
            $modal->jenis_modal = 'Transfer Modal';
            $modal->riwayat_modal = 0;
            $modal->save();

            if (app()->environment('production')) {
                $user = User::where('role','Owner')->get();
                foreach ($user as $tes) {
                    Mail::to($tes->email)->send(new MailTransfer($modal));
                }
            }

            Alert::success('Berhasil', 'Data Transfer Modal Berhasil Diajukan, Mohon Menunggu Approval');
            return redirect()->back();
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }


    }
}
