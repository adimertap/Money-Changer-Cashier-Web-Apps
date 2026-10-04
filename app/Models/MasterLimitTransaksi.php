<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MasterLimitTransaksi extends Model
{
    protected $table = 'tb_master_limit_transaksi';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_aturan',
        'nominal_limit_idr',
        'ekuivalen_usd',
        'periode_hari',
        'use_live_kurs_usd',
        'is_active',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nominal_limit_idr' => 'float',
        'ekuivalen_usd' => 'float',
        'periode_hari' => 'integer',
        'use_live_kurs_usd' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Memastikan tabel schema tersedia di database jika belum pernah dimigrasikan
     */
    public static function ensureTableExists()
    {
        if (!Schema::hasTable('tb_master_limit_transaksi')) {
            Schema::create('tb_master_limit_transaksi', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama_aturan', 150);
                $table->decimal('nominal_limit_idr', 18, 2)->default(180000000);
                $table->decimal('ekuivalen_usd', 18, 2)->default(10000);
                $table->integer('periode_hari')->default(30);
                $table->boolean('use_live_kurs_usd')->default(false);
                $table->boolean('is_active')->default(true);
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });

            // Seed initial default rule sesuai regulasi Bank Indonesia
            DB::table('tb_master_limit_transaksi')->insert([
                'nama_aturan' => 'Validasi Kuncian Paspor Rolling 30 Hari (Regulasi Bank Indonesia)',
                'nominal_limit_idr' => 180000000,
                'ekuivalen_usd' => 10000,
                'periode_hari' => 30,
                'use_live_kurs_usd' => 0,
                'is_active' => 1,
                'keterangan' => 'Akumulasi transaksi per nomor paspor setara USD 10.000 (Rp 180 Juta) dalam 30 hari terakhir mewajibkan upload dokumen pendukung (Underlying/Form A).',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Mengambil aturan batas aktif, atau membuat record default jika belum ada
     */
    public static function getActiveLimit()
    {
        try {
            self::ensureTableExists();

            $limit = self::where('is_active', 1)->orderByDesc('id')->first();
            if (!$limit) {
                $limit = self::first();
                if (!$limit) {
                    $limit = self::create([
                        'nama_aturan' => 'Validasi Kuncian Paspor Rolling 30 Hari (Regulasi Bank Indonesia)',
                        'nominal_limit_idr' => 180000000,
                        'ekuivalen_usd' => 10000,
                        'periode_hari' => 30,
                        'use_live_kurs_usd' => 0,
                        'is_active' => 1,
                        'keterangan' => 'Batas transaksi customer per nomor paspor setara USD 10.000 (Rp 180 Juta).',
                    ]);
                }
            }
            return $limit;
        } catch (\Throwable $e) {
            // Fallback object jika terjadi kendala database
            $fallback = new self();
            $fallback->id = 1;
            $fallback->nama_aturan = 'Validasi Kuncian Paspor Rolling 30 Hari (Regulasi BI)';
            $fallback->nominal_limit_idr = 180000000;
            $fallback->ekuivalen_usd = 10000;
            $fallback->periode_hari = 30;
            $fallback->use_live_kurs_usd = false;
            $fallback->is_active = true;
            $fallback->keterangan = 'Default fallback regulasi BI';
            return $fallback;
        }
    }
}
