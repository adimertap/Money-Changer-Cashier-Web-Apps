<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        $roles = config('access_menus.roles', []);
        $now = now();
        DB::table('roles')->insert(array_map(function ($role) use ($now) {
            return [
                'name' => $role,
                'is_system' => $role === 'Owner',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $roles));
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
