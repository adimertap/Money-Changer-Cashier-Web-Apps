<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTerdugaHeader extends Model
{
    protected $table = 'tb_master_terduga_header';

    protected $primaryKey = 'terduga_header_id';

    protected $fillable = [
        'tahun',
        'file_name',
        'is_active',
        'jumlah',
        'created_by',
        'updated_by',
    ];

    public function terduga()
    {
        return $this->hasMany(MasterTerduga::class, 'terduga_header_id', 'terduga_header_id');
    }
}
