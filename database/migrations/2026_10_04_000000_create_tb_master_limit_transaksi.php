<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTbMasterLimitTransaksi extends Migration
{
    public function up()
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

        if (Schema::hasTable('access_menus')) {
            if (!DB::table('access_menus')->where('menu_key', 'master-limit-transaksi')->exists()) {
                DB::table('access_menus')->insert([
                    'menu_key' => 'master-limit-transaksi',
                    'menu_label' => 'Batas Kuncian Paspor',
                    'menu_group' => 'Master Data',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('tb_master_limit_transaksi');

        if (Schema::hasTable('access_menus')) {
            $menuId = DB::table('access_menus')->where('menu_key', 'master-limit-transaksi')->value('id');
            if ($menuId) {
                DB::table('role_menus')->where('menu_id', $menuId)->delete();
                DB::table('access_menus')->where('id', $menuId)->delete();
            }
        }
    }
}
