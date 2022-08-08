<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ExcelDebitKredit implements FromView, ShouldAutoSize, WithEvents
{
    public function __construct($jurnal, $currency)
    {
        $this->jurnal = $jurnal;
        $this->currency = $currency;
    }

    public function view(): View
    {
        return view('pages.jurnal.kredit&debit.excel',['jurnal' => $this->jurnal, 'kurs' => $this->currency]);
    }

    public function registerEvents(): array
    {

        return[
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A1:H1')->applyFromArray([
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

    
}
