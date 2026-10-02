<?php

namespace App\Services;

use App\Models\AccessMenu;
use App\Models\RoleMenu;
use App\Models\User;

class MenuAccessService
{
    public function keysFor(User $user): array
    {
        if ($user->role === 'Owner') {
            return AccessMenu::orderBy('id')->pluck('menu_key')->all();
        }

        return RoleMenu::where('role', $user->role)
            ->with('menu')
            ->get()
            ->pluck('menu.menu_key')
            ->filter()
            ->values()
            ->all();
    }

    public function loadIntoSession(User $user): array
    {
        $keys = $this->keysFor($user);
        session(['allowed_menu_keys' => $keys]);

        return $keys;
    }

    public function allows($menuOrMenus): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->role === 'Owner') {
            return true;
        }

        $keys = (array) session('allowed_menu_keys', []);
        foreach ((array) $menuOrMenus as $menuKey) {
            if (in_array($menuKey, $keys, true)) {
                return true;
            }
        }

        return false;
    }
}
