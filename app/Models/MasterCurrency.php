<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCurrency extends Model
{
    use SoftDeletes;

    protected $table = "tb_currency";

    protected $primaryKey = 'id_currency';

    protected $fillable = [
        'nama_currency',
        'country',
        'nilai_kurs',
        'img_flag',
        'jenis_kurs',
        'keterangan',
        'urutan',
        'last_nilai_jual',
        'jumlah_valas'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $timestamps = true;
}
