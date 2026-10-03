<?php

namespace App\Http\Controllers;

use App\Exports\SummaryValasExport;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use App\Models\MasterCurrency;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SummaryValasController extends Controller
{
    public function index()
    {
        return view('pages.laporan.summary-valas.index', [
            'cabangs' => $this->allowedCabangs(),
            'currencies' => MasterCurrency::withoutGlobalScope('cabang')
                ->orderBy('nama_currency')
                ->get(['id_currency', 'nama_currency', 'country']),
        ]);
    }

    public function download(Request $request)
    {
        $allowedCabangIds = $this->allowedCabangIds();
        $allowedCurrencyIds = MasterCurrency::withoutGlobalScope('cabang')
            ->pluck('id_currency')
            ->map('intval')
            ->all();
        $data = $request->validate([
            'cabang_id' => ['nullable', 'integer', Rule::in($allowedCabangIds)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'semua_currency' => ['nullable', 'boolean'],
            'currency_id' => ['nullable', 'integer', 'required_unless:semua_currency,1', Rule::in($allowedCurrencyIds)],
            'format' => ['required', 'in:excel,pdf'],
        ]);

        $branchIds = $request->filled('cabang_id') ? [(int) $data['cabang_id']] : $allowedCabangIds;
        $currencyIds = $request->boolean('semua_currency')
            ? $allowedCurrencyIds
            : [(int) $data['currency_id']];
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->endOfDay();
        $rows = $this->buildReportRows($branchIds, $currencyIds, $start, $end);

        if ($rows->isEmpty()) {
            return redirect()->route('summary-valas.index')
                ->with('error', 'Data Summary Valas tidak ditemukan pada periode dan filter yang dipilih.');
        }

        $branchLabel = $request->filled('cabang_id')
            ? optional(MasterCabang::withoutGlobalScope('cabang')->whereIn('cabang_id', $allowedCabangIds)->find($data['cabang_id']))->cabang_name
            : 'Semua Cabang';
        $periodLabel = $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        $report = [
            'branch_label' => $branchLabel ?: 'Cabang',
            'period_label' => $periodLabel,
            'rows' => $rows,
        ];
        $filename = 'Summary Valas ' . $start->format('Y-m-d') . ' sampai ' . $end->format('Y-m-d');

        if ($data['format'] === 'pdf') {
            return Pdf::loadView('pages.laporan.summary-valas.pdf', compact('report'))
                ->setPaper('a4', 'landscape')
                ->download($filename . '.pdf');
        }

        return Excel::download(new SummaryValasExport($report), $filename . '.xlsx');
    }

    public static function calculateRow(string $code, string $name, array $values): array
    {
        $beginning = (float) ($values['beginning_balance'] ?? 0);
        $buy = (float) ($values['buy_quantity'] ?? 0);
        $sell = (float) ($values['sell_quantity'] ?? 0);
        $buyIdr = (float) ($values['buy_idr'] ?? 0);
        $sellIdr = (float) ($values['sell_idr'] ?? 0);

        return [
            'code' => $code,
            'name' => $name,
            'beginning_balance' => $beginning,
            'buy' => $buy,
            'sell' => $sell,
            'ending_balance' => $beginning + $buy - $sell,
            'buy_idr' => $buyIdr,
            'sell_idr' => $sellIdr,
        ];
    }

    public static function reportCurrencyIds(Collection $opening, Collection $period): Collection
    {
        return $opening->keys()->merge($period->keys())->unique()->values();
    }

    public function buildReportRows(array $branchIds, array $currencyIds, Carbon $start, Carbon $end): Collection
    {
        $base = Jurnal::withoutGlobalScope('cabang')
            ->join('tb_currency', 'tb_jurnal.id_currency', '=', 'tb_currency.id_currency')
            ->whereIn('tb_jurnal.cabang_id', $branchIds)
            ->whereIn('tb_jurnal.id_currency', $currencyIds)
            ->whereIn('tb_jurnal.jenis_jurnal', ['Debit', 'Kredit Jual']);

        $opening = (clone $base)
            ->where('tb_jurnal.tanggal_jurnal', '<', $start->toDateTimeString())
            ->selectRaw('tb_jurnal.id_currency, tb_currency.nama_currency as currency_code, tb_currency.country as currency_name')
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) as beginning_balance")
            ->groupBy('tb_jurnal.id_currency', 'tb_currency.nama_currency', 'tb_currency.country')
            ->get()
            ->keyBy('id_currency');

        $period = (clone $base)
            ->whereBetween('tb_jurnal.tanggal_jurnal', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->selectRaw('tb_jurnal.id_currency, tb_currency.nama_currency as currency_code, tb_currency.country as currency_name')
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) as buy_quantity")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.jumlah_tukar ELSE 0 END), 0) as sell_quantity")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Debit' THEN tb_jurnal.total_tukar ELSE 0 END), 0) as buy_idr")
            ->selectRaw("COALESCE(SUM(CASE WHEN tb_jurnal.jenis_jurnal = 'Kredit Jual' THEN tb_jurnal.total_tukar ELSE 0 END), 0) as sell_idr")
            ->groupBy('tb_jurnal.id_currency', 'tb_currency.nama_currency', 'tb_currency.country')
            ->get()
            ->keyBy('id_currency');

        return self::reportCurrencyIds($opening, $period)
            ->map(function ($currencyId) use ($opening, $period) {
                $openingRow = $opening->get($currencyId);
                $periodRow = $period->get($currencyId);
                $source = $periodRow ?: $openingRow;
                return self::calculateRow((string) optional($source)->currency_code, (string) (optional($source)->currency_name ?: optional($source)->currency_code), [
                    'beginning_balance' => optional($openingRow)->beginning_balance,
                    'buy_quantity' => optional($periodRow)->buy_quantity,
                    'sell_quantity' => optional($periodRow)->sell_quantity,
                    'buy_idr' => optional($periodRow)->buy_idr,
                    'sell_idr' => optional($periodRow)->sell_idr,
                ]);
            })
            ->sortBy('code')
            ->values()
            ->map(function (array $row, $index) {
                $row['no'] = $index + 1;
                return $row;
            });
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
        return MasterCabang::whereIn('cabang_id', $this->allowedCabangIds())
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
    }
}
