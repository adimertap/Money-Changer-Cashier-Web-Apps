<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPasporNominal extends Model
{
    use Concerns\BelongsToCabang;

    protected $table = "tb_history_paspor_nominal";

    protected $primaryKey = 'history_paspor_id';

    protected $fillable = [
        'paspor',
        'user_id',
        'nominal',
        'tanggal_transaksi',
        'bulan',
        'tahun',
        'kode_transaksi',
        'currency_id',
        'jumlah',
        'created_by',
        'updated_by',
        'cabang_id',
    ];

    public $timestamps = true;
}
