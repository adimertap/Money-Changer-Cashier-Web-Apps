# Role Menu Access Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add configurable sidebar-menu visibility for `Owner`, `Pegawai`, `Admin Cabang`, and `Kasir` while preserving the existing login redirect, branch session behavior, and business routes.

**Architecture:** Store a stable sidebar menu catalog in `access_menus` and role assignments in `role_menus`. A small menu-access service loads allowed menu keys into `allowed_menu_keys` at login and exposes one Blade conditional directive; `Owner` bypasses the stored assignments. An Owner-only controller and Blade page replace one role's menu assignments transactionally, while the existing Master Pegawai screens assign one of the four direct values already stored in `users.role`.

**Tech Stack:** Laravel 8, PHP `^7.3|^8.0`, MySQL, Eloquent, Blade, PHPUnit, existing Bootstrap/Falcon layout.

**Spec:** `docs/superpowers/specs/2026-10-01-role-menu-access-design.md`

## Global Constraints

- The four role values are exactly `Owner`, `Pegawai`, `Admin Cabang`, and `Kasir`.
- Access is sidebar-menu visibility only; do not add route/controller guards or per-action permissions.
- `Owner` always sees every cataloged menu, even when role-menu rows are missing.
- Non-Owner users fail closed when `allowed_menu_keys` is absent or empty.
- Preserve existing `cabangs`, `cabang_aktif`, and non-Owner redirect behavior in `LoginController::authenticated()`.
- Preserve existing `Owner`, `Pegawai`, and `jadwal.checking` middleware behavior.
- Do not add an authorization package or modify transaction, journal, branch, attendance, or export business logic.
- Do not use PowerShell; use Bash syntax for any shell command.
- Do not run automated tests or verification commands during implementation; the user will perform testing manually after the code changes.
- The navbar-filtering task remains required because it is the actual sidebar visibility implementation; only its test/verification execution is deferred to the user.

## Review Focus

- An existing database role outside the four allowed values must be reported and must not be silently reassigned; pin this in the migration test/check in Task 1.
- An Owner with no `role_menus` rows must still see every menu; pin this in the service test in Task 2.
- A non-Owner with no `allowed_menu_keys` session value must see no restricted menu; pin this in the service test in Task 2.
- Removing a checkbox must remove its pivot row rather than leaving stale access; pin this in the role update feature test in Task 3.
- A self-role change must refresh both menu permissions and the existing branch-active session behavior; pin this in the controller test/check in Task 4.

---

## File Map

Create these focused files:

- `config/access_menus.php` — canonical menu keys, labels, groups, editable roles, and initial non-Owner assignments.
- `database/migrations/2026_10_01_000002_create_access_menu_tables.php` — creates `access_menus` and `role_menus`, validates existing role values, and seeds the catalog/default assignments.
- `app/Models/AccessMenu.php` — Eloquent model for the menu catalog.
- `app/Models/RoleMenu.php` — Eloquent model for role-to-menu assignments.
- `app/Services/MenuAccessService.php` — resolves allowed menu keys and writes the login/session representation.
- `app/Http/Controllers/RoleHakAksesController.php` — Owner-only checklist page and transactional role assignment update.
- `resources/views/pages/rolehakakses/index.blade.php` — grouped checklists for `Pegawai`, `Admin Cabang`, and `Kasir`, plus non-editable Owner “Semua Menu” status.
- `tests/Unit/MenuAccessServiceTest.php` — Owner bypass and non-Owner fail-closed behavior.
- `tests/Feature/RoleHakAksesTest.php` — Owner page, validation, replacement semantics, and non-Owner denial.

Modify these existing files:

- `app/Providers/AppServiceProvider.php` — register the single `@menuAccess(...)` Blade conditional directive.
- `app/Http/Controllers/Auth/LoginController.php` — load menu keys during `authenticated()` without changing redirects or branch selection.
- `app/Http/Controllers/MasterPegawaiController.php` — validate the four roles and refresh the current session after a self-update.
- `resources/views/pages/masterpegawai/create.blade.php` — add `Admin Cabang` and `Kasir` options.
- `resources/views/pages/masterpegawai/edit.blade.php` — add `Admin Cabang` and `Kasir` options and preserve the selected value.
- `routes/web.php` — add the Owner-only Role & Hak Akses endpoints under the existing `owner` prefix.
- `resources/views/layouts/navbar.blade.php` — replace unconditional/Owner-only sidebar visibility checks with menu-key checks and preserve parent-group behavior.

