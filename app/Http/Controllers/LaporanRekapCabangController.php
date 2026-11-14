<?php

namespace App\Http\Controllers;

use App\Exports\ExcelRekapCabang;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LaporanRekapCabangController extends Controller
{
    private function legacyConnections(): array
    {
        $map = [];
        foreach (['mysql_cabang_2', 'mysql_cabang_4', 'mysql_cabang_5'] as $conn) {
            $cfg = config("database.connections.{$conn}");
            if (!empty($cfg['cabang_id']) && !empty($cfg['database'])) {
                $map[(int) $cfg['cabang_id']] = [
                    'connection' => $conn,
                    'cabang_name' => $cfg['cabang_name'] ?? '-',
                ];
            }
        }
        return $map;
    }

    private function mergeCurrencyTotals($baseRows, $extraRows)
    {
        $grouped = [];
        foreach ($baseRows->concat($extraRows) as $row) {
            $key = $row->nama . '|' . $row->jumlah_kurs;
            if (!isset($grouped[$key])) {
                $grouped[$key] = clone $row;
                $grouped[$key]->total = (float) $row->total;
            } else {
                $grouped[$key]->total += (float) $row->total;
            }
        }
        return collect(array_values($grouped))->sortBy('nama')->values();
    }

    public function index()
    {
        $cabangs = $this->allowedCabangs();
        return view('pages.laporan.rekap-cabang.index', compact('cabangs'));
    }

    public function download(Request $request)
    {
        $allowed = $this->allowedCabangIds();
        $data = $request->validate([
            'radio_input' => ['required', 'in:excel,pdf'],
            'from_date_export' => ['required', 'date'],
            'to_date_export' => ['required', 'date', 'after_or_equal:from_date_export'],
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
        $legacyMap = $this->legacyConnections();
        $reports = [];

        foreach ($cabangs as $cabang) {
            $currency = $this->jurnalQuery($cabang->cabang_id, $data)
                ->join('tb_currency', 'tb_jurnal.id_currency', '=', 'tb_currency.id_currency')
                ->where('tb_jurnal.jenis_jurnal', 'Debit')
                ->selectRaw('tb_currency.nama_currency as nama, SUM(tb_jurnal.jumlah_tukar) as total, tb_jurnal.kurs as jumlah_kurs')
                ->groupBy('tb_currency.nama_currency', 'tb_jurnal.kurs')
                ->orderBy('tb_currency.nama_currency')
                ->get();

            $totalDebit = (float) $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Debit')
                ->sum('total_tukar');
            $totalKredit = (float) $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Kredit Jual')
                ->sum('total_tukar');
            $totalModal = (float) $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Kredit')
                ->sum('jumlah_modal');

            if (isset($legacyMap[(int) $cabang->cabang_id])) {
                $connection = $legacyMap[(int) $cabang->cabang_id]['connection'];
                try {
                    $legacyBase = fn () => Jurnal::on($connection)
                        ->withoutGlobalScope('cabang')
                        ->whereDate('tb_jurnal.tanggal_jurnal', '>=', $data['from_date_export'])
                        ->whereDate('tb_jurnal.tanggal_jurnal', '<=', $data['to_date_export']);

                    $legacyCurrency = $legacyBase()
                        ->join('tb_currency', 'tb_jurnal.id_currency', '=', 'tb_currency.id_currency')
                        ->where('tb_jurnal.jenis_jurnal', 'Debit')
                        ->selectRaw('tb_currency.nama_currency as nama, SUM(tb_jurnal.jumlah_tukar) as total, tb_jurnal.kurs as jumlah_kurs')
                        ->groupBy('tb_currency.nama_currency', 'tb_jurnal.kurs')
                        ->get();

                    $currency = $this->mergeCurrencyTotals($currency, $legacyCurrency);
                    $totalDebit += (float) $legacyBase()->where('jenis_jurnal', 'Debit')->sum('total_tukar');
                    $totalKredit += (float) $legacyBase()->where('jenis_jurnal', 'Kredit Jual')->sum('total_tukar');
                    $totalModal += (float) $legacyBase()->where('jenis_jurnal', 'Kredit')->sum('jumlah_modal');
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengambil rekap cabang legacy {$cabang->cabang_id} ({$connection}): " . $e->getMessage());
                }
            }

            $reports[] = [
                'cabang' => $cabang,
                'currency' => $currency,
                'totalDebit' => $totalDebit,
                'totalKredit' => $totalKredit,
                'totalModal' => $totalModal,
                'grand' => $totalKredit + $totalModal - $totalDebit,
            ];
        }

        $hasData = collect($reports)->contains(function ($report) {
            return $report['currency']->isNotEmpty()
                || $report['totalDebit'] != 0
                || $report['totalKredit'] != 0
                || $report['totalModal'] != 0;
        });

        if (!$hasData) {
            return redirect()->route('laporan-rekap-cabang.index')
                ->with('error', 'Data operasional tidak ditemukan pada periode dan cabang yang dipilih.');
        }

        $baseFilename = 'rekap-cabang ' . $data['from_date_export'] . ' sampai ' . $data['to_date_export'];
        if ($data['radio_input'] === 'pdf') {
            $periodLabel = Carbon::parse($data['from_date_export'])->format('d/m/Y') . ' - ' . Carbon::parse($data['to_date_export'])->format('d/m/Y');
            return Pdf::loadView('pages.laporan.rekap-cabang.pdf', compact('reports', 'data', 'periodLabel'))
                ->setPaper('a4', 'portrait')
                ->download($baseFilename . '.pdf');
        }

        return Excel::download(new ExcelRekapCabang($reports), $baseFilename . '.xlsx');
    }

    private function jurnalQuery($cabangId, array $data)
    {
        return Jurnal::withoutGlobalScope('cabang')
            ->where('tb_jurnal.cabang_id', $cabangId)
            ->whereDate('tb_jurnal.tanggal_jurnal', '>=', $data['from_date_export'])
            ->whereDate('tb_jurnal.tanggal_jurnal', '<=', $data['to_date_export']);
    }

    private function allowedCabangIds()
    {
        if (Auth::user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->map('intval')->all();
        }

        return array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
    }

    private function allowedCabangs()
    {
        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $this->allowedCabangIds())
            ->orderBy('cabang_name')
            ->get(['cabang_id', 'cabang_name']);
    }
}
