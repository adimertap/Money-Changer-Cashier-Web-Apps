<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAccessMenuTables extends Migration
{
    public function up()
    {
        $menus = config('access_menus.menus', []);
        $defaults = config('access_menus.defaults', []);

        Schema::create('access_menus', function (Blueprint $table) {
            $table->id();
            $table->string('menu_key')->unique();
            $table->string('menu_label');
            $table->string('menu_group')->nullable();
            $table->timestamps();
        });

        Schema::create('role_menus', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index();
            $table->foreignId('menu_id')->constrained('access_menus')->cascadeOnDelete();
            $table->unique(['role', 'menu_id']);
            $table->timestamps();
        });

        $now = now();
        DB::table('access_menus')->insert(array_map(function ($menu) use ($now) {
            return [
                'menu_key' => $menu['key'],
                'menu_label' => $menu['label'],
                'menu_group' => $menu['group'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $menus));

        $menuIds = DB::table('access_menus')->pluck('id', 'menu_key');
        $rows = [];
        foreach ($defaults as $role => $keys) {
            foreach ($keys as $key) {
                if (isset($menuIds[$key])) {
                    $rows[] = [
                        'role' => $role,
                        'menu_id' => $menuIds[$key],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if ($rows) {
            DB::table('role_menus')->insert($rows);
        }
    }

    public function down()
    {
        Schema::dropIfExists('role_menus');
        Schema::dropIfExists('access_menus');
    }
}
