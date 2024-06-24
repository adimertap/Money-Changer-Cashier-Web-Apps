<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        try {
            $month = Carbon::now()->format('m');
            $bulan_ini = Carbon::now()->format('M Y');
            $currency = MasterCurrency::count();
            $pegawai = User::count();
            $currentMonth = date('m');
            $currentYear = date('Y');

            //TODAY
            $jumlah_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->where('jenis_transaksi','Beli')->count();
            $total_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->where('jenis_transaksi','Beli')->sum('total');
            $jumlah_jual_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->where('jenis_transaksi','Jual')->count();
            $total_jual_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->where('jenis_transaksi','Jual')->sum('total');

            //MODAL
            $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Pending')->count();
            $sisa_modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Terima')->first();
            $total_modal_bulan_ini = ModalTransaksi::whereMonth('tanggal_modal', $currentMonth) ->whereYear('tanggal_modal', $currentYear)->sum('jumlah_modal');

            // BULAN INI
            $jumlah_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('jenis_transaksi','Beli')
            ->count();
            $total_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('jenis_transaksi','Beli')
            ->sum('total');
            $jumlah_jual_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('jenis_transaksi','Jual')
            ->count();
            $total_jual_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->where('jenis_transaksi','Jual')
            ->sum('total');

            //SEMUA
            $jumlah_seluruh = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
           

            $transaksi = Transaksi::with('detailTransaksi')->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
                ->orderBy('created_at', 'DESC')
                ->take(5)->get();

            //PEGAWAI
            $pegawai_money_today_total = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
                ->where('jenis_transaksi','Beli')
                ->sum('total');
            $pegawai_money_today_total_jual = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
                ->where('jenis_transaksi','Jual')
                ->sum('total');

            // BARIS 2
            $pegawai_count_money_today = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
                ->where('jenis_transaksi','Beli')
                ->count();
                $pegawai_count_money_today_jual = Transaksi::where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
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

            $transaksi_pegawai_money = Transaksi::with('detailTransaksi')->where('id_pegawai', Auth::user()->id)
                ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
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
                'transaksi_pegawai_money'
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

}
