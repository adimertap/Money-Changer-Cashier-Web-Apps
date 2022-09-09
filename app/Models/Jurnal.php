<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{

    protected $table = "tb_jurnal";

    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_transaksi',
        'id_modal',
        'tanggal_jurnal',
        'id_currency',
        'kurs',
        'jumlah_tukar',
        'jumlah_modal',
        'total_tukar',
        'jenis_jurnal',
        'id_pegawai'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public $timestamps = true;

    public function Transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi')->withTrashed();
    }

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id')->withTrashed();
    }

    public function Modal()
    {
        return $this->belongsTo(ModalTransaksi::class, 'id_modal', 'id_modal');
    }

    public function Currency()
    {
        return $this->belongsTo(MasterCurrency::class, 'id_currency', 'id_currency')->withTrashed();
    }
}
