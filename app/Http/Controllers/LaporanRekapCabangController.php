<?php

namespace App\Http\Controllers;

use App\Exports\ExcelRekapCabang;
use App\Models\Jurnal;
use App\Models\MasterCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class LaporanRekapCabangController extends Controller
{
    public function index()
    {
        $cabangs = $this->allowedCabangs();
        return view('pages.laporan.rekap-cabang.index', compact('cabangs'));
    }

    public function download(Request $request)
    {
        $allowed = $this->allowedCabangIds();
        $data = $request->validate([
            'radio_input' => ['required', 'in:excel'],
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
        $reports = [];

        foreach ($cabangs as $cabang) {
            $currency = $this->jurnalQuery($cabang->cabang_id, $data)
                ->join('tb_currency', 'tb_jurnal.id_currency', '=', 'tb_currency.id_currency')
                ->where('tb_jurnal.jenis_jurnal', 'Debit')
                ->selectRaw('tb_currency.nama_currency as nama, SUM(tb_jurnal.jumlah_tukar) as total, tb_jurnal.kurs as jumlah_kurs')
                ->groupBy('tb_currency.nama_currency', 'tb_jurnal.kurs')
                ->orderBy('tb_currency.nama_currency')
                ->get();

            $totalDebit = $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Debit')
                ->sum('total_tukar');
            $totalKredit = $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Kredit Jual')
                ->sum('total_tukar');
            $totalModal = $this->jurnalQuery($cabang->cabang_id, $data)
                ->where('jenis_jurnal', 'Kredit')
                ->sum('jumlah_modal');

            $reports[] = [
                'cabang' => $cabang,
                'currency' => $currency,
                'totalDebit' => (float) $totalDebit,
                'totalKredit' => (float) $totalKredit,
                'totalModal' => (float) $totalModal,
                'grand' => (float) $totalKredit + (float) $totalModal - (float) $totalDebit,
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

        $filename = 'rekap-cabang ' . $data['from_date_export'] . ' sampai ' . $data['to_date_export'] . '.xlsx';
        return Excel::download(new ExcelRekapCabang($reports), $filename);
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
