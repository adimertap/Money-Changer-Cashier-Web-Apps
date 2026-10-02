<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCabangIdToTables extends Migration
{
    // ponytail: nama tabel detail currency belum pasti, tabel yang tidak ada dilewati
    private $tables = [
        'tb_transaksi',
        'tb_detail_transaksi',
        'tb_modal_transaksi',
        'tb_master_shift',
        'tb_jadwal_kerja',
        'tb_currency',
        'tb_detail_currency',
        'tb_master_cabang_user',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'cabang_id')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                // int signed, sama dengan tb_master_cabang.cabang_id
                $t->integer('cabang_id')->nullable();
                $t->foreign('cabang_id')->references('cabang_id')->on('tb_master_cabang');
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'cabang_id')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['cabang_id']);
                $t->dropColumn('cabang_id');
            });
        }
    }
}
