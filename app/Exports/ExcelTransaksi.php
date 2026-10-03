<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class ExcelTransaksi implements FromCollection, Responsable, ShouldAutoSize,
WithMapping, WithHeadings, WithColumnWidths, WithEvents, WithCustomStartCell, WithColumnFormatting
{
    use Exportable;
    public $transaksi;
    private $fileName = "report-transaksi.xlsx";
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($transaksi)
    {
        $this->transaksi = $transaksi;
        // $this->grand_total = $penjualan->sum('grand_total');
        // $this->total_produk = $penjualan->count('id_produk');
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
            'J' => 25,          
        ];
    }

    public function headings():array
    {
        return[
            'Kode Transaksi',
            'Tanggal Transaksi',
            'Pegawai',
            'Customer',
            'Nomor Passport',
            'Negara Asal',
            'Currency',
            'Harga PerCurrency',
            'Jumlah Tukar',
            'Total',
        ];
    }

    public function map($transaksi): array
    {
        $jumlahTukar = (float) $transaksi->jumlah_tukar;
        return [
            $transaksi->kode_transaksi,
            $transaksi->tanggal_transaksi,
            optional($transaksi->Pegawai)->name,
            $transaksi->nama_customer,
            $transaksi->nomor_passport,
            $transaksi->negara_asal,
            $transaksi->nama_currency,
            (float) $transaksi->jumlah_currency,
            floor($jumlahTukar) == $jumlahTukar ? (int) $jumlahTukar : $jumlahTukar,
            round((float) $transaksi->total_tukar),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $lastRow = $this->transaksi->count() + 2;
                $event->sheet->getStyle('H3:J' . $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);

                for ($row = 3; $row <= $lastRow; $row++) {
                    $kursVal = $event->sheet->getCell('H' . $row)->getValue();
                    if (is_numeric($kursVal) && floor((float)$kursVal) != (float)$kursVal) {
                        $event->sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0.00##');
                    }
                    $jtVal = $event->sheet->getCell('I' . $row)->getValue();
                    if (is_numeric($jtVal) && floor((float)$jtVal) != (float)$jtVal) {
                        $event->sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                }
            }
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
