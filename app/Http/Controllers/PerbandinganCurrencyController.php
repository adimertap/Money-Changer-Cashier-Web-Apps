<?php

namespace App\Http\Controllers;

use App\Models\MasterCabang;
use App\Models\MasterCurrency;
use Illuminate\Support\Facades\Auth;

class PerbandinganCurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(app(\App\Services\MenuAccessService::class)->allows('master-currency'), 403);
            return $next($request);
        });
    }

    private function cabangIds(): array
    {
        if (Auth::user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->all();
        }

        return array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
    }

    public function index()
    {
        $cabangIds = $this->cabangIds();
        $cabangs = MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $cabangIds)
            ->orderBy('cabang_name')
            ->get();

        $currencies = MasterCurrency::withoutGlobalScope('cabang')
            ->whereIn('cabang_id', $cabangIds)
            ->orderBy('jenis_kurs', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->orderBy('nama_currency', 'ASC')
            ->get();

        $rows = $currencies
            ->groupBy(fn ($item) => strtoupper(trim($item->nama_currency)) . '|' . $item->jenis_kurs)
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'nama_currency' => $first->nama_currency,
                    'jenis_kurs' => $first->jenis_kurs,
                    'keterangan' => $first->keterangan,
                    'urutan' => $first->urutan,
                    'by_cabang' => $items->keyBy('cabang_id'),
                ];
            })
            ->values();

        return view('pages.mastercurrency.perbandingan', compact('cabangs', 'rows'));
    }
}