No route/controller business-flow files outside the listed role/session files should be changed.

---

### Task 1: Catalog, Tables, and Initial Assignments

**Files:**
- Create: `config/access_menus.php`
- Create: `database/migrations/2026_10_01_000002_create_access_menu_tables.php`
- Create: `app/Models/AccessMenu.php`
- Create: `app/Models/RoleMenu.php`
- Test: `tests/Feature/AccessMenuMigrationTest.php`

**Interfaces:**
- Produces `config('access_menus.roles')`, `config('access_menus.editable_roles')`, and `config('access_menus.menus')` for later tasks.
- Produces `AccessMenu::whereIn('menu_key', $keys)` and `RoleMenu::where('role', $role)` query models.
- The migration must leave `users.role` as the existing direct role field; do not add `access_role`.

- [ ] **Step 1: Write the failing catalog test**

Create a database-backed feature test that asserts the four roles and representative menu keys are available after migrations:

```php
public function test_catalog_contains_four_roles_and_expected_sidebar_keys()
{
    $this->assertSame(
        ['Owner', 'Pegawai', 'Admin Cabang', 'Kasir'],
        config('access_menus.roles')
    );

    $keys = \App\Models\AccessMenu::pluck('menu_key')->all();

    $this->assertContains('dashboard', $keys);
    $this->assertContains('master-pegawai', $keys);
    $this->assertContains('role-hak-akses', $keys);
    $this->assertContains('transaksi', $keys);
    $this->assertContains('jurnal-debit-kredit', $keys);
    $this->assertContains('laporan-absensi', $keys);
    $this->assertContains('laporan-rekap-cabang', $keys);
}
```

Add a second test that reads distinct `users.role` values before the access migration and expects an invalid value to cause a clear migration exception rather than being rewritten. Use a temporary test database row such as `Legacy Role` only if the test database schema supports the project’s existing user columns; otherwise keep this as a manual migration preflight check documented in the test file.

- [ ] **Step 2: Run the focused test and verify it fails**

Run:

```bash
vendor/bin/phpunit tests/Feature/AccessMenuMigrationTest.php -v
```

Expected: FAIL because the config, models, tables, and seeded rows do not exist yet.

- [ ] **Step 3: Add the canonical menu catalog**

Create `config/access_menus.php` with these exact keys and groups:

```php
return [
    'roles' => ['Owner', 'Pegawai', 'Admin Cabang', 'Kasir'],
    'editable_roles' => ['Pegawai', 'Admin Cabang', 'Kasir'],
    'menus' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'group' => 'Dashboard'],
        ['key' => 'web-exchange', 'label' => 'Web Exchange', 'group' => 'Dashboard'],
        ['key' => 'master-cabang', 'label' => 'Cabang', 'group' => 'Master Data'],
        ['key' => 'master-pegawai', 'label' => 'Pegawai', 'group' => 'Master Data'],
        ['key' => 'role-hak-akses', 'label' => 'Role & Hak Akses', 'group' => 'Master Data'],
        ['key' => 'master-currency', 'label' => 'Currency', 'group' => 'Master Data'],
        ['key' => 'master-customer', 'label' => 'Customer', 'group' => 'Master Data'],
        ['key' => 'master-terduga', 'label' => 'Terduga', 'group' => 'Master Data'],
        ['key' => 'master-threshold', 'label' => 'Batas Atas Transaksi', 'group' => 'Master Data'],
        ['key' => 'shift', 'label' => 'Shift', 'group' => 'Jadwal'],
        ['key' => 'jadwal', 'label' => 'Jadwal', 'group' => 'Jadwal'],
        ['key' => 'jadwal-user', 'label' => 'Jadwal & Absen', 'group' => 'Jadwal'],
        ['key' => 'modal', 'label' => 'Modal', 'group' => 'Transaction'],
        ['key' => 'transaksi', 'label' => 'Transaksi', 'group' => 'Transaction'],
        ['key' => 'transaksi-jual', 'label' => 'Jual Valas', 'group' => 'Transaction'],
        ['key' => 'rekapan-hari-ini', 'label' => 'Rekapan Hari Ini', 'group' => 'Pelaporan'],
        ['key' => 'seluruh-transaksi', 'label' => 'Seluruh Transaksi', 'group' => 'Pelaporan'],
        ['key' => 'jurnal-bulanan', 'label' => 'Jurnal Bulanan', 'group' => 'Pelaporan'],
        ['key' => 'jurnal-debit-kredit', 'label' => 'Jurnal Debit Kredit', 'group' => 'Pelaporan'],
        ['key' => 'laporan-absensi', 'label' => 'Laporan Absensi', 'group' => 'Pelaporan'],
        ['key' => 'laporan-harian', 'label' => 'Laporan Harian', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-pegawai', 'label' => 'Laporan Pegawai', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-saya', 'label' => 'Laporan Saya', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-rekap-cabang', 'label' => 'Rekapitulasi Cabang (Excel)', 'group' => 'Pelaporan'],
        ['key' => 'approval-modal', 'label' => 'Approval Modal', 'group' => 'Log dan Approval'],
        ['key' => 'log-transaksi', 'label' => 'Log Transaksi', 'group' => 'Log dan Approval'],
    ],
    'defaults' => [
        'Pegawai' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
        'Admin Cabang' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
        'Kasir' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
    ],
];
```

