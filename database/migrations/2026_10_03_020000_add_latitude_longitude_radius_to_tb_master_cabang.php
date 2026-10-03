<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLatitudeLongitudeRadiusToTbMasterCabang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tb_master_cabang')) {
            Schema::table('tb_master_cabang', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_master_cabang', 'latitude')) {
                    $table->decimal('latitude', 11, 8)->nullable()->after('alamat');
                }
                if (!Schema::hasColumn('tb_master_cabang', 'longitude')) {
                    $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
                }
                if (!Schema::hasColumn('tb_master_cabang', 'radius')) {
                    $table->integer('radius')->default(50)->after('longitude')->comment('Radius absensi dalam meter');
                }
                if (!Schema::hasColumn('tb_master_cabang', 'absen_radius_active')) {
                    $table->boolean('absen_radius_active')->default(true)->after('radius')->comment('Status aktif radius checking untuk absensi');
                }
            });

            // Sinkronisasi data lat/lng lama jika kolom lat/lng ada
            if (Schema::hasColumn('tb_master_cabang', 'lat') && Schema::hasColumn('tb_master_cabang', 'latitude')) {
                DB::statement("UPDATE tb_master_cabang SET latitude = lat WHERE latitude IS NULL AND lat IS NOT NULL");
            }
            if (Schema::hasColumn('tb_master_cabang', 'lng') && Schema::hasColumn('tb_master_cabang', 'longitude')) {
                DB::statement("UPDATE tb_master_cabang SET longitude = lng WHERE longitude IS NULL AND lng IS NOT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tb_master_cabang')) {
            Schema::table('tb_master_cabang', function (Blueprint $table) {
                if (Schema::hasColumn('tb_master_cabang', 'absen_radius_active')) {
                    $table->dropColumn('absen_radius_active');
                }
                if (Schema::hasColumn('tb_master_cabang', 'radius')) {
                    $table->dropColumn('radius');
                }
                if (Schema::hasColumn('tb_master_cabang', 'longitude')) {
                    $table->dropColumn('longitude');
                }
                if (Schema::hasColumn('tb_master_cabang', 'latitude')) {
                    $table->dropColumn('latitude');
                }
            });
        }
    }
}
