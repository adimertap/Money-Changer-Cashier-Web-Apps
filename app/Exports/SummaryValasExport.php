<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SummaryValasExport implements WithMultipleSheets
{
    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function sheets(): array
    {
        return [new SummaryValasSheet($this->report)];
    }
}
