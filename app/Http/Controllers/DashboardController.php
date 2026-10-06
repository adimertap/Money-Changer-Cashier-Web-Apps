<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use App\Models\MasterCurrency;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $isOwner = $user->role === 'Owner';
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $cabangId = $isOwner ? $request->input('cabang_id') : session('cabang_aktif');
            $allowedCabangIds = $isOwner
                ? MasterCabang::where('is_active', 1)->pluck('cabang_id')->all()
                : array_column(session('cabangs', []), 'cabang_id');
            if ($cabangId !== null && $cabangId !== '' && !in_array((int) $cabangId, array_map('intval', $allowedCabangIds), true)) {
                abort(403);
            }
            $cabangs = $isOwner
                ? MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name'])
                : collect();
            $branchFilter = function ($query) use ($cabangId, $isOwner) {
                if ($isOwner) {
                    $query->withoutGlobalScope('cabang');
                }
                if ($cabangId) {
                    $query->where($query->getModel()->getTable() . '.cabang_id', $cabangId);
                }
                return $query;
            };

            $month = Carbon::now()->format('m');
            $bulan_ini = Carbon::now()->format('M Y');
            $currency = MasterCurrency::count();
            if ($isOwner) {
                $pegawai = User::count();
            } else {
                $pegawai = User::diCabangAktif()->count();
            }
            $currentMonth = date('m');
            $currentYear = date('Y');
            $todayDate = Carbon::now()->format('Y-m-d');

            // Query helper untuk aggregate
            $branchFilter = function ($query) use ($isOwner) {
                if ($isOwner) {
                    $query->withoutGlobalScope('cabang');
                }
                return $query;
            };

            // TODAY (Beli)
            $jumlah_hari_ini = $branchFilter(Transaksi::where('tanggal_transaksi', $todayDate)->where('jenis_transaksi','Beli'))->count();
            $total_hari_ini = $branchFilter(Transaksi::where('tanggal_transaksi', $todayDate)->where('jenis_transaksi','Beli'))->sum('total');
            $jumlah_jual_hari_ini = $branchFilter(Transaksi::where('tanggal_transaksi', $todayDate)->where('jenis_transaksi','Jual'))->count();
            $total_jual_hari_ini = $branchFilter(Transaksi::where('tanggal_transaksi', $todayDate)->where('jenis_transaksi','Jual'))->sum('total');

            // MODAL
            $modal = $branchFilter(ModalTransaksi::where('tanggal_modal', $todayDate)->where('status_modal', 'Pending'))->count();
            $sisa_modal = $branchFilter(ModalTransaksi::where('tanggal_modal', $todayDate)->where('status_modal', 'Terima'))->first();
            $total_modal_bulan_ini = $branchFilter(ModalTransaksi::whereMonth('tanggal_modal', $currentMonth)->whereYear('tanggal_modal', $currentYear))->sum('jumlah_modal');

            // BULAN INI & BULAN LALU (COMPARISON)
            $lastMonthDate = Carbon::now()->subMonth();
            $lastMonth = $lastMonthDate->format('m');
            $lastMonthYear = $lastMonthDate->format('Y');

            $jumlah_bulan_ini = $branchFilter(Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->where('jenis_transaksi','Beli'))
                ->count();
            $total_bulan_ini = $branchFilter(Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->where('jenis_transaksi','Beli'))
                ->sum('total');
            $total_bulan_lalu = $branchFilter(Transaksi::whereMonth('tanggal_transaksi', $lastMonth)
                ->whereYear('tanggal_transaksi', $lastMonthYear)
                ->where('jenis_transaksi','Beli'))
                ->sum('total');
            $diff_total_bulan = 0;
            if ($total_bulan_lalu > 0) {
                $diff_total_bulan = (($total_bulan_ini - $total_bulan_lalu) / $total_bulan_lalu) * 100;
            } elseif ($total_bulan_ini > 0) {
                $diff_total_bulan = 100;
            }

            $jumlah_jual_bulan_ini = $branchFilter(Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->where('jenis_transaksi','Jual'))
                ->count();
            $total_jual_bulan_ini = $branchFilter(Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->where('jenis_transaksi','Jual'))
                ->sum('total');

            // SEMUA
            $jumlah_seluruh = $branchFilter(Transaksi::where('tanggal_transaksi', $todayDate))->count();

            $transaksi = $branchFilter(Transaksi::with('detailTransaksi', 'Cabang')
                ->where('tanggal_transaksi', $todayDate))
                ->orderBy('created_at', 'DESC')
                ->take(5)->get();

            // Ringkasan per cabang langsung untuk Owner
            $cabangStats = collect();
            $approvalModal = collect();
            if ($isOwner) {
                $cabangStats = MasterCabang::where('is_active', 1)
                    ->orderBy('cabang_name')
                    ->get()
                    ->map(function ($cabang) use ($todayDate, $currentMonth, $currentYear, $lastMonth, $lastMonthYear) {
                        $cId = $cabang->cabang_id;
                        $trxToday = Transaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->where('tanggal_transaksi', $todayDate)
                            ->where('jenis_transaksi', 'Beli');
                        $trxMonth = Transaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->whereMonth('tanggal_transaksi', $currentMonth)
                            ->whereYear('tanggal_transaksi', $currentYear)
                            ->where('jenis_transaksi', 'Beli');
                        $trxLastMonth = Transaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->whereMonth('tanggal_transaksi', $lastMonth)
                            ->whereYear('tanggal_transaksi', $lastMonthYear)
                            ->where('jenis_transaksi', 'Beli');
                        $totalBulanBranch = (clone $trxMonth)->sum('total');
                        $totalBulanLaluBranch = (clone $trxLastMonth)->sum('total');
                        $diffBulanBranch = 0;
                        if ($totalBulanLaluBranch > 0) {
                            $diffBulanBranch = (($totalBulanBranch - $totalBulanLaluBranch) / $totalBulanLaluBranch) * 100;
                        } elseif ($totalBulanBranch > 0) {
                            $diffBulanBranch = 100;
                        }

                        $trxJualMonth = Transaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->whereMonth('tanggal_transaksi', $currentMonth)
                            ->whereYear('tanggal_transaksi', $currentYear)
                            ->where('jenis_transaksi', 'Jual');
                        $modalToday = ModalTransaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->where('tanggal_modal', $todayDate)
                            ->where('status_modal', 'Terima')
                            ->first();
                        $totalModalBulan = ModalTransaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->whereMonth('tanggal_modal', $currentMonth)
                            ->whereYear('tanggal_modal', $currentYear)
                            ->sum('jumlah_modal');
                        $pegawaiCount = User::whereHas('cabangs', function ($q) use ($cId) {
                            $q->where('tb_master_cabang.cabang_id', $cId);
                        })->count();
                        $pengajuanPendingCount = ModalTransaksi::withoutGlobalScope('cabang')
                            ->where('cabang_id', $cId)
                            ->where('status_modal', 'Pending')
                            ->count();

                        return (object) [
                            'cabang_id' => $cId,
                            'cabang_name' => $cabang->cabang_name,
                            'lokasi' => $cabang->lokasi ?? '-',
                            'jumlah_hari_ini' => (clone $trxToday)->count(),
                            'total_hari_ini' => (clone $trxToday)->sum('total'),
                            'jumlah_bulan_ini' => (clone $trxMonth)->count(),
                            'total_bulan_ini' => $totalBulanBranch,
                            'total_bulan_lalu' => (float) $totalBulanLaluBranch,
                            'diff_bulan' => round($diffBulanBranch, 1),
                            'jumlah_jual_bulan_ini' => (clone $trxJualMonth)->count(),
                            'total_jual_bulan_ini' => (clone $trxJualMonth)->sum('total'),
                            'sisa_modal' => $modalToday ? (float) $modalToday->riwayat_modal : null,
                            'modal_awal' => $modalToday ? (float) $modalToday->total_modal_backup : null,
                            'total_modal_terpakai_bulan_ini' => (float) $totalModalBulan,
                            'pegawai_count' => $pegawaiCount,
                            'pengajuan_pending_count' => $pengajuanPendingCount,
                        ];
                    });

                $approvalModal = ModalTransaksi::withoutGlobalScope('cabang')
                    ->with(['Pegawai', 'Cabang'])
                    ->where('status_modal', 'Pending')
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();
            }

            // PEGAWAI
            $pegawai_money_today_total = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', $todayDate)
                ->where('jenis_transaksi','Beli')
                ->sum('total');
            $pegawai_money_today_total_jual = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', $todayDate)
                ->where('jenis_transaksi','Jual')
                ->sum('total');

            // BARIS 2
            $pegawai_count_money_today = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', $todayDate)
                ->where('jenis_transaksi','Beli')
                ->count();
            $pegawai_count_money_today_jual = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', $todayDate)
                ->where('jenis_transaksi','Jual')
                ->count();

            $pegawai_sum_money_bulan = Transaksi::where('id_pegawai', Auth::user()->id)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->whereMonth('tanggal_transaksi', $currentMonth)
                ->where('jenis_transaksi','Beli')
                ->sum('total');

            $pegawai_sum_money_bulan_jual = Transaksi::where('id_pegawai', Auth::user()->id)
                ->whereYear('tanggal_transaksi', $currentYear)
                ->whereMonth('tanggal_transaksi', $currentMonth)
                ->where('jenis_transaksi','Jual')
                ->sum('total');

            $transaksi_pegawai_money = Transaksi::with('detailTransaksi', 'Cabang')->where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', $todayDate)
                ->orderBy('created_at', 'DESC')
                ->take(5)->get();

            return view('pages.dashboard.dashboard', compact(
                'jumlah_hari_ini',
                'pegawai_money_today_total_jual',
                'pegawai_count_money_today_jual',
                'pegawai_sum_money_bulan_jual',
                'jumlah_seluruh',
                'jumlah_jual_hari_ini',
                'total_hari_ini',
                'total_jual_hari_ini',
                'jumlah_jual_bulan_ini',
                'total_jual_bulan_ini',
                'currency',
                'pegawai',
                'modal',
                'sisa_modal',
                'jumlah_bulan_ini',
                'total_bulan_ini',
                'total_modal_bulan_ini',
                'transaksi',
                'bulan_ini',
                'pegawai_money_today_total',
                'pegawai_count_money_today',
                'pegawai_sum_money_bulan',
                'transaksi_pegawai_money',
                'cabangStats',
                'approvalModal',
                'total_bulan_lalu',
                'diff_total_bulan'
            ));
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function change_password(Request $request)
    {
        try {
            $email = $request->email;
            $password = $request->password;
            $user = User::where('email', $email)->first();
            if (!$user) {
                Alert::warning('Error', 'User not Found, Try Again');
                return redirect()->back();
            }
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:6', 'regex:/^(?=.*[A-Z])(?=.*[0-9])/'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                Alert::warning('Error', implode("<br>", $errors));
                return redirect()->back();
            }else{
                $user->password = bcrypt($password);
                $user->save();

                 Alert::success('Berhasil', 'Berhasil Reset Password');
                return redirect()->back();
            }

        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function switch_cabang(Request $request)
    {
        $id = $request->input('cabang_id') ?: null;
        $isOwner = Auth::user()->role === 'Owner';
        $allowed = $isOwner
            ? MasterCabang::where('is_active', 1)->pluck('cabang_id')->all()
            : array_column(session('cabangs', []), 'cabang_id');

        // null (semua cabang) hanya untuk Owner
        if ($id === null ? !$isOwner : !in_array((int) $id, $allowed, true)) {
            abort(403);
        }
        session(['cabang_aktif' => $id === null ? null : (int) $id]);
        return redirect()->back();
    }

    public function change_password_v2()
    {
        return view('auth.passwords.resetLogin');
    }

    public function change_password_v2_post(Request $request){
        try {
            $email = $request->email;
            $password = $request->password;
            if($password !== $request->confirm_password){
                Alert::warning('Error', 'Password Confirm not match');
                return redirect()->back();
            }
            $user = User::where('email', $email)->first();
            if (!$user) {
                Alert::warning('Error', 'User not Found, Try Again');
                return redirect()->back();
            }
            $user->password = bcrypt($password);
            $user->update();

            Alert::success('Berhasil', 'Berhasil Reset Password');
            return redirect()->route('login');
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

}
