<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSummaryValasAccessMenu extends Migration
{
    public function up()
    {
        if (!DB::table('access_menus')->where('menu_key', 'summary-valas')->exists()) {
            DB::table('access_menus')->insert([
                'menu_key' => 'summary-valas',
                'menu_label' => 'Summary Valas',
                'menu_group' => 'Pelaporan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        $menuId = DB::table('access_menus')->where('menu_key', 'summary-valas')->value('id');
        if ($menuId) {
            DB::table('role_menus')->where('menu_id', $menuId)->delete();
            DB::table('access_menus')->where('id', $menuId)->delete();
        }
    }
}
