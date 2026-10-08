<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterCabang;

class MasterCustomer extends Model
{
    protected $table = "tb_master_customer";

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'country',
        'passport',
        'nik',
        'is_terduga',
        'kode_densus',
        'alias',
        'is_active',
        'created_by',
        'updated_by',
        'cabang_terdaftar',
        'npwp',
        'domicile',
        'income',
        'job',
        'company',
        'company_form',
        'position',
        'business_sector',
        'transaction_purpose',
        'relationship',
        'source_of_funds',
        'supporting_document_file',
    ];

    public $timestamps = true;

    public function cabang()
    {
        return $this->belongsTo(MasterCabang::class, 'cabang_terdaftar', 'cabang_id');
    }
}
