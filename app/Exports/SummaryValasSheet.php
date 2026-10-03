<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SummaryValasSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function view(): View
    {
        return view('pages.laporan.summary-valas.excel', ['report' => $this->report]);
    }

    public function title(): string
    {
        return 'Summary Valas';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:H4')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $event->sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF343A40']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $event->sheet->getStyle('G5:H' . max(5, $event->sheet->getHighestRow()))
                    ->getNumberFormat()->setFormatCode('Rp #,##0');
            },
        ];
    }
}