The defaults preserve the current non-Owner sidebar visibility. The catalog includes existing Owner-only links so the Owner can see and administer a complete sidebar inventory; existing route middleware remains unchanged.

- [ ] **Step 4: Create the migration and Eloquent models**

Create `access_menus` with a unique `menu_key`, label, group, and timestamps. Create `role_menus` with indexed `role`, foreign-keyed `menu_id`, unique `(role, menu_id)`, and timestamps. Before inserting rows, query distinct non-null `users.role` values and throw a `RuntimeException` listing any value not in `config('access_menus.roles')`; do not silently update legacy data. Insert every configured menu, then insert default role rows from `config('access_menus.defaults')` using menu-key lookup. Make `down()` drop `role_menus` before `access_menus`.

`AccessMenu` should define `fillable` for `menu_key`, `menu_label`, and `menu_group`, plus a `roleMenus()` relation. `RoleMenu` should define `fillable` for `role` and `menu_id`, plus a `menu()` relation and explicit `$table = 'role_menus'` if needed by the project naming conventions.

- [ ] **Step 5: Hand off migration testing to the user**

Do not run PHPUnit, PHP lint, or any other verification command. Tell the user to verify that the catalog/default rows migrate correctly and that invalid legacy roles are reported rather than silently reassigned.

- [ ] **Step 6: Commit the catalog layer**

```bash
git add config/access_menus.php database/migrations/2026_10_01_000002_create_access_menu_tables.php app/Models/AccessMenu.php app/Models/RoleMenu.php tests/Feature/AccessMenuMigrationTest.php
git commit -m "feat: add role menu catalog tables"
```

---

### Task 2: Session Resolver and Blade Helper

**Files:**
- Create: `app/Services/MenuAccessService.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Unit/MenuAccessServiceTest.php`

**Interfaces:**
- Produces `MenuAccessService::keysFor(User $user): array`.
- Produces `MenuAccessService::loadIntoSession(User $user): array` and writes `allowed_menu_keys`.
- Produces one Blade conditional directive: `@menuAccess('menu-key')` or `@menuAccess(['key-a', 'key-b'])`; arrays use “any visible key” semantics.

- [ ] **Step 1: Write failing service tests**

Use `RefreshDatabase`, authenticate a user, and assert the service contract:

```php
public function test_owner_gets_every_catalog_key_without_pivot_rows()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);
    $keys = app(\App\Services\MenuAccessService::class)->keysFor($owner);

    $this->assertSame(
        \App\Models\AccessMenu::orderBy('id')->pluck('menu_key')->all(),
        $keys
    );
}

public function test_non_owner_without_session_access_fails_closed()
{
    $user = \App\Models\User::factory()->create(['role' => 'Kasir']);
    $this->actingAs($user);
    session()->forget('allowed_menu_keys');

    $this->assertFalse(app(\App\Services\MenuAccessService::class)->allows('transaksi'));
}

public function test_loading_non_owner_uses_only_role_pivot_keys()
{
    $user = \App\Models\User::factory()->create(['role' => 'Kasir']);
    $menu = \App\Models\AccessMenu::where('menu_key', 'transaksi')->firstOrFail();
    \App\Models\RoleMenu::create(['role' => 'Kasir', 'menu_id' => $menu->id]);

    $keys = app(\App\Services\MenuAccessService::class)->loadIntoSession($user);

    $this->assertSame(['transaksi'], $keys);
    $this->assertSame(['transaksi'], session('allowed_menu_keys'));
}
```

