<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCurrency extends Model
{
    use SoftDeletes;

    /*
     * Currency is currently a global master for every branch.
     * Branch-specific scope and automatic cabang_id assignment are disabled.
     * use Concerns\BelongsToCabang;
     */

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
        'jumlah_valas',
        // 'cabang_id', // disabled: currency is global for every branch.
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $timestamps = true;
}
