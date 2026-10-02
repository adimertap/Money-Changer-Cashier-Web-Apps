<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExcelRekapCabang implements WithMultipleSheets
{
    public function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    public function sheets(): array
    {
        $titles = [];

        return array_map(function (array $report) use (&$titles) {
            $base = trim((string) $report['cabang']->cabang_name) ?: 'Cabang';
            $base = mb_substr(str_replace(['\\', '/', '?', '*', '[', ']', ':'], '-', $base), 0, 31);
            $titles[$base] = ($titles[$base] ?? 0) + 1;
            $title = $titles[$base] === 1 ? $base : mb_substr($base, 0, 28) . ' ' . $titles[$base];

            return new ExcelRekapCabangSheet($report, $title);
        }, $this->reports);
    }
}
