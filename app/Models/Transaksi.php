<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'total',
        'nama_customer',
        'nomor_passport',
        'negara_asal'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    public $timestamps = true;

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi','id_transaksi');
    }

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id')->withTrashed();
    }

    public function Modal()
    {
        return $this->belongsTo(ModalTransaksi::class, 'id_modal', 'id_modal');
    }

    public static function getId()
    {
        $getId = DB::table('tb_transaksi')->orderBy('id_transaksi', 'DESC')->take(1)->get();
        if (count($getId) > 0) return $getId;
        return (object)[
            (object)[
                'id_transaksi' => 0
            ]
        ];
    }
}
