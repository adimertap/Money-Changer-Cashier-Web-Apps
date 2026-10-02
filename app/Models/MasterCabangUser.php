<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCabangUser extends Model
{
    protected $table = "tb_master_cabang_user";

    protected $primaryKey = 'cabang_user_id';

    protected $fillable = [
        'user_id',
        'cabang_id',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;
}
