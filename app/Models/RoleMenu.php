<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    protected $table = 'role_menus';

    protected $fillable = [
        'role',
        'menu_id',
    ];

    public function menu()
    {
        return $this->belongsTo(AccessMenu::class, 'menu_id');
    }
}
