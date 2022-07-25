<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class TransaksiHarianExport implements FromCollection, 
    WithHeadings, 
    ShouldAutoSize, 
    WithMapping, 
    WithEvents,
    WithCustomStartCell
{
    public $today;

    public function __construct($today)
    {
        $this->today = $today;
    }
    
    
    public function headings():array
    {  return[
        '#',
        'KODE TRANSAKSI',
        'TANGGAL TRANSAKSI',
        'TOTAL TRANSAKSI',
    ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
 
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaksi::where('tanggal_transaksi', $this->today)->get();
    }

    public function map($transaksi):array
    {
        return [
            $transaksi->iteration,
            $transaksi->kode_transaksi,
            $transaksi->tanggal_transaksi,
            $transaksi->total
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A2:D2')->applyFromArray([
                    'font' =>[
                        'bold' => true,
                    ]
                ]);

            }
        ];   
    }
}
