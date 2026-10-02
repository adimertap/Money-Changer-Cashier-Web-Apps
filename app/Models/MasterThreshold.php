<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterThreshold extends Model
{
    protected $table = "tb_master_threshold";

    protected $primaryKey = 'threshold_id';

    protected $fillable = [
        'bulan',
        'tahun',
        'start_date',
        'end_date',
        'nominal',
        'currency_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;
}
