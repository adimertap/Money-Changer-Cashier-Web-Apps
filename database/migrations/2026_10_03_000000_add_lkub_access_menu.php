<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLkubAccessMenu extends Migration
{
    public function up()
    {
        if (!DB::table('access_menus')->where('menu_key', 'lkub')->exists()) {
            DB::table('access_menus')->insert([
                'menu_key' => 'lkub',
                'menu_label' => 'LKUB',
                'menu_group' => 'Pelaporan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        $menuId = DB::table('access_menus')->where('menu_key', 'lkub')->value('id');
        if ($menuId) {
            DB::table('role_menus')->where('menu_id', $menuId)->delete();
            DB::table('access_menus')->where('id', $menuId)->delete();
        }
    }
}
