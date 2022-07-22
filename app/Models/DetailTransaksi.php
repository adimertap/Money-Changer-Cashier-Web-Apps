<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = "tb_detail_transaksi";

    protected $primaryKey = 'id_detail_transaksi';

    protected $fillable = [
        'id_transaksi',
        'id_currency',
        'jumlah_currency',
        'jumlah_tukar',
        'total_tukar',
    ];

    protected $hidden =[ 
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function Transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi','id_transaksi');
    }
}