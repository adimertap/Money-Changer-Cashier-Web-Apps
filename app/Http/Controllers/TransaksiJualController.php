<?php

namespace App\Http\Controllers;


use App\Models\Jurnal;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Alert;
use App\Models\DetailTransaksi;
use App\Models\LogEdit;
use App\Models\LogEditDetail;
use App\Models\MasterCurrency;
use App\Models\MasterCabang;
use App\Models\ModalTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiJualController extends Controller
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

    private function mergeValasSummary($baseRows, $extraRows, array $groupKeys, array $sumFields)
    {
        $grouped = [];
        foreach ($baseRows->concat($extraRows) as $row) {
            $key = implode('|', array_map(fn ($k) => (string) ($row->{$k} ?? ''), $groupKeys));
            if (!isset($grouped[$key])) {
                $grouped[$key] = clone $row;
                foreach ($sumFields as $f) {
                    $grouped[$key]->{$f} = (float) ($row->{$f} ?? 0);
                }
            } else {
                foreach ($sumFields as $f) {
                    $grouped[$key]->{$f} = (float) $grouped[$key]->{$f} + (float) ($row->{$f} ?? 0);
                }
            }
        }
        return collect(array_values($grouped));
    }

    public function index(Request $request)
    {

        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);

            $today = Carbon::now()->format('Y-m-d');
            $user = Auth::user();
            $isPegawai = $user->role != 'Owner';
            $selectedCabangId = $request->filled('cabang_id')
                ? (int) $request->cabang_id
                : ($isPegawai && session('cabang_aktif') ? (int) session('cabang_aktif') : null);

            $transaksiQuery = Transaksi::with(['Pegawai', 'Cabang'])
                ->where('tanggal_transaksi', $today)
                ->where('jenis_transaksi', 'Jual')
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                })
                ->orderBy('updated_at', 'DESC');
            $jurnalQuery = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                ->where('tanggal_jurnal', $today);
            $jurnalQuery2 = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                ->where('tanggal_jurnal', $today);

            if ($request->filled('cabang_id')) {
                $jurnalQuery->where('tb_jurnal.cabang_id', $request->cabang_id);
                $jurnalQuery2->where('tb_jurnal.cabang_id', $request->cabang_id);
            }

            if ($isPegawai) {
                $transaksiQuery->where('id_pegawai', $user->id);
                $jurnalQuery->where('id_pegawai', $user->id);
                $jurnalQuery2->where('id_pegawai', $user->id);
            }

            $transaksi = $transaksiQuery->get();

            $legacyMap = $this->legacyConnections();
            $allowedIds = $isPegawai
                ? ($selectedCabangId ? [$selectedCabangId] : array_map('intval', array_column(session('cabangs', []), 'cabang_id')))
                : MasterCabang::where('is_active', 1)->pluck('cabang_id')->map(fn ($id) => (int) $id)->all();

            $targetLegacyIds = $selectedCabangId
                ? (isset($legacyMap[$selectedCabangId]) ? [$selectedCabangId] : [])
                : array_values(array_intersect(array_keys($legacyMap), $allowedIds));

            $cabangMap = !empty($targetLegacyIds)
                ? MasterCabang::whereIn('cabang_id', $targetLegacyIds)->get()->keyBy('cabang_id')
                : collect();

            $filterPegawaiId = (!$isPegawai && $request->filterData) ? $request->filterData : ($isPegawai ? $user->id : null);
            $filterPegawaiName = $filterPegawaiId ? User::where('id', $filterPegawaiId)->value('name') : null;

            if (!$isPegawai && $request->filterData) {
                $jurnalQuery->where('id_pegawai', $request->filterData);
                $jurnalQuery2->where('id_pegawai', $request->filterData);
            }

            $report = $jurnalQuery->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis')
                ->where('jenis_jurnal', 'Kredit Jual')
                ->groupBy('nama_currency', 'kurs', 'jenis_kurs')
                ->get();

            $valas = $jurnalQuery2->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total')
                ->where('jenis_jurnal', 'Kredit Jual')
                ->groupBy('nama_currency', 'jenis_kurs')
                ->get();

            foreach ($targetLegacyIds as $legacyCabangId) {
                $connection = $legacyMap[$legacyCabangId]['connection'];
                $fallbackCabangName = $legacyMap[$legacyCabangId]['cabang_name'];
                try {
                    $legacyQuery = Transaksi::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->leftJoin('users', 'tb_transaksi.id_pegawai', '=', 'users.id')
                        ->select('tb_transaksi.*', 'users.name as legacy_pegawai_name')
                        ->where('tb_transaksi.tanggal_transaksi', $today)
                        ->where('tb_transaksi.jenis_transaksi', 'Jual')
                        ->when($isPegawai, function ($q) use ($user) {
                            $q->where(function ($sub) use ($user) {
                                $sub->where('tb_transaksi.id_pegawai', $user->id)
                                    ->orWhere('users.name', $user->name);
                            });
                        })
                        ->orderBy('tb_transaksi.updated_at', 'DESC');

                    $cabangModel = $cabangMap->get($legacyCabangId) ?: new MasterCabang(['cabang_name' => $fallbackCabangName]);
                    $legacyRows = $legacyQuery->get()->each(function ($item) use ($legacyCabangId, $cabangModel) {
                        $item->setRelation('Pegawai', new User(['name' => $item->legacy_pegawai_name ?: '-']));
                        $item->setRelation('Cabang', $cabangModel);
                        $item->legacy_cabang_id = $legacyCabangId;
                    });
                    $transaksi = $transaksi->concat($legacyRows);

                    $legacyJurnalBase = Jurnal::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->leftJoin('users', 'tb_jurnal.id_pegawai', '=', 'users.id')
                        ->join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                        ->where('tb_jurnal.tanggal_jurnal', $today)
                        ->where('tb_jurnal.jenis_jurnal', 'Kredit Jual')
                        ->when($filterPegawaiId, function ($q) use ($filterPegawaiId, $filterPegawaiName) {
                            $q->where(function ($sub) use ($filterPegawaiId, $filterPegawaiName) {
                                $sub->where('tb_jurnal.id_pegawai', $filterPegawaiId);
                                if ($filterPegawaiName) {
                                    $sub->orWhere('users.name', $filterPegawaiName);
                                }
                            });
                        });

                    $legacyReport = (clone $legacyJurnalBase)
                        ->selectRaw('tb_currency.nama_currency as nama_kurs, SUM(tb_jurnal.jumlah_tukar) as jumlah_tukar, tb_jurnal.kurs as nilai_kurs, tb_currency.jenis_kurs as jenis')
                        ->groupBy('tb_currency.nama_currency', 'tb_jurnal.kurs', 'tb_currency.jenis_kurs')
                        ->get();
                    $report = $this->mergeValasSummary($report, $legacyReport, ['nama_kurs', 'nilai_kurs', 'jenis'], ['jumlah_tukar']);

                    $legacyValas = (clone $legacyJurnalBase)
                        ->selectRaw('tb_currency.nama_currency as nama_kurs, SUM(tb_jurnal.jumlah_tukar) as jumlah, tb_jurnal.kurs as nilai, tb_currency.jenis_kurs as jenis, SUM(tb_jurnal.total_tukar) as total')
                        ->groupBy('tb_currency.nama_currency', 'tb_currency.jenis_kurs')
                        ->get();
                    $valas = $this->mergeValasSummary($valas, $legacyValas, ['nama_kurs', 'jenis'], ['jumlah', 'total']);
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil transaksi jual legacy cabang {$legacyCabangId} ({$connection}): " . $e->getMessage());
                }
            }

            $transaksi = $transaksi->sortByDesc(function ($item) {
                return strtotime((string) ($item->updated_at ?? $item->created_at ?? $item->tanggal_transaksi));
            })->values();

            $count = $transaksi->count();
            $total_transaksi = $transaksi->sum('total');
            $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            if (!$isPegawai) {
                $pegawai = User::where('role', '!=', 'Owner')->get();
                return view('pages.TransaksiJual.owner', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'pegawai', 'report', 'cabang'));
            }

            return view('pages.TransaksiJual.index', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'report', 'cabang'));
        } catch (\Throwable $th) {
            Log::error('TransaksiJualController index error: ' . $th->getMessage());
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function getkursJumlah($id_currency)
    {
        $kurs = MasterCurrency::where('id_currency', '=', $id_currency)->pluck('jumlah_valas');
        return $kurs;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        // $tes = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        // if(empty($tes)){
        //     Alert::warning('Belum Mengajukan Modal', 'Anda Belum Mengajukan Modal');
        //     return redirect()->route('modal.index');
        // }else{
        //     if($tes->status_modal == 'Pending'){
        //         Alert::warning('Modal Diproses, Mohon Menunggu', 'Pengajuan Modal Anda Hari Ini Belum Diproses oleh Owner');
        //         return redirect()->route('modal.index');
        //     }elseif($tes->status_modal == 'Tolak'){
        //         Alert::warning('Modal Ditolak', 'Pengajuan Modal Anda Hari Ini Ditolak oleh Owner, Edit Data Modal');
        //         return redirect()->route('modal.index');
        //     }else{
        //         $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        //     }
        // }

        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');

        $id = Transaksi::getIdJual();
        foreach ($id as $value);
        $idlama = $value->id_transaksi;
        $idbaru = $idlama + 1;
        $blt = date('ymd');
        $id = Auth::user()->id;
        $kode_transaksi = 'JL' . $blt . '-' . $idbaru;

        $idTest = Transaksi::getId();
        foreach ($idTest as $value);
        $idlama2 = $value->id_transaksi;
        $idbaru2 = $idlama2 + 1;

        return view('pages.TransaksiJual.create', compact('idbaru2', 'currency', 'modal', 'today', 'kode_transaksi', 'today_format', 'idbaru'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
            DB::beginTransaction();
            $transaksi = new Transaksi();
            $transaksi->kode_transaksi = $request->kode_transaksi;
            $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
            $transaksi->total = $request->total;
            $transaksi->id_pegawai = Auth::user()->id;
            $transaksi->jenis_transaksi = 'Jual';
            $transaksi->cabang_id = session('cabang_aktif');
            if ($modal) {
                $transaksi->id_modal = $modal->id_modal;
            }
            $transaksi->save();


            foreach ($request->detail as $key) {
                $det = new DetailTransaksi;
                $det->currency_id = $key['currency_id'];
                $det->jumlah_currency = $key['jumlah_currency'];
                $det->jumlah_tukar = $key['jumlah_tukar'];
                $det->total_tukar = $key['total_tukar'];
                $det->id_transaksi = $transaksi->id_transaksi;
                $det->save();

                $jurnal = new Jurnal();
                $jurnal->id_transaksi = $transaksi->id_transaksi;
                $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                $jurnal->id_currency = $key['currency_id'];
                $jurnal->kurs = $key['jumlah_currency'];
                $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                $jurnal->total_tukar = $key['total_tukar'];
                $jurnal->jenis_jurnal = 'Kredit Jual';
                $jurnal->id_pegawai = Auth::user()->id;
                $jurnal->cabang_id = $transaksi->cabang_id;
                $jurnal->save();

                $cry = MasterCurrency::where('id_currency', $key['currency_id'])->first();
                if ($cry) {
                    $cry->last_nilai_jual = $key['jumlah_currency'];
                    $cry->jumlah_valas -= $key['jumlah_tukar'];
                    $cry->update();
                }
            }

            if (!$modal) {
                $modal = new ModalTransaksi();
                $modal->tanggal_modal = Carbon::now();
                $modal->jumlah_modal = $request->total;
                $modal->status_modal = 'Terima';
                $modal->id_pegawai = Auth::user()->id;
                $modal->riwayat_modal = $request->total;
                $modal->total_modal_backup = $request->total;
                $modal->jenis_modal = 'Modal Awal';
                $modal->save();

                $transaksi->id_modal = $modal->id_modal;
                $transaksi->update();
            } else {
                $perhitungan = $modal->riwayat_modal + $request->total;
                $modal->riwayat_modal = $perhitungan;
                $modal->save();
            }

            DB::commit();
            Alert::success('Berhasil', 'Data Transaksi Berhasil Ditambahkan');
            return $transaksi->id_transaksi;
        } catch (\Throwable $th) {
            DB::rollBack();
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
        $transaksi = Transaksi::with('detailTransaksi.Currency')->find($id);
        $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Terima')->first();
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');

        return view('pages.TransaksiJual.edit', compact('transaksi', 'currency', 'modal', 'today', 'today_format'));
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
            DB::beginTransaction();
            $transaksi = Transaksi::find($id);
            $new_log = new LogEdit();
            $new_log->id_pegawai = Auth::user()->id;
            $new_log->id_modal = $transaksi->id_modal;
            $new_log->tanggal_transaksi = $transaksi->tanggal_transaksi;
            $new_log->kode_transaksi = $transaksi->kode_transaksi;
            $new_log->total = $transaksi->total;
            $new_log->jenis_log = 'Edit';
            $new_log->keterangan_log = $request->keterangan_log;
            $new_log->save();

            $getDetail = DetailTransaksi::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($getDetail as $item) {
                $new_det_log = new LogEditDetail();
                $new_det_log->id_log = $new_log->id_log;
                $new_det_log->currency_id = $item->currency_id;
                $new_det_log->jumlah_currency = $item->jumlah_currency;
                $new_det_log->jumlah_tukar = $item->jumlah_tukar;
                $new_det_log->total_tukar = $item->total_tukar;
                $new_det_log->save();

                $cry = MasterCurrency::where('id_currency', $item['currency_id'])->first();
                if ($cry) {
                    // Decrement jumlah_valas
                    $cry->last_nilai_jual = $item->jumlah_currency;
                    $cry->jumlah_valas -= $item['jumlah_tukar'];
                    $cry->update();
                }
            }

            $transaksi->total = $request->total;
            $transaksi->nama_customer = $request->nama_customer;
            $transaksi->nomor_passport = $request->nomor_passport;
            $transaksi->negara_asal = $request->asal_negara;
            $transaksi->id_pegawai = Auth::user()->id;
            $transaksi->save();

            $transaksi->detailTransaksi()->delete();
            $transaksi->detailTransaksi()->insert($request->detail);

            foreach ($request->detail as $key) {
                $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->where('id_currency', $key['currency_id'])->first();
                if (empty($jurnal)) {
                    $jurnal = new Jurnal();
                    $jurnal->id_transaksi = $transaksi->id_transaksi;
                    $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                    $jurnal->id_currency = $key['currency_id'];
                    $jurnal->kurs = $key['jumlah_currency'];
                    $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                    $jurnal->total_tukar = $key['total_tukar'];
                    $jurnal->jenis_jurnal = 'Kredit Jual';
                    $jurnal->id_pegawai = Auth::user()->id;
                    $jurnal->save();
                } else {
                    $jurnal->id_transaksi = $transaksi->id_transaksi;
                    $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                    $jurnal->id_currency = $key['currency_id'];
                    $jurnal->kurs = $key['jumlah_currency'];
                    $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                    $jurnal->total_tukar = $key['total_tukar'];
                    $jurnal->jenis_jurnal = 'Kredit Jual';
                    $jurnal->id_pegawai = Auth::user()->id;
                    $jurnal->save();
                }

                $cry = MasterCurrency::where('id_currency', $key['currency_id'])->first();
                if ($cry) {
                    $cry->jumlah_valas += $key['jumlah_tukar'];
                    $cry->update();
                }
            }
            $modal = ModalTransaksi::find($request->id_modal);
            $modal->riwayat_modal = $request->jumlah_modal;
            $modal->save();
            DB::commit();

            Alert::success('Berhasil', 'Data Transaksi Jual Berhasil Diupdate');
            return $request;
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
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
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $transaksi = Transaksi::find($request->transaksi_id);
            $log = new LogEdit;
            $log->id_pegawai = $transaksi->id_pegawai;
            $log->id_modal = $transaksi->id_modal;
            $log->jenis_log = 'Delete';
            $log->keterangan_log = $request->keterangan_log;
            $log->tanggal_transaksi = $transaksi->tanggal_transaksi;
            $log->kode_transaksi = $transaksi->kode_transaksi;
            $log->total = $transaksi->total;
            $log->save();

            $getDetail = DetailTransaksi::where('id_transaksi', $request->transaksi_id)->get();
            foreach ($getDetail as $item) {
                $new_det_log = new LogEditDetail();
                $new_det_log->id_log = $log->id_log;
                $new_det_log->currency_id = $item->currency_id;
                $new_det_log->jumlah_currency = $item->jumlah_currency;
                $new_det_log->jumlah_tukar = $item->jumlah_tukar;
                $new_det_log->total_tukar = $item->total_tukar;
                $new_det_log->save();

                $cry = MasterCurrency::where('id_currency', $item['currency_id'])->first();
                if ($cry) {
                    // Increment jumlah_valas
                    $cry->jumlah_valas += $item['jumlah_tukar'];
                    $cry->update();
                }
            }

            $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($jurnal as $tes) {
                $tes->delete();
            }
            $detail = DetailTransaksi::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($detail as $s) {
                $s->delete();
            }
            $modal = ModalTransaksi::where('id_modal', $transaksi->id_modal)->first();
            $perhitungan = $modal->riwayat_modal + $transaksi->total;
            $modal->riwayat_modal = $perhitungan;
            $modal->save();
            $transaksi->delete();
            DB::commit();
            Alert::success('Berhasil', 'Data Transaksi Berhasil Terhapus');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }
}
