<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogEdit extends Model
{

    use SoftDeletes;

    protected $table = "new_log_edit";

    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_pegawai',
        'id_modal',
        'tanggal_transaksi',
        'kode_transaksi',
        'total',
        'keterangan_log'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    public $timestamps = true;

    public function detailLog()
    {
        return $this->hasMany(LogEditDetail::class, 'id_log','id_log');
    }

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id')->withTrashed();
    }

    public function Modal()
    {
        return $this->belongsTo(ModalTransaksi::class, 'id_modal', 'id_modal');
    }

}
