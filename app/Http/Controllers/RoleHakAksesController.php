<?php

namespace App\Http\Controllers;

use App\Models\AccessMenu;
use App\Models\Role;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleHakAksesController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $editableRoles = $roles->where('is_system', false);
        $menus = AccessMenu::orderBy('menu_group')->orderBy('menu_label')->get()->groupBy('menu_group');
        $roleMenus = RoleMenu::whereIn('role', $editableRoles->pluck('name'))
            ->with('menu')
            ->get()
            ->groupBy('role')
            ->map(function ($items) {
                return $items->pluck('menu.menu_key')->filter()->values()->all();
            });

        return view('pages.rolehakakses.index', compact('roles', 'editableRoles', 'menus', 'roleMenus'));
    }

    public function store(Request $request)
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[^<>]+$/', 'unique:roles,name'],
        ]);

        Role::create(['name' => $data['name']]);

        return redirect()
            ->route('role-hak-akses.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, $role)
    {
        abort_unless(Role::where('name', $role)->where('is_system', false)->exists(), 403);

        $data = $request->validate([
            'menu_keys' => 'nullable|array',
            'menu_keys.*' => 'string|exists:access_menus,menu_key',
        ]);

        $menuIds = AccessMenu::whereIn('menu_key', $data['menu_keys'] ?? [])
            ->pluck('id')
            ->all();

        DB::transaction(function () use ($role, $menuIds) {
            RoleMenu::where('role', $role)->delete();

            $now = now();
            $rows = array_map(function ($menuId) use ($role, $now) {
                return [
                    'role' => $role,
                    'menu_id' => $menuId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $menuIds);

            if ($rows) {
                RoleMenu::insert($rows);
            }
        });

        return redirect()
            ->route('role-hak-akses.index')
            ->with('success', 'Hak akses menu berhasil diperbarui.');
    }
}
