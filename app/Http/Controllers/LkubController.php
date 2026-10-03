<?php

namespace App\Http\Controllers;

use App\Exports\LkubExport;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use App\Models\MasterThreshold;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LkubController extends Controller
{
    public function index()
    {
        return view('pages.laporan.lkub.index', [
            'cabangs' => $this->allowedCabangs(),
            'months' => $this->months(),
            'years' => range(now()->year - 5, now()->year + 5),
        ]);
    }

    public function download(Request $request)
    {
        $allowed = $this->allowedCabangIds();
        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'digits:4'],
            'format' => ['required', 'in:excel,pdf,csv,txt'],
            'semua_cabang' => ['nullable', 'boolean'],
            'cabang_ids' => ['required_unless:semua_cabang,1', 'array', 'min:1'],
            'cabang_ids.*' => ['integer', 'distinct', Rule::in($allowed)],
        ]);

        $branchIds = $request->boolean('semua_cabang')
            ? $allowed
            : array_values(array_unique(array_map('intval', $data['cabang_ids'])));
        $cabangs = MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $branchIds)
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
        $periodLabel = $this->months()[(int) $data['month']] . ' ' . $data['year'];
        $reports = $cabangs->map(function ($cabang) use ($data, $periodLabel) {
            return [
                'cabang' => $cabang,
                'period' => $periodLabel,
                'rows' => $this->buildReportRows($cabang->cabang_id, (int) $data['month'], (int) $data['year']),
            ];
        })->all();

        if (!collect($reports)->contains(function (array $report) {
            return $report['rows']->isNotEmpty();
        })) {
            return redirect()->route('laporan-lkub.index')
                ->with('error', 'Data LKUB tidak ditemukan pada periode dan cabang yang dipilih.');
        }

        $filename = 'LKUB ' . $periodLabel;
        if ($data['format'] === 'pdf') {
            return Pdf::loadView('pages.laporan.lkub.pdf', compact('reports'))
                ->setPaper('a4', 'landscape')
                ->download($filename . '.pdf');
        }

        if ($data['format'] === 'csv') {
            return $this->downloadCsv($reports, $filename . '.csv');
        }

        if ($data['format'] === 'txt') {
            return $this->downloadTxt($reports, $filename . '.txt', $periodLabel);
        }

        return Excel::download(new LkubExport($reports, $periodLabel), $filename . '.xlsx');
    }

    private function downloadCsv(array $reports, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'CABANG',
                'NO',
                'FOREX',
                'TYPE',
                'BG. BALANCE',
                'BG. BALANCE (Rp.)',
                'BUY',
                'BUY (Rp.)',
                'SELL',
                'SELL (Rp.)',
                'BALANCE',
                'MIDDLE RATE',
                'BALANCE (Rp.)',
            ]);

            foreach ($reports as $report) {
                $cabangName = $report['cabang']->cabang_name;
                foreach ($report['rows'] as $row) {
                    fputcsv($handle, [
                        $cabangName,
                        $row['no'],
                        $row['forex'],
                        $row['type'],
                        number_format($row['bg_balance'], 2, '.', ''),
                        number_format($row['bg_balance_rp'], 2, '.', ''),
                        number_format($row['buy'], 2, '.', ''),
                        number_format($row['buy_rp'], 2, '.', ''),
                        number_format($row['sell'], 2, '.', ''),
                        number_format($row['sell_rp'], 2, '.', ''),
                        number_format($row['balance'], 2, '.', ''),
                        number_format($row['middle_rate'], 4, '.', ''),
                        number_format($row['balance_rp'], 2, '.', ''),
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function downloadTxt(array $reports, string $filename, string $periodLabel)
    {
        $headers = [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($reports, $periodLabel) {
            $handle = fopen('php://output', 'w');

            $divider = str_repeat('=', 160) . "\r\n";
            $line = str_repeat('-', 160) . "\r\n";

            fwrite($handle, $divider);
            fwrite($handle, "LKUB - LAPORAN KEGIATAN USAHA BULANAN\r\n");
            fwrite($handle, "Periode: " . $periodLabel . "\r\n");
            fwrite($handle, $divider . "\r\n");

            foreach ($reports as $index => $report) {
                if ($index > 0) {
                    fwrite($handle, "\r\n\r\n");
                }
                fwrite($handle, "CABANG: " . $report['cabang']->cabang_name . "\r\n");
                fwrite($handle, $line);

                $headerStr = sprintf(
                    "%-4s %-8s %-6s %14s %18s %14s %18s %14s %18s %14s %14s %18s\r\n",
                    'NO', 'FOREX', 'TYPE', 'BG. BALANCE', 'BG. BALANCE (Rp)',
                    'BUY', 'BUY (Rp)', 'SELL', 'SELL (Rp)', 'BALANCE', 'MIDDLE RATE', 'BALANCE (Rp)'
                );
                fwrite($handle, $headerStr);
                fwrite($handle, $line);

                foreach ($report['rows'] as $row) {
                    $rowStr = sprintf(
                        "%-4s %-8s %-6s %14s %18s %14s %18s %14s %18s %14s %14s %18s\r\n",
                        $row['no'],
                        $row['forex'],
                        $row['type'],
                        number_format($row['bg_balance'], 2, ',', '.'),
                        number_format($row['bg_balance_rp'], 2, ',', '.'),
                        number_format($row['buy'], 2, ',', '.'),
                        number_format($row['buy_rp'], 2, ',', '.'),
                        number_format($row['sell'], 2, ',', '.'),
                        number_format($row['sell_rp'], 2, ',', '.'),
                        number_format($row['balance'], 2, ',', '.'),
                        number_format($row['middle_rate'], 4, ',', '.'),
                        number_format($row['balance_rp'], 2, ',', '.')
                    );
                    fwrite($handle, $rowStr);
                }
                fwrite($handle, $line);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public static function calculateRow(string $forex, array $values): array
    {
        $openingQuantity = (float) ($values['opening_quantity'] ?? 0);
        $openingRupiah = (float) ($values['opening_rupiah'] ?? 0);
        $buy = (float) ($values['buy_quantity'] ?? 0);
        $buyRp = (float) ($values['buy_rupiah'] ?? 0);
        $sell = (float) ($values['sell_quantity'] ?? 0);
        $sellRp = (float) ($values['sell_rupiah'] ?? 0);
        $balance = $openingQuantity + $buy - $sell;
        $balanceRp = $openingRupiah + $buyRp - $sellRp;

        return [
            'forex' => $forex,
            'type' => 'UKA',
            'bg_balance' => $openingQuantity,
            'bg_balance_rp' => $openingRupiah,
            'buy' => $buy,
            'buy_rp' => $buyRp,
            'sell' => $sell,
            'sell_rp' => $sellRp,
            'balance' => $balance,
            'middle_rate' => $balance == 0.0 ? 0.0 : $balanceRp / $balance,
            'balance_rp' => $balanceRp,
        ];
    }

    public function buildReportRows(int $cabangId, int $month, int $year): Collection
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $base = Jurnal::withoutGlobalScope('cabang')
            ->join('tb_currency', 'tb_jurnal.id_currency', '=', 'tb_currency.id_currency')
            ->where('tb_jurnal.cabang_id', $cabangId);

        $monthName = $this->months()[$month];
        // Ambil preset batas transaksi bulanan (Master Threshold) yang aktif
        $thresholds = MasterThreshold::where('is_active', 1)
            ->where(function ($q) use ($year, $monthName, $start, $end) {
                $q->where(function ($sub) use ($year, $monthName) {
                    $sub->where('tahun', $year)
                        ->where('bulan', $monthName);
                })->orWhere(function ($sub) use ($start, $end) {
                    $sub->whereDate('start_date', '<=', $end->toDateString())
                        ->whereDate('end_date', '>=', $start->toDateString());
                });
            })
            ->get()
            ->keyBy('currency_id');

        $opening = (clone $base)
            ->whereDate('tb_jurnal.tanggal_jurnal', '<', $start->toDateString())
            ->whereIn('tb_jurnal.jenis_jurnal', ['Debit', 'Kredit Jual'])
            ->selectRaw('tb_jurnal.id_currency, tb_currency.nama_currency as forex')
            ->selectRaw("SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.jumlah_tukar ELSE 0 END) - SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.jumlah_tukar ELSE 0 END) as opening_quantity")
            ->selectRaw("SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.total_tukar ELSE 0 END) - SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.total_tukar ELSE 0 END) as opening_rupiah")
            ->groupBy('tb_jurnal.id_currency', 'tb_currency.nama_currency')
            ->get()
            ->keyBy('id_currency');

        // Ambil transaksi periode bulan berjalan secara kronologis
        $periodJournals = (clone $base)
            ->whereDate('tb_jurnal.tanggal_jurnal', '>=', $start->toDateString())
            ->whereDate('tb_jurnal.tanggal_jurnal', '<=', $end->toDateString())
            ->whereIn('tb_jurnal.jenis_jurnal', ['Debit', 'Kredit Jual'])
            ->orderBy('tb_jurnal.tanggal_jurnal', 'asc')
            ->orderBy('tb_jurnal.id_jurnal', 'asc')
            ->get([
                'tb_jurnal.id_currency',
                'tb_currency.nama_currency as forex',
                'tb_jurnal.jenis_jurnal',
                'tb_jurnal.jumlah_tukar',
                'tb_jurnal.total_tukar',
                'tb_jurnal.kurs'
            ]);

        $groupedJournals = $periodJournals->groupBy('id_currency');
        $periodData = [];

        foreach ($groupedJournals as $currencyId => $items) {
            $forex = $items->first()->forex;
            $threshold = $thresholds->get($currencyId);

            $buyQuantity = 0.0;
            $buyRupiah = 0.0;
            $sellQuantity = 0.0;
            $sellRupiah = 0.0;

            if (!$threshold) {
                // Tanpa batas threshold, rekap semua transaksi apa adanya
                foreach ($items as $item) {
                    $qty = (float) $item->jumlah_tukar;
                    $rp = (float) $item->total_tukar;
                    if ($item->jenis_jurnal === 'Debit') {
                        $buyQuantity += $qty;
                        $buyRupiah += $rp;
                    } elseif ($item->jenis_jurnal === 'Kredit Jual') {
                        $sellQuantity += $qty;
                        $sellRupiah += $rp;
                    }
                }
            } else {
                // Terhubung dengan Master Threshold: transaksi yang melebihi batas nominal bulanan dipangkas dari LKUB
                $limit = (float) $threshold->nominal;
                $isRupiahLimit = $limit > 100000;
                $accumulated = 0.0;

                foreach ($items as $item) {
                    $qty = (float) $item->jumlah_tukar;
                    $rp = (float) $item->total_tukar;
                    $val = $isRupiahLimit ? $rp : $qty;

                    if ($accumulated >= $limit) {
                        // Batas threshold tercapai, lebihnya tidak ditampilkan di laporan LKUB ke BI
                        continue;
                    }

                    if ($accumulated + $val <= $limit) {
                        if ($item->jenis_jurnal === 'Debit') {
                            $buyQuantity += $qty;
                            $buyRupiah += $rp;
                        } elseif ($item->jenis_jurnal === 'Kredit Jual') {
                            $sellQuantity += $qty;
                            $sellRupiah += $rp;
                        }
                        $accumulated += $val;
                    } else {
                        // Transaksi sebagian melewati batas threshold, hanya tampilkan sisa yang masuk batas
                        $remaining = $limit - $accumulated;
                        if ($remaining > 0) {
                            $kurs = (float) $item->kurs ?: 1.0;
                            if ($isRupiahLimit) {
                                $allowedRp = $remaining;
                                $allowedQty = $allowedRp / $kurs;
                            } else {
                                $allowedQty = $remaining;
                                $allowedRp = $allowedQty * $kurs;
                            }

                            if ($item->jenis_jurnal === 'Debit') {
                                $buyQuantity += $allowedQty;
                                $buyRupiah += $allowedRp;
                            } elseif ($item->jenis_jurnal === 'Kredit Jual') {
                                $sellQuantity += $allowedQty;
                                $sellRupiah += $allowedRp;
                            }
                            $accumulated = $limit;
                        }
                    }
                }
            }

            $periodData[$currencyId] = (object) [
                'id_currency' => $currencyId,
                'forex' => $forex,
                'buy_quantity' => $buyQuantity,
                'buy_rupiah' => $buyRupiah,
                'sell_quantity' => $sellQuantity,
                'sell_rupiah' => $sellRupiah,
            ];
        }

        $period = collect($periodData);

        return self::reportCurrencyIds($opening, $period)->map(function ($currencyId) use ($opening, $period) {
            $openingRow = $opening->get($currencyId);
            $periodRow = $period->get($currencyId);
            $forex = optional($periodRow ?: $openingRow)->forex;
            $row = self::calculateRow($forex, [
                'opening_quantity' => optional($openingRow)->opening_quantity,
                'opening_rupiah' => optional($openingRow)->opening_rupiah,
                'buy_quantity' => optional($periodRow)->buy_quantity,
                'buy_rupiah' => optional($periodRow)->buy_rupiah,
                'sell_quantity' => optional($periodRow)->sell_quantity,
                'sell_rupiah' => optional($periodRow)->sell_rupiah,
            ]);
            $row['id_currency'] = $currencyId;
            return $row;
        })->sortBy('forex')->values()->map(function (array $row, $index) {
            $row['no'] = $index + 1;
            return $row;
        });
    }

    public static function reportCurrencyIds(Collection $opening, Collection $period): Collection
    {
        return $period->keys()->unique()->values();
    }

    private function allowedCabangIds(): array
    {
        if (Auth::user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->map('intval')->all();
        }

        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', array_map('intval', array_column(session('cabangs', []), 'cabang_id')))
            ->pluck('cabang_id')
            ->map('intval')
            ->all();
    }

    private function allowedCabangs()
    {
        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $this->allowedCabangIds())
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
    }

    private function months(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }
}
