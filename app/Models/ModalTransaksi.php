<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModalTransaksi extends Model
{   
    protected $table = "tb_modal_transaksi";

    protected $primaryKey = 'id_modal';

    protected $fillable = [
        'id_pegawai',
        'jumlah_modal',
        'tanggal_modal',
        'status_modal',
        'keterangan_approval',
        'riwayat_modal',
        'pengajuan_tambah',
        'total_modal_backup'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id')->withTrashed();
    }
}
