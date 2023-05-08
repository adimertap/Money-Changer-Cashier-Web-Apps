<?php

namespace App\Http\Controllers;

use Knp\Snappy\Pdf;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiHarianExport;
use RealRashid\SweetAlert\Facades\Alert;

class CetakController extends Controller
{
    public function cetak($id)
    {
        try {
            $transaksi = Transaksi::with('detailTransaksi','Pegawai')->find($id);
            return view('print.cetak', compact('transaksi'));
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
       
    }

    public function exportexcel($today)
    {
       return Excel::download(new TransaksiHarianExport($today), 'RekapTransaksi.xlsx');
    }
}
