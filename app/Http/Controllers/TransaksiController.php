<?php

namespace App\Http\Controllers;

use App\Exports\ExcelHarian;
use App\Exports\ExcelHarianOwner;
use App\Exports\ExcelHarianView;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\LogEdit;
use App\Models\LogEditDetail;
use App\Models\MasterCurrency;
use App\Models\MasterCabang;
use App\Models\MasterCustomer;
use App\Models\MasterThreshold;
use App\Models\MasterLimitTransaksi;
use App\Models\MasterTerduga;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function allowedCabangIds($isOwner = null)
    {
        $isOwner = $isOwner === null ? Auth::user()->role === 'Owner' : $isOwner;
        if ($isOwner) {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->map(function ($id) {
                return (int) $id;
            })->all();
        }

        $sessionCabangs = array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
        $activeCabang = session('cabang_aktif');
        if ($activeCabang) {
            $sessionCabangs = [(int) $activeCabang];
        }

        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $sessionCabangs)
            ->pluck('cabang_id')
            ->map(function ($id) {
                return (int) $id;
            })->all();
    }

    private function resolveCabangId(Request $request, $isOwner, $required = false)
    {
        $allowedCabangIds = $this->allowedCabangIds($isOwner);
        $cabangId = $isOwner
            ? ($request->input('cabang_id') ?: session('cabang_aktif'))
            : session('cabang_aktif');

        if ($cabangId === null || $cabangId === '') {
            if ($required) {
                abort(422, 'Cabang wajib dipilih.');
            }
            return null;
        }

        abort_unless(in_array((int) $cabangId, $allowedCabangIds, true), 403, 'Cabang tidak valid.');
        return (int) $cabangId;
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
            $selectedCabangId = $this->resolveCabangId($request, !$isPegawai);

            // Get pagination size from frontend (default to 10)
            $perPage = $request->input('per_page', 10);

            $transaksiQuery = Transaksi::with('Cabang')
                ->where('tanggal_transaksi', $today)
                ->where('jenis_transaksi', 'Beli')
                ->orderBy('updated_at', 'DESC');

            $jurnalQuery = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                ->where('tanggal_jurnal', $today);

            $jurnalQuery2 = clone $jurnalQuery; // Clone the query to reuse later

            if ($isPegawai) {
                $transaksiQuery->where('id_pegawai', $user->id);
                $jurnalQuery->where('id_pegawai', $user->id);
                $jurnalQuery2->where('id_pegawai', $user->id);
            }
            if ($selectedCabangId) {
                $transaksiQuery->where('tb_transaksi.cabang_id', $selectedCabangId);
                $jurnalQuery->where('tb_jurnal.cabang_id', $selectedCabangId);
                $jurnalQuery2->where('tb_jurnal.cabang_id', $selectedCabangId);
            }

            // Paginate transactions
            $transaksi = $transaksiQuery->paginate($perPage)->withQueryString();
            $count = $transaksi->total();
            $total_transaksi = $transaksiQuery->sum('total');
            $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();

            $report = $jurnalQuery->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis')
                ->where('jenis_jurnal', 'Debit')
                ->groupBy('nama_currency', 'kurs', 'jenis_kurs')
                ->get();

            $valas = $jurnalQuery2->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total')
                ->where('jenis_jurnal', 'Debit')
                ->groupBy('nama_currency', 'jenis_kurs')
                ->get();

            if (!$isPegawai) {
                $pegawai = User::where('role', '!=', 'Owner')->get();

                if ($request->filterData) {
                    $report = $jurnalQuery->where('id_pegawai', $request->filterData)
                        ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis, id_pegawai as user')
                        ->where('jenis_jurnal', 'Debit')
                        ->groupBy('nama_currency', 'jenis_kurs', 'kurs', 'id_pegawai')
                        ->get();

                    $valas = $jurnalQuery2->where('id_pegawai', $request->filterData)
                        ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total, id_pegawai as user')
                        ->where('jenis_jurnal', 'Debit')
                        ->groupBy('nama_currency', 'jenis_kurs', 'id_pegawai')
                        ->get();
                }

                $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name']);
                return view('pages.transaksi.owner', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'pegawai', 'report', 'cabang'));
            }

            $cabang = MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name']);
            return view('pages.transaksi.index', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'report', 'cabang'));

        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }


    public function getkurs($id_currency)
    {
        try {
            $kurs = MasterCurrency::where('id_currency', '=', $id_currency)->pluck('nilai_kurs');
            return json_encode($kurs);
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function getkursedit($id_currency)
    {
        try {
            $kurs = MasterCurrency::where('id_currency', '=', $id_currency)->pluck('nilai_kurs');
            return json_encode($kurs);
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }

    public function Export_dokumen(Request $request)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);

        try {
            if (Auth::user()->role != 'Owner') {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')->where('id_pegawai', Auth::user()->id);
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->filled('cabang_id')) {
                    $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Beli')->where('tanggal_transaksi', Carbon::today())->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');
                // return $transaksi;

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        return $pdf->download('report-harian ' . $today . ' ' . Auth::user()->name . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarian($transaksi);
                    }
                }
            } else {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')
                    ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->OrderBy('tb_transaksi.updated_at');
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->id_pegawai) {
                    $transaksi->where('id_pegawai', $request->id_pegawai);
                }
                if ($request->filled('cabang_id')) {
                    $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Beli')->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian-owner', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        if ($request->id_pegawai) {
                            return $pdf->download('report-harian ' . $today . ' ' . $transaksi[0]->Pegawai->name . ' .pdf');
                        }
                        return $pdf->download('report-harian ' . $today . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarianOwner($transaksi);
                    }
                }
            }
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function Export_dokumen_jual(Request $request)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);

        try {
            if (Auth::user()->role != 'Owner') {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')->where('id_pegawai', Auth::user()->id);
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->filled('cabang_id')) {
                    $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Jual')->where('tanggal_transaksi', Carbon::today())->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');
                // return $transaksi;

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        return $pdf->download('report-harian ' . $today . ' ' . Auth::user()->name . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarian($transaksi);
                    }
                }
            } else {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')
                    ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->OrderBy('tb_transaksi.updated_at');
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->id_pegawai) {
                    $transaksi->where('id_pegawai', $request->id_pegawai);
                }
                if ($request->filled('cabang_id')) {
                    $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Jual')->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian-owner', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        if ($request->id_pegawai) {
                            return $pdf->download('report-harian ' . $today . ' ' . $transaksi[0]->Pegawai->name . ' .pdf');
                        }
                        return $pdf->download('report-harian ' . $today . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarianOwner($transaksi);
                    }
                }
            }
        } catch (\Throwable $th) {
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
        $isOwner = Auth::user()->role === 'Owner';
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);
        $selectedCabangId = $this->resolveCabangId($request, $isOwner);
        $cabangs = MasterCabang::whereIn('cabang_id', $this->allowedCabangIds($isOwner))
            ->where('is_active', 1)
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
        if ($isOwner && !$selectedCabangId && $cabangs->isNotEmpty()) {
            $selectedCabangId = (int) $cabangs->first()->cabang_id;
        }
        $customers = MasterCustomer::where('is_active', 1)
            ->when($selectedCabangId, function ($query) use ($selectedCabangId) {
                $query->where('cabang_terdaftar', $selectedCabangId);
            })
            ->when(!$selectedCabangId && !$isOwner, function ($query) {
                $query->where('cabang_terdaftar', session('cabang_aktif'));
            })
            ->orderBy('name')
            ->get(['customer_id', 'name', 'alias', 'country', 'passport', 'nik', 'alamat', 'cabang_terdaftar']);

        $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
        $tesQuery = $isOwner ? ModalTransaksi::withoutGlobalScope('cabang') : ModalTransaksi::query();
        $tes = $selectedCabangId
            ? (clone $tesQuery)->where('cabang_id', $selectedCabangId)->where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first()
            : null;
        if ($selectedCabangId && empty($tes)) {
            Alert::warning('Belum Mengajukan Modal', 'Anda Belum Mengajukan Modal');
            return redirect()->route('modal.index', ['cabang_id' => $selectedCabangId]);
        } elseif ($selectedCabangId && $tes->status_modal == 'Pending') {
                Alert::warning('Modal Diproses, Mohon Menunggu', 'Pengajuan Modal Anda Hari Ini Belum Diproses oleh Owner');
                return redirect()->route('modal.index', ['cabang_id' => $selectedCabangId]);
        } elseif ($selectedCabangId && $tes->status_modal == 'Tolak') {
                Alert::warning('Modal Ditolak', 'Pengajuan Modal Anda Hari Ini Ditolak oleh Owner, Edit Data Modal');
                return redirect()->route('modal.index', ['cabang_id' => $selectedCabangId]);
        } elseif ($selectedCabangId) {
            $modal = (clone $tesQuery)->where('cabang_id', $selectedCabangId)->where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Terima')->first();
        }
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');
        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
        // $id = Transaksi::getId();
        $id = Transaksi::getIdBeli();
        foreach ($id as $value);
        $idlama = $value->id_transaksi;
        $idbaru = $idlama + 1;
        $blt = date('ymd');
        $id = Auth::user()->id;
        $kode_transaksi = 'RV' . $blt . '-' . $idbaru;
        $countries = json_decode(file_get_contents(base_path('countries.json')), true) ?: [];
        asort($countries);

        return view('pages.transaksi.create', compact(
            'currency', 'modal', 'today', 'kode_transaksi', 'today_format', 'idbaru',
            'jumlah_transaksi', 'total_transaksi', 'countries', 'cabangs', 'customers',
            'selectedCabangId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function activePassportThreshold($date)
    {
        return MasterThreshold::where('is_active', 1)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->orderByDesc('threshold_id')
            ->first();
    }

    private function passportThresholdResult($passport, $total = 0, $date = null)
    {
        $passport = mb_strtolower(trim((string) $passport));
        $total = (float) $total;
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        $dateStr = $targetDate->toDateString();

        // Ambil batas dari Master Data Batas Transaksi Customer (tb_master_limit_transaksi)
        $activeRule = MasterLimitTransaksi::getActiveLimit();
        $isRuleActive = $activeRule ? (bool) $activeRule->is_active : true;
        $periodeHari = $activeRule && $activeRule->periode_hari > 0 ? (int) $activeRule->periode_hari : 30;

        if ($activeRule && $activeRule->use_live_kurs_usd) {
            $usdCurrency = MasterCurrency::where('nama_currency', 'LIKE', '%USD%')->first();
            $usdRate = $usdCurrency && (float) $usdCurrency->nilai_kurs > 0 ? (float) $usdCurrency->nilai_kurs : 18000;
            $limit = (float) (($activeRule->ekuivalen_usd ?: 10000) * $usdRate);
        } elseif ($activeRule && $activeRule->nominal_limit_idr > 0) {
            $limit = (float) $activeRule->nominal_limit_idr;
        } else {
            // Default BI Threshold: Setara USD 10.000 (Rp 180.000.000)
            $limit = 180000000;
        }

        if ($passport === '') {
            return [
                'exceeded' => false,
                'reason' => 'passport_empty',
                'accumulated' => 0,
                'accumulated_this_month' => 0,
                'projected' => $total,
                'limit' => $limit,
                'remaining' => $limit,
                'threshold' => null,
            ];
        }

        // Akumulasi rolling sesuai periode_hari dari master data (default 30 hari)
        $accumulated = (float) Transaksi::whereRaw('LOWER(TRIM(nomor_passport)) = ?', [$passport])
            ->whereDate('tanggal_transaksi', '>=', $targetDate->copy()->subDays($periodeHari - 1)->toDateString())
            ->whereDate('tanggal_transaksi', '<=', $dateStr)
            ->sum('total');

        // Akumulasi bulan berjalan (bulan ini)
        $accumulatedThisMonth = (float) Transaksi::whereRaw('LOWER(TRIM(nomor_passport)) = ?', [$passport])
            ->whereYear('tanggal_transaksi', $targetDate->year)
            ->whereMonth('tanggal_transaksi', $targetDate->month)
            ->sum('total');

        $projected = $accumulated + $total;
        $remaining = max(0, $limit - $accumulated);

        // Jika aturan dinonaktifkan oleh Owner, transaksi tidak akan terblokir
        $exceeded = $isRuleActive && ($projected > $limit);

        return [
            'exceeded' => $exceeded,
            'reason' => null,
            'accumulated' => $accumulated,
            'accumulated_this_month' => $accumulatedThisMonth,
            'projected' => $projected,
            'limit' => $limit,
            'remaining' => $remaining,
            'threshold' => null,
            'rule' => $activeRule,
        ];
    }

    public function passportThreshold(Request $request)
    {
        $data = $request->validate([
            'nomor_passport' => 'nullable|string|max:100',
            'total' => 'nullable|numeric|min:0',
            'tanggal_transaksi' => 'nullable|date',
        ]);
        $result = $this->passportThresholdResult(
            $data['nomor_passport'] ?? null,
            $data['total'] ?? 0,
            $data['tanggal_transaksi'] ?? null
        );

        $limit = (float) $result['limit'];
        $accumulated = (float) $result['accumulated'];
        $accumulatedThisMonth = (float) $result['accumulated_this_month'];

        return response()->json([
            'exceeded' => $result['exceeded'],
            'reason' => $result['reason'],
            'accumulated' => $accumulated,
            'accumulated_formatted' => 'Rp ' . number_format($accumulated, 0, ',', '.'),
            'accumulated_this_month' => $accumulatedThisMonth,
            'accumulated_this_month_formatted' => 'Rp ' . number_format($accumulatedThisMonth, 0, ',', '.'),
            'projected' => $result['projected'],
            'projected_formatted' => 'Rp ' . number_format($result['projected'], 0, ',', '.'),
            'limit' => $limit,
            'limit_formatted' => 'Rp ' . number_format($limit, 0, ',', '.'),
            'remaining' => $result['remaining'],
            'remaining_formatted' => 'Rp ' . number_format($result['remaining'], 0, ',', '.'),
            'percentage' => $limit > 0 ? min(100, round(($accumulated / $limit) * 100, 1)) : 0,
            'percentage_this_month' => $limit > 0 ? min(100, round(($accumulatedThisMonth / $limit) * 100, 1)) : 0,
            'threshold' => null,
            'rule_name' => optional($result['rule'])->nama_aturan,
            'periode_hari' => optional($result['rule'])->periode_hari ?: 30,
        ]);
    }

    public function store(Request $request)
    {
        $passportResult = $this->passportThresholdResult(
            $request->nomor_passport,
            $request->total,
            $request->tanggal_transaksi
        );
        if ($passportResult['exceeded']) {
            $documentValidator = Validator::make($request->all(), [
                'supporting_document_type' => 'required|string|max:100',
                'supporting_document_number' => 'required|string|max:100',
                'supporting_document_date' => 'required|date',
                'supporting_document_note' => 'required|string|max:1000',
                'supporting_document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);
            if ($documentValidator->fails()) {
                return response()->json([
                    'message' => 'Dokumen pendukung wajib diisi karena akumulasi passport melewati batas 30 hari.',
                    'requires_supporting_document' => true,
                    'errors' => $documentValidator->errors(),
                ], 422);
            }
        }

        try {
            DB::beginTransaction();
            $isOwner = Auth::user()->role === 'Owner';
            $request->validate([
                'cabang_id' => 'required|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $transactionCabangId = $this->resolveCabangId($request, $isOwner, true);
            $customer = $request->customer_id ? MasterCustomer::findOrFail($request->customer_id) : null;
            if ($customer && (int) $customer->cabang_terdaftar !== $transactionCabangId) {
                DB::rollBack();
                return response()->json(['message' => 'Customer tidak sesuai dengan cabang transaksi.'], 422);
            }
            if (!$customer && !trim((string) $request->nama_customer)) {
                DB::rollBack();
                return response()->json(['message' => 'Customer wajib dipilih.'], 422);
            }
            $screeningTerms = collect([$customer ? $customer->name : $request->nama_customer, $customer ? $customer->alias : $request->customer_alias])
                ->filter()
                ->flatMap(function ($value) {
                    return preg_split('/\s*;\s*/', $value);
                })
                ->map(function ($term) {
                    return preg_replace('/\s+/', ' ', trim($term));
                })
                ->filter(function ($term) {
                    return mb_strlen($term) >= 2;
                })
                ->unique(function ($term) {
                    return mb_strtolower($term);
                })
                ->values()
                ->all();
            $screening = MasterTerduga::query()
                ->where(function ($query) use ($screeningTerms) {
                    foreach ($screeningTerms as $term) {
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
                })
                ->exists();
            if ($screening && $request->input('screening_confirmed') !== '1') {
                DB::rollBack();
                return response()->json([
                    'message' => 'Customer masuk daftar terduga dan membutuhkan konfirmasi.',
                    'requires_screening_confirmation' => true,
                ], 422);
            }
            $kodeTransaksi = $request->kode_transaksi;
            if (!$kodeTransaksi || Transaksi::where('kode_transaksi', $kodeTransaksi)->exists()) {
                $last = Transaksi::orderBy('id_transaksi', 'desc')->first();
                $nextId = ($last ? $last->id_transaksi : 0) + 1;
                $kodeTransaksi = 'RV' . date('ymd') . '-' . $nextId;
            }
            $transaksi = new Transaksi();
            $transaksi->kode_transaksi = $kodeTransaksi;
            $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
            $transaksi->id_modal = $request->id_modal;
            $transaksi->total = $request->total;
            $transaksi->id_pegawai = Auth::user()->id;
            $transaksi->nama_customer = $request->nama_customer ?: ($customer ? $customer->name : null);
            $transaksi->nomor_passport = $request->nomor_passport ?: ($customer ? $customer->passport : null);
            $transaksi->negara_asal = $request->asal_negara ?: ($customer ? $customer->country : null);
            $transaksi->jenis_transaksi = 'Beli';
            $transaksi->cabang_id = $transactionCabangId;
            $transaksi->supporting_document_type = $request->supporting_document_type;
            $transaksi->supporting_document_number = $request->supporting_document_number;
            $transaksi->supporting_document_date = $request->supporting_document_date;
            $transaksi->supporting_document_note = $request->supporting_document_note;
            if ($request->hasFile('supporting_document_file')) {
                $transaksi->supporting_document_file = $request->file('supporting_document_file')
                    ->store('transaksi/dokumen', 'public');
            }
            $transaksi->save();
            $transaksi->healCustomerData();

            // Bersihkan baris detail/jurnal "yatim" yang kebetulan memakai id_transaksi baru ini
            // (sisa data lama / transaksi gagal) agar valas transaksi sebelumnya tidak ikut terbawa.
            DetailTransaksi::withoutGlobalScopes()->where('id_transaksi', $transaksi->id_transaksi)->delete();
            Jurnal::withoutGlobalScopes()->where('id_transaksi', $transaksi->id_transaksi)->delete();

            // Hanya simpan detail milik transaksi ini (unik per currency) & hitung ulang total di server
            $details = collect($request->detail ?: [])
                ->filter(function ($item) {
                    return !empty($item['currency_id']) && (float) ($item['jumlah_tukar'] ?? 0) > 0;
                })
                ->unique('currency_id')
                ->values();
            if ($details->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'Detail transaksi kosong.'], 422);
            }
            $serverTotal = round($details->sum(function ($item) {
                return (float) $item['total_tukar'];
            }), 2);
            if (abs($serverTotal - (float) $transaksi->total) > 0.01) {
                $transaksi->total = $serverTotal;
                $transaksi->save();
                $request->merge(['total' => $serverTotal]);
            }

            // $transaksi->detailTransaksi()->insert($request->detail);
            foreach ($details as $key) {
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
                $jurnal->jenis_jurnal = 'Debit';
                $jurnal->id_pegawai = Auth::user()->id;
                $jurnal->cabang_id = $transaksi->cabang_id;
                $jurnal->save();

                $cry = MasterCurrency::where('id_currency', $key['currency_id'])->first();
                if ($cry) {
                    $cry->jumlah_valas += $key['jumlah_tukar'];
                    $cry->update();
                }
            }

            $modal = ModalTransaksi::find($request->id_modal);
            $perhitungan = $modal->riwayat_modal - $request->total;
            $modal->riwayat_modal = $perhitungan;
            $modal->save();
            DB::commit();
            Alert::success('Berhasil', 'Data Transaksi Berhasil Ditambahkan');
            return $transaksi;
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
        $transaksi = Transaksi::with('Pegawai', 'Cabang', 'detailTransaksi.Currency')->find($id);
        if (!$transaksi) {
            Alert::warning('Error', 'Transaksi tidak ditemukan');
            return redirect()->back();
        }

        // Auto-heal jika customer di database belum lengkap
        $transaksi->healCustomerData();

        $detail = DetailTransaksi::where('id_transaksi', $id)->get();
        return view('pages.transaksi.detail', compact('transaksi','detail'));
    }

    public function downloadDokumen($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        if (empty($transaksi->supporting_document_file)) {
            Alert::warning('Perhatian', 'Dokumen pendukung tidak ditemukan.');
            return redirect()->back();
        }

        $path = $transaksi->supporting_document_file;
        if (!Storage::disk('public')->exists($path)) {
            Alert::warning('Perhatian', 'File dokumen tidak ditemukan di penyimpanan server.');
            return redirect()->back();
        }

        return Storage::disk('public')->response($path);
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
        $currency = MasterCurrency::orderBy('jenis_kurs','ASC')->get();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');
        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');

        // Customer aktif sesuai cabang transaksi untuk dropdown searchable.
        $customerCabangId = $transaksi->cabang_id ?: session('cabang_aktif');
        $customers = MasterCustomer::where('is_active', 1)
            ->when($customerCabangId, function ($query) use ($customerCabangId) {
                $query->where('cabang_terdaftar', $customerCabangId);
            })
            ->orderBy('name')
            ->get(['customer_id', 'name', 'alias', 'country', 'passport', 'nik']);
        $selectedCustomer = $customers->first(function ($customer) use ($transaksi) {
            return $customer->name === $transaksi->nama_customer
                && (!$transaksi->nomor_passport || (string) $customer->passport === (string) $transaksi->nomor_passport);
        });
        $selectedCustomerId = $selectedCustomer ? $selectedCustomer->customer_id : null;

        return view('pages.transaksi.edit', compact('transaksi','currency','modal','today','today_format','jumlah_transaksi','total_transaksi','customers','selectedCustomerId'));
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
                    $cry->jumlah_valas += $item['jumlah_tukar'];
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
                    $jurnal->jenis_jurnal = 'Debit';
                    $jurnal->id_pegawai = Auth::user()->id;
                    $jurnal->save();
                } else {
                    $jurnal->id_transaksi = $transaksi->id_transaksi;
                    $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                    $jurnal->id_currency = $key['currency_id'];
                    $jurnal->kurs = $key['jumlah_currency'];
                    $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                    $jurnal->total_tukar = $key['total_tukar'];
                    $jurnal->jenis_jurnal = 'Debit';
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

            Alert::success('Berhasil', 'Data Transaksi Berhasil Diedit');
            return $request;
        } catch (\Throwable $th) {
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
    public function destroy($id)
    {
        //
    }

    /**
     * Validasi terduga dari nama dan alias.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateTerduga(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:1000',
        ]);

        $nama = $request->input('nama');

        // Pisahkan dengan delimiter ; (mengikuti logika di method store)
        $screeningTerms = collect([$nama])
            ->filter()
            ->flatMap(function ($value) {
                return preg_split('/\s*;\s*/', $value);
            })
            ->map(function ($term) {
                return preg_replace('/\s+/', ' ', trim($term));
            })
            ->filter(function ($term) {
                return mb_strlen($term) >= 2;
            })
            ->unique(function ($term) {
                return mb_strtolower($term);
            })
            ->values()
            ->all();

        // Cek di tabel terduga ke name dan alias
        $screening = MasterTerduga::query()
            ->where(function ($query) use ($screeningTerms) {
                foreach ($screeningTerms as $term) {
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
            })
            ->exists();

        return response()->json([
            'terduga' => $screening,
            'message' => $screening ? 'Nama atau alias ditemukan dalam daftar terduga.' : 'Tidak ditemukan dalam daftar terduga.',
            'search_terms' => $screeningTerms,
        ]);
    }

    public function hapus(Request $request)
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
                    if ($transaksi->jenis_transaksi  == 'Jual') {
                        $cry->jumlah_valas += $item['jumlah_tukar'];
                    } else {
                        $cry->jumlah_valas -= $item['jumlah_tukar'];
                    }
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
