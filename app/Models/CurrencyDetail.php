<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyDetail extends Model
{
    protected $table = "tb_det_currency";

    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_currency',
        'keterangan',
        'nilai_baru'
    ];

    protected $hidden =[ 
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function Kurs()
    {
        return $this->belongsTo(MasterCurrency::class, 'id_currency','id_currency');
    }
}
