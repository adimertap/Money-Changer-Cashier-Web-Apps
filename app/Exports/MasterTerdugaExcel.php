<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterTerdugaExcel implements FromArray, ShouldAutoSize, WithHeadings
{
    public function __construct($header)
    {
        $this->header = $header;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama',
            'Alias',
            'Tipe',
            'Kode Densus',
            'Tempat Lahir',
            'Tanggal Lahir',
            'WN',
        ];
    }

    public function array(): array
    {
        return $this->header->terduga->values()->map(function ($item, $index) {
            return [
                $index + 1,
                $item->name ?: '-',
                $item->alias ?: '-',
                $item->terduga_type ?: '-',
                $item->kode_densus ?: '-',
                $item->tempat_lahir ?: '-',
                $item->tanggal_lahir ?: '-',
                $item->wn ?: '-',
            ];
        })->all();
    }
}
