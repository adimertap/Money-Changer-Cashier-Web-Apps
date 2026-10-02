<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCabangIdToTbJurnal extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('tb_jurnal', 'cabang_id')) {
            return;
        }
        Schema::table('tb_jurnal', function (Blueprint $t) {
            $t->integer('cabang_id')->nullable();
            $t->foreign('cabang_id')->references('cabang_id')->on('tb_master_cabang');
        });
    }

    public function down()
    {
        Schema::table('tb_jurnal', function (Blueprint $t) {
            $t->dropForeign(['cabang_id']);
            $t->dropColumn('cabang_id');
        });
    }
}
