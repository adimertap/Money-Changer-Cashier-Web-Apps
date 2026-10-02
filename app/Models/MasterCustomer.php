<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterCabang;

class MasterCustomer extends Model
{
    protected $table = "tb_master_customer";

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'country',
        'passport',
        'pekerjaan',
        'nik',
        'tanggal_terdaftar',
        'alamat',
        'is_terduga',
        'kode_densus',
        'alias',
        'is_active',
        'created_by',
        'updated_by',
        'cabang_terdaftar',
    ];

    public $timestamps = true;

    public function cabang()
    {
        return $this->belongsTo(MasterCabang::class, 'cabang_terdaftar', 'cabang_id');
    }
}
