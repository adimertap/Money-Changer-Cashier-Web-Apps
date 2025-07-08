<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use App\Models\MasterShift;

class JadwalKerjaFormatExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;
    protected $pegawai_ids;
    protected $start_date;
    protected $end_date;
    protected $shift_id;
    protected $shift;
    protected $rowPegawaiStartIndexes = [];

    public function __construct($pegawai_ids, $start_date, $end_date, $shift_id)
    {
        $this->pegawai_ids = $pegawai_ids;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->shift_id = $shift_id;
        $this->shift = MasterShift::find($shift_id);
    }

    public function collection()
    {
        $rows = [];
        $this->rowPegawaiStartIndexes = [];
        foreach ($this->pegawai_ids as $pegawai_id) {
            $user = \App\Models\User::find($pegawai_id);
            $name = $user ? $user->name : '';
            $current = strtotime($this->start_date);
            $end = strtotime($this->end_date ?? $this->start_date);
            $firstRowIndex = count($rows) + 2; // +2 for heading row and 1-based index
            $this->rowPegawaiStartIndexes[] = $firstRowIndex;
            while ($current <= $end) {
                $date = date('Y-m-d', $current);
                $month = date('m', $current);
                $year = date('Y', $current);
                $rows[] = [
                    $this->shift_id, // shift_id
                    $pegawai_id, // id
                    $name, // name
                    $date, // tanggal
                    '', // keterangan
                    $month, // month
                    $year, // year
                ];
                $current = strtotime('+1 day', $current);
            }
        }
        return new \Illuminate\Support\Collection($rows);
    }

    public function headings(): array
    {
        return [
            'shift_id',
            'id',
            'name',
            'tanggal',
            'keterangan',
            'month',
            'year',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Highlight the first row of each pegawai in yellow if exporting for all pegawai
                if (count($this->pegawai_ids) > 1) {
                    foreach ($this->rowPegawaiStartIndexes as $rowIdx) {
                        $event->sheet->getStyle('A'.$rowIdx.':G'.$rowIdx)
                            ->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => [ 'rgb' => 'FFFF00' ]
                                ]
                            ]);
                    }
                }

                // Start writing the master shift table at column L, row 2
                $startColumn = 'L';
                $startRow = 2;
                $headers = ['Shift Id', 'Shift Name', 'Shift In', 'Shift Out'];
                foreach ($headers as $i => $header) {
                    $event->sheet->setCellValueByColumnAndRow(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn) + $i,
                        $startRow,
                        $header
                    );
                }
                $shifts = MasterShift::all();
                $row = $startRow + 1;
                foreach ($shifts as $shift) {
                    $event->sheet->setCellValue($startColumn . $row, $shift->shift_id);
                    $event->sheet->setCellValue(chr(ord($startColumn)+1) . $row, $shift->shift_name);
                    $event->sheet->setCellValue(chr(ord($startColumn)+2) . $row, $shift->shift_in);
                    $event->sheet->setCellValue(chr(ord($startColumn)+3) . $row, $shift->shift_out);
                    $row++;
                }
            }
        ];
    }
}
