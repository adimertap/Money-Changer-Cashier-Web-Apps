<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 100)->default('Pegawai')->after('password');
            });
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role VARCHAR(100) NOT NULL");
    }

    public function down()
    {
        // Dynamic role values cannot be safely converted back if custom roles exist.
    }
}
