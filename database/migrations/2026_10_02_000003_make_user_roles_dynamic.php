<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeUserRolesDynamic extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(100) NOT NULL");
        }
    }

    public function down()
    {
        // Dynamic role values cannot be safely converted back if custom roles exist.
    }
}