- [ ] **Step 2: Run the focused tests and verify failure**

Run:

```bash
vendor/bin/phpunit tests/Unit/MenuAccessServiceTest.php -v
```

Expected: FAIL because the service and Blade directive do not exist.

- [ ] **Step 3: Implement the minimal service**

Implement `keysFor(User $user)` so Owner returns all catalog keys ordered by menu ID and every other role returns `RoleMenu::where('role', $user->role)->with('menu')->get()->pluck('menu.menu_key')->filter()->values()->all()`. Implement `loadIntoSession()` by calling `keysFor()` and `session(['allowed_menu_keys' => $keys])`. Implement `allows($menuOrMenus)` so Owner returns true, non-Owner checks strict membership in `session('allowed_menu_keys', [])`, and an array returns true when at least one key is allowed. Never treat a missing session value as all access.

- [ ] **Step 4: Register the one Blade directive**

Import `Illuminate\Support\Facades\Blade` and `App\Services\MenuAccessService` in `AppServiceProvider`, then register:

```php
Blade::if('menuAccess', function ($menuOrMenus) {
    return app(MenuAccessService::class)->allows($menuOrMenus);
});
```

Do not add a second global helper or duplicate database queries in Blade.

- [ ] **Step 5: Hand off service testing to the user**

Do not run PHPUnit, Blade compilation, PHP lint, or any other verification command. Tell the user to verify Owner bypass, non-Owner fail-closed behavior, and rendering of the `@menuAccess('transaksi')` directive.

- [ ] **Step 6: Commit the resolver layer**

```bash
git add app/Services/MenuAccessService.php app/Providers/AppServiceProvider.php tests/Unit/MenuAccessServiceTest.php
git commit -m "feat: add session-backed menu access helper"
```

---

### Task 3: Owner Role & Hak Akses Management

**Files:**
- Create: `app/Http/Controllers/RoleHakAksesController.php`
- Create: `resources/views/pages/rolehakakses/index.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/RoleHakAksesTest.php`

**Interfaces:**
- `GET /owner/role-hak-akses` named `role-hak-akses.index` returns `roles`, `editableRoles`, and grouped `menus`.
- `PUT /owner/role-hak-akses/{role}` named `role-hak-akses.update` accepts `menu_keys[]` and replaces that role’s assignments.
- Only `Pegawai`, `Admin Cabang`, and `Kasir` are editable; Owner is displayed as “Semua Menu” and cannot be updated through this page.

- [ ] **Step 1: Write failing feature tests**

Add tests for Owner access and replacement semantics:

```php
public function test_owner_can_view_role_access_page()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);

    $response = $this->actingAs($owner)->get(route('role-hak-akses.index'));

    $response->assertOk()->assertSee('Role & Hak Akses')->assertSee('Admin Cabang');
}

public function test_owner_update_replaces_removed_menu_rows()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);
    $transaksi = \App\Models\AccessMenu::where('menu_key', 'transaksi')->firstOrFail();
    $modal = \App\Models\AccessMenu::where('menu_key', 'modal')->firstOrFail();
    \App\Models\RoleMenu::insert([
        ['role' => 'Kasir', 'menu_id' => $transaksi->id, 'created_at' => now(), 'updated_at' => now()],
        ['role' => 'Kasir', 'menu_id' => $modal->id, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $response = $this->actingAs($owner)->put(route('role-hak-akses.update', 'Kasir'), [
        'menu_keys' => ['transaksi'],
    ]);

    $response->assertRedirect(route('role-hak-akses.index'));
    $this->assertDatabaseHas('role_menus', ['role' => 'Kasir', 'menu_id' => $transaksi->id]);
    $this->assertDatabaseMissing('role_menus', ['role' => 'Kasir', 'menu_id' => $modal->id]);
}

public function test_owner_cannot_edit_owner_checklist_and_non_owner_cannot_open_page()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);
    $kasir = \App\Models\User::factory()->create(['role' => 'Kasir']);

    $this->actingAs($owner)->put(route('role-hak-akses.update', 'Owner'), ['menu_keys' => []])->assertForbidden();
    $this->actingAs($kasir)->get(route('role-hak-akses.index'))->assertForbidden();
}
```

- [ ] **Step 2: Run the feature tests and verify failure**

Run:

