<?php

namespace App\Http\Controllers;

use App\Models\MasterCurrency;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $month = Carbon::now()->format('m');
        $bulan_ini = Carbon::now()->format('M Y');
        $jumlah_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_hari_ini = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
        $currency = MasterCurrency::count();
        $pegawai = User::count();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Pending')->count();
        $sisa_modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Terima')->first();
        $jumlah_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $month)->count();
        $total_bulan_ini = Transaksi::whereMonth('tanggal_transaksi', $month)->sum('total');
        $total_modal_bulan_ini = ModalTransaksi::whereMonth('tanggal_modal', $month)->sum('jumlah_modal');
        
        $transaksi = Transaksi::with('detailTransaksi')->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))
        ->orderBy('created_at','DESC')
        ->take(5)->get();
        
        return view('pages.dashboard.dashboard', compact(
            'jumlah_hari_ini',
            'total_hari_ini',
            'currency',
            'pegawai',
            'modal',
            'sisa_modal',
            'jumlah_bulan_ini',
            'total_bulan_ini',
            'total_modal_bulan_ini',
            'transaksi',
            'bulan_ini'
        ));
    }
}
