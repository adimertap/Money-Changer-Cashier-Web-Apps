<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    protected $table = 'access_menus';

    protected $fillable = [
        'menu_key',
        'menu_label',
        'menu_group',
    ];

    public function roleMenus()
    {
        return $this->hasMany(RoleMenu::class, 'menu_id');
    }
}
