<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function cetak($id)
    {
        $transaksi = Transaksi::with('detailTransaksi','Pegawai')->find($id);
        
        $tes = Transaksi::loadView('print.cetak', ['transaksi'=> $transaksi])
        ->setOptions(['dpi' => 150, 'isRemoteEnabled' => true])
        ->setPaper([0,0,288,935.43]);
        return $tes;

        // return view('print.cetak', compact('transaksi'));
    }
}
