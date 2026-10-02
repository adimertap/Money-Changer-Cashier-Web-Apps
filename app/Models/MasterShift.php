<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterShift extends Model
{
    use Concerns\BelongsToCabang;

    protected $table = "tb_master_shift";

    protected $primaryKey = 'shift_id';

    protected $fillable = [
        'shift_name',
        'shift_in',
        'shift_out',
        'cabang_id',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public $timestamps = true;
}
