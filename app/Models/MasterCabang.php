<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCabang extends Model
{
    protected $table = "tb_master_cabang";

    protected $primaryKey = 'cabang_id';

    protected $fillable = [
        'cabang_name',
        'alamat',
        'lat',
        'lng',
        'latitude',
        'longitude',
        'radius',
        'absen_radius_active',
        'is_active',
    ];

    public function getAbsenRadiusActiveAttribute($value)
    {
        return $value !== null ? (bool) $value : true;
    }

    public function getLatitudeAttribute($value)
    {
        return $value ?? ($this->attributes['lat'] ?? null);
    }

    public function getLongitudeAttribute($value)
    {
        return $value ?? ($this->attributes['lng'] ?? null);
    }

    public function getRadiusAttribute($value)
    {
        return $value !== null ? (int) $value : 50;
    }

    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = $value;
        $this->attributes['lat'] = $value;
    }

    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = $value;
        $this->attributes['lng'] = $value;
    }

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany(User::class, 'tb_master_cabang_user', 'cabang_id', 'user_id');
    }
}
