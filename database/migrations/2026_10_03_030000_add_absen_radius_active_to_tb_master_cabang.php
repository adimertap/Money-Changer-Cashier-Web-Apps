<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tb_master_cabang') && !Schema::hasColumn('tb_master_cabang', 'absen_radius_active')) {
            Schema::table('tb_master_cabang', function (Blueprint $table) {
                $table->boolean('absen_radius_active')->default(true)->after('radius');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tb_master_cabang') && Schema::hasColumn('tb_master_cabang', 'absen_radius_active')) {
            Schema::table('tb_master_cabang', function (Blueprint $table) {
                $table->dropColumn('absen_radius_active');
            });
        }
    }
};
