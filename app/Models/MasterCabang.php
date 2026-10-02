<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCabang extends Model
{
    protected $table = "tb_master_cabang";

    protected $primaryKey = 'cabang_id';

    protected $fillable = [
        'cabang_name',
        'alamat',
        'lat',
        'lng',
        'is_active',
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_master_cabang_user', 'cabang_id', 'user_id');
    }
}
