<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ExcelHarianView implements FromView
{
    // public $transaksi;

    public function __construct($transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function view(): View
    {
        return view('export.excel',['transaksi' => $this->transaksi]);
    }
}
