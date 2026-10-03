<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ExcelDebitKredit implements FromView, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    public function __construct($jurnal, $currency, $totalDebit, $totalKredit, $totalModal)
    {
        $this->jurnal = $jurnal;
        $this->currency = $currency;
        $this->totalDebit = $totalDebit;
        $this->totalKredit = $totalKredit;
        $this->totalModal = $totalModal;
    }

    public function view(): View
    {
        return view('pages.jurnal.kredit&debit.excel',['jurnal' => $this->jurnal, 'kurs' => $this->currency, 'totalDebit' => $this->totalDebit, 'totalKredit'=> $this->totalKredit,'totalModal'=> $this->totalModal]);
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0',
            'F' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $highestRow = $event->sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $jtCell = $event->sheet->getCell('E' . $row);
                    $jtVal = $jtCell->getValue();
                    if (is_numeric($jtVal) && floor((float)$jtVal) != (float)$jtVal) {
                        $event->sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    }

                    $kursCell = $event->sheet->getCell('F' . $row);
                    $kursVal = $kursCell->getValue();
                    if (is_numeric($kursVal) && floor((float)$kursVal) != (float)$kursVal) {
                        $event->sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00##');
                    }
                }
            }
        ];
    }


}
