<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKerja extends Model
{
    protected $table = "tb_jadwal_kerja";

    protected $primaryKey = 'jadwal_id';

    protected $fillable = [
        'shift_id',
        'id',
        'tanggal',
        'tanggal_akhir',
        'keterangan',
        'month',
        'year',
        'status',
        'jam_masuk',
        'jam_keluar',
        'status_absen_in',
        'status_absen_out'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public $timestamps = true;

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function Shift(): BelongsTo
    {
        return $this->belongsTo(MasterShift::class, 'shift_id', 'shift_id');
    }
}
