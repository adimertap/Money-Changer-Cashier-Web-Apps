<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nama_panggilan',
        'jenis_kelamin',
        'phone_number',
        'alamat',
        'role',
        'last_activity'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cabangs()
    {
        return $this->belongsToMany(MasterCabang::class, 'tb_master_cabang_user', 'user_id', 'cabang_id')
            ->withPivot('created_by', 'updated_by')
            ->withTimestamps();
    }

    // user yang ter-assign ke cabang aktif di header; null (semua cabang) = tanpa filter
    public function scopeDiCabangAktif($query)
    {
        return $query->when(session('cabang_aktif'), function ($q, $id) {
            $q->whereHas('cabangs', fn ($c) => $c->where('tb_master_cabang.cabang_id', $id));
        });
    }
}