```bash
vendor/bin/phpunit tests/Feature/RoleHakAksesTest.php -v
```

Expected: FAIL because routes, controller, and view do not exist.

- [ ] **Step 3: Add the Owner-only routes under the existing groups**

Inside the authenticated `jadwal.checking` group and existing `Route::prefix('owner')->middleware(['Owner'])` group, add:

```php
Route::get('role-hak-akses', [\App\Http\Controllers\RoleHakAksesController::class, 'index'])
    ->name('role-hak-akses.index');
Route::put('role-hak-akses/{role}', [\App\Http\Controllers\RoleHakAksesController::class, 'update'])
    ->name('role-hak-akses.update');
```

Do not move or broaden the existing middleware group.

- [ ] **Step 4: Implement transactional replacement**

`index()` should load `config('access_menus.roles')`, `config('access_menus.editable_roles')`, and `AccessMenu::orderBy('menu_group')->orderBy('menu_label')->get()->groupBy('menu_group')`, then render the view.

`update(Request $request, $role)` should validate `$role` against `config('access_menus.editable_roles')` and `menu_keys` as `nullable|array` with each key `exists:access_menus,menu_key`. Inside `DB::transaction`, delete `RoleMenu` rows for that role and insert one timestamped row per validated menu key. Redirect to `role-hak-akses.index` with a success flash. An empty checklist must delete every row for that role.

- [ ] **Step 5: Build the grouped checklist view**

Render an Owner summary row:

```blade
<div class="alert alert-info">Owner: Semua Menu (selalu memiliki akses)</div>
```

For each editable role, render one PUT form with `@csrf`, `@method('PUT')`, hidden/checkbox inputs named `menu_keys[]`, grouped by `menu_group`, and checked state from the role’s current menu keys. Use stable `id` values such as `menu-{{ Str::slug($role) }}-{{ Str::slug($menu->menu_key) }}` and labels from `menu_label`. Submit each role separately so removing a checkbox has deterministic replacement semantics.

- [ ] **Step 6: Hand off testing to the user**

Do not run PHPUnit, Artisan, PHP lint, or any other verification command. Tell the user to verify that all role management tests pass, both named routes use the `Owner` middleware, and checkbox removals delete stale pivot rows.

- [ ] **Step 7: Commit role management**

```bash
git add app/Http/Controllers/RoleHakAksesController.php resources/views/pages/rolehakakses/index.blade.php routes/web.php tests/Feature/RoleHakAksesTest.php
git commit -m "feat: add owner role menu management"
```

---

### Task 4: Four-Role Employee Assignment and Self-Session Refresh

**Files:**
- Modify: `app/Http/Controllers/MasterPegawaiController.php`
- Modify: `resources/views/pages/masterpegawai/create.blade.php`
- Modify: `resources/views/pages/masterpegawai/edit.blade.php`
- Test: `tests/Feature/MasterPegawaiRoleTest.php`

**Interfaces:**
- `store()` and `update()` accept only the four exact role values.
- Existing `syncCabang()` behavior remains unchanged.
- A self-update refreshes `allowed_menu_keys`, `cabangs`, and `cabang_aktif` using the same role semantics as login.

- [ ] **Step 1: Write failing role validation tests**

Add tests that post each new role successfully and reject an unknown role:

```php
public function test_master_pegawai_accepts_admin_cabang_and_kasir_roles()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);

    foreach (['Admin Cabang', 'Kasir'] as $role) {
        $response = $this->actingAs($owner)->post(route('master-pegawai.store'), [
            'name' => 'User ' . $role,
            'nama_panggilan' => 'User',
            'jenis_kelamin' => 'L',
            'phone_number' => '081234567890',
            'alamat' => 'Alamat',
            'role' => $role,
            'email' => strtolower(str_replace(' ', '.', $role)) . '@example.test',
            'password' => 'password',
            'cabang_ids' => [],
        ]);

        $response->assertRedirect(route('master-pegawai.index'));
        $this->assertDatabaseHas('users', ['role' => $role]);
    }
}

public function test_master_pegawai_rejects_unknown_role()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);

    $this->actingAs($owner)->from(route('master-pegawai.create'))
        ->post(route('master-pegawai.store'), ['role' => 'Unknown'])
        ->assertSessionHasErrors('role');
}
```

- [ ] **Step 2: Run the focused tests and verify failure**

Run:

```bash
vendor/bin/phpunit tests/Feature/MasterPegawaiRoleTest.php -v
```

