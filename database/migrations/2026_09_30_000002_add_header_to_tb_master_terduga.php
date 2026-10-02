<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderToTbMasterTerduga extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tb_master_terduga') || Schema::hasColumn('tb_master_terduga', 'terduga_header_id')) {
            return;
        }

        Schema::table('tb_master_terduga', function (Blueprint $table) {
            $table->integer('terduga_header_id')->nullable()->after('terduga_id');
            $table->foreign('terduga_header_id')
                ->references('terduga_header_id')
                ->on('tb_master_terduga_header')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('tb_master_terduga') || !Schema::hasColumn('tb_master_terduga', 'terduga_header_id')) {
            return;
        }

        Schema::table('tb_master_terduga', function (Blueprint $table) {
            $table->dropForeign(['terduga_header_id']);
            $table->dropColumn('terduga_header_id');
        });
    }
}
