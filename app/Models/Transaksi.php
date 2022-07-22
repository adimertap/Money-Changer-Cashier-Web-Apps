<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = "tb_transaksi";

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_pegawai',
        'id_modal',
        'tanggal_transaksi',
        'kode_transaksi',
        'total'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $timestamps = true;

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id');
    }

    public function Modal()
    {
        return $this->belongsTo(ModalTransaksi::class, 'id_modal', 'id_modal');
    }
}
