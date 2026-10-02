<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTerduga extends Model
{
    protected $table = "tb_master_terduga";

    protected $primaryKey = 'terduga_id';

    protected $fillable = [
        'name',
        'alias',
        'description',
        'terduga_type',
        'kode_densus',
        'tempat_lahir',
        'tanggal_lahir',
        'wn',
        'alamat',
        'file_name',
        'file_updated',
        'created_by',
        'updated_by',
        'is_clear',
        'terduga_header_id',
    ];

    public $timestamps = true;

    public function header()
    {
        return $this->belongsTo(MasterTerdugaHeader::class, 'terduga_header_id', 'terduga_header_id');
    }
}
