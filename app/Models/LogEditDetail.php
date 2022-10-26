<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEditDetail extends Model
{
    protected $table = "new_detail_log_edit";

    protected $primaryKey = 'id_detail_log';

    protected $fillable = [
        'id_log',
        'currency_id',
        'jumlah_currency',
        'jumlah_tukar',
        'total_tukar',
    ];

    protected $hidden =[ 
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function Log()
    {
        return $this->belongsTo(LogEdit::class, 'id_log','id_log')->withTrashed();
    }

    public function Currency()
    {
        return $this->belongsTo(MasterCurrency::class, 'currency_id','id_currency')->withTrashed();
    }

}