Expected: new roles are not consistently validated/rendered yet.

- [ ] **Step 3: Add controller validation with one role source**

Add a private method returning:

```php
private function roleRules()
{
    return ['required', 'in:' . implode(',', config('access_menus.roles'))];
}
```

Call it from both `store()` and `update()` before assigning `$request->role`, while retaining the existing field assignments and `syncCabang()` transaction. Do not change branch validation or pivot synchronization.

- [ ] **Step 4: Refresh the current user’s complete session after self-update**

Inject or resolve `MenuAccessService` in `MasterPegawaiController`. After the existing transaction, when `$pegawai->id === Auth::id()`, reload active branches and set:

```php
session([
    'cabangs' => $cabangs,
    'cabang_aktif' => $pegawai->role === 'Owner' ? null : ($cabangs[0]['cabang_id'] ?? null),
]);
app(\App\Services\MenuAccessService::class)->loadIntoSession($pegawai);
```

This keeps Owner’s all-branch behavior and gives a self-demoted user non-Owner menu/session behavior immediately. Do not change another user’s active session.

- [ ] **Step 5: Update create/edit role selects**

Replace the two-option select lists in both views with:

```blade
@foreach (config('access_menus.roles') as $role)
    <option value="{{ $role }}" {{ old('role', $item->role ?? '') === $role ? 'selected' : '' }}>{{ $role }}</option>
@endforeach
```

Use the existing create/edit variable conventions so create has no `$item` dependency and edit preserves the current role. Keep all existing branch fields and validation markup.

- [ ] **Step 6: Hand off testing to the user**

Do not run PHPUnit, Artisan, PHP lint, or any other verification command. Tell the user to verify that all four roles validate and render, unknown roles fail validation, and existing branch sync behavior remains unchanged.

- [ ] **Step 7: Commit employee role assignment**

```bash
git add app/Http/Controllers/MasterPegawaiController.php resources/views/pages/masterpegawai/create.blade.php resources/views/pages/masterpegawai/edit.blade.php tests/Feature/MasterPegawaiRoleTest.php
git commit -m "feat: support four employee roles"
```

---

### Task 5: Login Menu Session Loading

**Files:**
- Modify: `app/Http/Controllers/Auth/LoginController.php`
- Test: `tests/Feature/AuthMenuSessionTest.php`

**Interfaces:**
- `LoginController::authenticated($request, $user)` calls `MenuAccessService::loadIntoSession($user)`.
- Existing redirect remains `/` for Owner and `/transaksi/create` for every non-Owner role.
- Existing `cabangs` and `cabang_aktif` session values remain unchanged by role menu loading.

- [ ] **Step 1: Write failing authentication session tests**

Add tests for one Owner and one Kasir:

```php
public function test_owner_login_loads_all_menu_keys_and_keeps_owner_redirect()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner', 'password' => bcrypt('secret')]);

    $response = $this->post('/login', ['email' => $owner->email, 'password' => 'secret']);

    $response->assertRedirect('/');
    $this->assertSame(\App\Models\AccessMenu::pluck('menu_key')->sort()->values()->all(), collect(session('allowed_menu_keys'))->sort()->values()->all());
}

public function test_non_owner_login_loads_only_assigned_keys_and_keeps_transaction_redirect()
{
    $kasir = \App\Models\User::factory()->create(['role' => 'Kasir', 'password' => bcrypt('secret')]);
    $menu = \App\Models\AccessMenu::where('menu_key', 'transaksi')->firstOrFail();
    \App\Models\RoleMenu::create(['role' => 'Kasir', 'menu_id' => $menu->id]);

    $response = $this->post('/login', ['email' => $kasir->email, 'password' => 'secret']);

    $response->assertRedirect('/transaksi/create');
    $this->assertSame(['transaksi'], session('allowed_menu_keys'));
}
```

- [ ] **Step 2: Run tests and verify failure**

Run:

```bash
vendor/bin/phpunit tests/Feature/AuthMenuSessionTest.php -v
```

Expected: redirect behavior may pass, but `allowed_menu_keys` is missing because login does not yet load it.

- [ ] **Step 3: Load menu access without changing branch logic**

Resolve `MenuAccessService` in `authenticated()` and call `loadIntoSession($user)` after the existing branch session assignment and before the existing role-based redirect. Do not alter `$cabangs`, `cabang_aktif`, or the Owner/non-Owner redirect condition.

