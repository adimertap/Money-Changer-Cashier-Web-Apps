<?php

namespace App\Models\Concerns;

use App\Models\MasterCabang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Filter data sesuai cabang aktif di session('cabang_aktif').
 * null = semua cabang (hanya Owner); Pegawai tanpa cabang tidak melihat apa pun.
 * Pakai Model::withoutGlobalScope('cabang') bila perlu lintas cabang.
 */
trait BelongsToCabang
{
    public static function bootBelongsToCabang()
    {
        static::addGlobalScope('cabang', function (Builder $query) {
            if (!Auth::check()) {
                return;
            }
            $column = $query->getModel()->qualifyColumn('cabang_id');
            $aktif = session('cabang_aktif');
            if ($aktif) {
                $query->where($column, $aktif);
            } elseif (Auth::user()->role !== 'Owner') {
                $query->whereIn($column, []);
            }
        });

        static::creating(function ($model) {
            if ($model->cabang_id === null) {
                $model->cabang_id = session('cabang_aktif');
            }
        });
    }

    public function Cabang()
    {
        return $this->belongsTo(MasterCabang::class, 'cabang_id', 'cabang_id');
    }
}
