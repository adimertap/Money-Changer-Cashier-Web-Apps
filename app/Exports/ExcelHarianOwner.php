<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ExcelHarianOwner implements FromCollection, Responsable, ShouldAutoSize,
WithMapping, WithHeadings, WithColumnWidths, WithEvents, WithCustomStartCell
{
    use Exportable;
    public $transaksi;
    private $fileName = "report-transaksi-harian.xlsx";
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($transaksi)
    {
        $this->transaksi = $transaksi;
    }
    public function collection()
    {
        return $this->transaksi;
    }
    public function columnWidths(): array
    {
        return [
            'C' => 30,
            'E' => 30,  
            'I' => 25,          
        ];
    }

    public function headings():array
    {
        return[
            'Kode Transaksi',
            'Tanggal Transaksi',
            'Customer',
            'Nomor Passport',
            'Negara Asal',
            'Pegawai',
            'Currency',
            'Harga PerCurrency',
            'Jumlah Tukar',
            'Total',
        ];
    }

    public function map($transaksi): array
    {
            return[
                $transaksi->kode_transaksi,
                $transaksi->tanggal_transaksi,
                $transaksi->nama_customer,
                $transaksi->nomor_passport,
                $transaksi->negara_asal,
                $transaksi->Pegawai->name,
                $transaksi->nama_currency,
                $transaksi->jumlah_currency,
                $transaksi->jumlah_tukar,
                $transaksi->total_tukar,
            ];
            
    }

    public function registerEvents(): array
    {
        return[
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            }
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