- [ ] **Step 4: Hand off authentication testing to the user**

Do not run PHPUnit, PHP lint, or any other verification command. Tell the user to verify that both roles load the correct session keys and retain their existing redirects.

- [ ] **Step 5: Commit login integration**

```bash
git add app/Http/Controllers/Auth/LoginController.php tests/Feature/AuthMenuSessionTest.php
git commit -m "feat: load menu access at login"
```

---

### Task 6: Navbar Menu-Key Filtering

**Files:**
- Modify: `resources/views/layouts/navbar.blade.php`
- Test: `tests/Feature/NavbarMenuAccessTest.php`

**Interfaces:**
- Every sidebar link/group uses the single `@menuAccess(...)` directive.
- A parent group is rendered only when at least one child key is allowed.
- Existing `request()->routeIs(...)` open-state logic remains intact, with `role-hak-akses.*` added to the Master grouping if needed.

- [ ] **Step 1: Write failing navbar rendering tests**

Render the navbar as an authenticated user with explicit session keys and assert the expected labels:

```php
public function test_missing_non_owner_menu_keys_hide_their_links_and_empty_groups()
{
    $user = \App\Models\User::factory()->create(['role' => 'Kasir']);

    $view = $this->actingAs($user)
        ->withSession(['allowed_menu_keys' => ['transaksi']])
        ->view('layouts.navbar');

    $view->assertSee('Transaksi');
    $view->assertDontSee('Jual Valas');
    $view->assertDontSee('Jadwal &amp; Absen');
    $view->assertDontSee('Laporan Absensi');
}

public function test_owner_sees_all_sidebar_labels_without_pivot_rows()
{
    $owner = \App\Models\User::factory()->create(['role' => 'Owner']);

    $view = $this->actingAs($owner)->view('layouts.navbar');

    $view->assertSee('Role & Hak Akses')
        ->assertSee('Rekapitulasi Cabang (Excel)')
        ->assertSee('Approval Modal');
}
```

Use the project’s actual view-rendering assertion API if the installed Laravel test version does not provide `view()` directly; the test must still render `layouts.navbar` with the session value rather than only inspecting source text.

- [ ] **Step 2: Keep the failing navbar test for user execution**

Do not run the test. Leave it as the user-run check that restricted labels are hidden only after the menu-key implementation is complete.

- [ ] **Step 3: Apply stable menu keys to the current navbar**

Wrap the Dashboard link with `@menuAccess('dashboard')`; wrap Web Exchange with `@menuAccess('web-exchange')`. Add `role-hak-akses` immediately below Pegawai in Master Data.

Use these exact link-to-key mappings:

```text
master-cabang -> Cabang
master-pegawai -> Pegawai
role-hak-akses -> Role & Hak Akses
master-currency -> Currency
master-customer -> Customer
master-terduga -> Terduga
master-threshold -> Batas Atas Transaksi
shift -> Shift
jadwal -> Jadwal
jadwal-user -> Jadwal & Absen
modal -> Modal
transaksi -> Transaksi
transaksi-jual -> Jual Valas
rekapan-hari-ini -> Rekapan Hari Ini
seluruh-transaksi -> Seluruh Transaksi
jurnal-bulanan -> Jurnal Bulanan
jurnal-debit-kredit -> Jurnal Debit Kredit
laporan-harian -> Laporan Harian
laporan-pegawai -> Laporan Pegawai
laporan-saya -> Laporan Saya
laporan-rekap-cabang -> Rekapitulasi Cabang (Excel)
approval-modal -> Approval Modal
log-transaksi -> Log Transaksi
```

Replace the current unconditional parent groups with parent conditions using the child keys, for example:

```blade
@menuAccess(['master-cabang', 'master-pegawai', 'role-hak-akses', 'master-currency', 'master-customer', 'master-terduga', 'master-threshold'])
    <li class="nav-item">...existing Master Data markup with each child wrapped...</li>
@endmenuAccess
```

Repeat for Jadwal, Transaction, Pelaporan, nested Laporan Absensi, and Log/Approval. Remove `$isOwner` as the menu visibility decision so configured keys control sidebar display; do not remove the existing `Owner` middleware from routes. This intentionally preserves the agreed scope: a configured link can still be rejected by an existing route middleware, because this feature does not change direct URL authorization.

Keep the existing `$open` route patterns and add `role-hak-akses.*` to the Master active/open calculation if needed. Keep `Auth::user()->role` display at the bottom.

