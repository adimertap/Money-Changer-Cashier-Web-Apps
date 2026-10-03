<?php

namespace App\Http\Controllers;

use App\Exports\LkubExport;
use App\Models\Jurnal;
use App\Models\MasterCabang;
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
            'format' => ['required', 'in:excel,pdf'],
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

        return Excel::download(new LkubExport($reports, $periodLabel), $filename . '.xlsx');
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

        $opening = (clone $base)
            ->whereDate('tb_jurnal.tanggal_jurnal', '<', $start->toDateString())
            ->whereIn('tb_jurnal.jenis_jurnal', ['Debit', 'Kredit Jual'])
            ->selectRaw('tb_jurnal.id_currency, tb_currency.nama_currency as forex')
            ->selectRaw("SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.jumlah_tukar ELSE 0 END) - SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.jumlah_tukar ELSE 0 END) as opening_quantity")
            ->selectRaw("SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.total_tukar ELSE 0 END) - SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.total_tukar ELSE 0 END) as opening_rupiah")
            ->groupBy('tb_jurnal.id_currency', 'tb_currency.nama_currency')
            ->get()
            ->keyBy('id_currency');

        $period = (clone $base)
            ->whereDate('tb_jurnal.tanggal_jurnal', '>=', $start->toDateString())
            ->whereDate('tb_jurnal.tanggal_jurnal', '<=', $end->toDateString())
            ->whereIn('tb_jurnal.jenis_jurnal', ['Debit', 'Kredit Jual'])
            ->selectRaw('tb_jurnal.id_currency, tb_currency.nama_currency as forex')
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) as buy_quantity")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.total_tukar ELSE 0 END), 0) as buy_rupiah")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) as sell_quantity")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.total_tukar ELSE 0 END), 0) as sell_rupiah")
            ->groupBy('tb_jurnal.id_currency', 'tb_currency.nama_currency')
            ->get()
            ->keyBy('id_currency');

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