- [ ] **Step 4: Hand off navbar testing to the user**

Do not run PHPUnit, `git diff --check`, PHP lint, or any other verification command. Tell the user to verify restricted child links and empty parent groups are absent, Owner sees all labels without pivot rows, and active/open classes still work on existing routes.

- [ ] **Step 5: Commit navbar filtering**

```bash
git add resources/views/layouts/navbar.blade.php tests/Feature/NavbarMenuAccessTest.php
git commit -m "feat: filter sidebar by role menu access"
```

---

### Task 7: User-Owned Verification and Handoff

**Files:**
- Modify: none.
- Test: all role/menu tests plus the manual acceptance matrix below.

**Interfaces:**
- No new interface; this task documents checks the user will run after implementation.

- [ ] **Step 1: Run all focused tests yourself**

Do not run PHPUnit, Artisan, PHP lint, `git diff --check`, or any other verification command. The user will run the focused tests and inspect the output.

The user may run:

```bash
vendor/bin/phpunit tests/Feature/AccessMenuMigrationTest.php tests/Unit/MenuAccessServiceTest.php tests/Feature/RoleHakAksesTest.php tests/Feature/MasterPegawaiRoleTest.php tests/Feature/AuthMenuSessionTest.php tests/Feature/NavbarMenuAccessTest.php -v
```

- [ ] **Step 2: Run the existing suite yourself**

Do not run the existing suite. The user may run:

```bash
vendor/bin/phpunit -v
```

- [ ] **Step 3: Run syntax and route checks yourself**

Do not run PHP lint, Artisan, or any other verification command. The user may run the Bash-only checks listed below and report any failures:

```bash
php -l app/Services/MenuAccessService.php
php -l app/Http/Controllers/RoleHakAksesController.php
php -l app/Http/Controllers/Auth/LoginController.php
php -l app/Http/Controllers/MasterPegawaiController.php
php -l database/migrations/2026_10_01_000002_create_access_menu_tables.php
php artisan route:list --name=role-hak-akses
git diff --check
```

- [ ] **Step 4: Perform the manual acceptance matrix**

The user should verify the following using the running Laravel app and a database containing at least one user for each role:

1. Owner logs in and sees every sidebar group/link even when pivot rows are deleted.
2. Pegawai, Admin Cabang, and Kasir log in and see only their seeded/checked menu keys.
3. Removing one checkbox and saving removes the old pivot row and hides the link after the affected user logs in again.
4. A non-Owner with `allowed_menu_keys` manually removed from the session sees no restricted menu links.
5. Branch selector/session behavior remains unchanged: Owner has all branches with `cabang_aktif = null`; each non-Owner starts on the first assigned active branch.
6. Existing login redirects remain `/` for Owner and `/transaksi/create` for all non-Owner roles.
7. Existing Owner middleware still admits only Owner; no direct route authorization is claimed by this feature.

- [ ] **Step 5: Review scope before handoff**

Confirm the diff contains only the catalog, session helper, role management page/routes, employee role validation/forms, navbar filtering, and tests. Do not add action-level checks, route guards, or unrelated business logic.

- [ ] **Step 6: Commit any final test-only correction**

If verification required a correction owned by this feature, commit it separately:

```bash
git add <only-the-corrected-files>
git commit -m "test: verify role menu access behavior"
```

Do not commit generated framework cache files, database dumps, or dependency/vendor changes.

---

## Self-Review

- **Spec coverage:** The migration/catalog is Task 1; session loading and fail-closed behavior are Tasks 2 and 5; Owner management and transactional replacement are Task 3; four-role assignment and self-refresh are Task 4; navbar filtering and parent visibility are Task 6; verification and explicit route-security limitation are Task 7.
- **Placeholder scan:** No implementation step uses TBD/TODO or an unspecified “appropriate” behavior; each task gives the target file, interface, test shape, and command.
- **Type consistency:** `allowed_menu_keys` is always an array of menu-key strings; `MenuAccessService::allows()` accepts one string or an array; role management submits `menu_keys[]`; the migration and service both use the same `config/access_menus.php` keys.
- **Review focus:** Each of the five listed failure modes has a named test/check in its owning task.
- **Known compatibility boundary:** Existing Owner middleware remains unchanged. Sidebar visibility is configurable, but direct URL authorization remains outside this feature exactly as stated in the spec.
