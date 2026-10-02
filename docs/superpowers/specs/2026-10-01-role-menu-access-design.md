# Role Menu Access Design

## Goal

Add configurable sidebar-menu visibility for four user roles without changing existing business routes or controller authorization flows:

- `Owner`
- `Pegawai`
- `Admin Cabang`
- `Kasir`

The Owner always sees every menu and manages role menu checklists. The other roles see only menus enabled for their role. Existing branch assignments and the current login redirect remain intact.

## Scope and non-goals

### In scope

- Store the four role values on `users.role`.
- Store the menu catalog and role-to-menu assignments in database tables.
- Add an Owner-only Role & Hak Akses page below the Pegawai menu.
- Assign a configured role to each user through the existing employee create/edit screens.
- Load allowed menu keys into the authenticated session at login and after role changes.
- Provide one Blade helper for sidebar visibility.
- Update the existing navbar to hide disallowed menu groups/items.

### Out of scope

- Route/controller guards for direct URL access.
- Per-action permissions such as create, update, delete, or export.
- Changes to transaction, journal, branch, attendance, or export business logic.
- A new authorization package.

This deliberate boundary keeps the change small, but sidebar-only access is not a security boundary: a user who knows a hidden URL may still request it.

## Data model

### `access_menus`

A catalog of sidebar menu keys used by the application.

- `id`
- `menu_key` — unique stable key, for example `dashboard`, `transaksi`, `modal`, `jurnal-debit-kredit`, `laporan-absensi`, `laporan-rekap-cabang`, `master-pegawai`, `role-hak-akses`
- `menu_label`
- `menu_group` — optional grouping label for administration
- timestamps

Only menus that exist in the sidebar are cataloged. Nested attendance/report items can have their own keys where independently visible; parent groups are considered visible when at least one enabled child exists.

### `role_menus`

A role-to-menu pivot.

- `id`
- `role` — indexed string containing one of the four values
- `menu_id` — foreign key to `access_menus.id`
- unique pair `(role, menu_id)`
- timestamps

Owner does not need rows in this pivot because Owner bypasses the configured list and sees all menus.

### `users`

Add nullable `access_role` only if the system needs a user-specific configured role separate from the legacy role. For this agreed design, the four values live directly in `users.role`, so no extra user role column is needed. Existing values are migrated/validated to the four-role set, with current `Pegawai` users preserved as `Pegawai`.

The existing role is currently used by middleware and branch/session logic. Any role comparison that means “not Owner” continues to work for all three non-Owner roles; role-specific behavior is not introduced in this change.

## Role configuration page

Add Owner-only routes under the existing authenticated/Owner group:

- `GET /owner/role-hak-akses` — list the four roles and menu checklists.
- `PUT /owner/role-hak-akses/{role}` — replace the selected role's menu assignments.

The page is reachable from a new **Role & Hak Akses** item directly below the existing Pegawai item. The form displays the menu catalog as checkboxes grouped by menu group. Saving uses a transaction and `sync()`-style replacement so removed checks are actually removed.

Owner is not rendered as an editable permission checklist because Owner always has all menus. The UI can show Owner as “Semua Menu”.

## Employee role assignment

The existing Master Pegawai create/edit forms change their role select options to the four values. Existing controller validation is extended to require one of those values. No role-specific branch behavior is added.

For compatibility, the existing Owner-only Master Pegawai routes remain unchanged. If an Owner changes their own role, the current session permissions are refreshed immediately after save; the next login always reloads them.

## Login and session flow

1. `LoginController::authenticated()` loads the user's active assigned branches as it does today.
2. If the user's role is `Owner`, set a session marker indicating all menu keys are allowed.
3. Otherwise query the role-menu pivot and store the resulting menu keys in session as `allowed_menu_keys`.
4. Preserve the existing `cabang_aktif` behavior: Owner starts with all branches, other roles start with their first assigned branch.
5. On logout, Laravel clears the session as usual.

A small reusable service/helper resolves access from the current authenticated user instead of duplicating queries in every Blade file. The helper returns `true` for Owner and checks the session key for other roles. If the permission session is missing, it fails closed for non-Owner users and can be repopulated by the login flow.

## Navbar behavior

The helper is applied only to existing sidebar links/groups:

- Dashboard
- Master Data items
- Jadwal items
- Transaction items
- Reporting items, including the existing Laporan Absensi subtree and the new branch recap menu
- Log and Approval items
- Role & Hak Akses

A parent group is rendered if it has at least one visible child. The current active/open state logic remains, with the role-access route added to the Master/Pegawai grouping as needed. No route names or business endpoint behavior changes.

## Error handling and compatibility

- Unknown role values are rejected on create/update and left unchanged by the migration unless an existing database value is outside the known set; such values should be reported before migration rather than silently reassigned.
- Missing menu rows result in no access for non-Owner users, not all-access.
- Owner always bypasses menu configuration so an incomplete role-menu seed cannot lock out administration.
- Updating role menus does not require logging users out. Existing sessions can be refreshed on the next request through a lightweight session-version or simply on the next login; the minimum implementation refreshes the current Owner session and documents that other users need to log in again.
- Existing routes and controllers are untouched except the employee role validation, role-session loading, and new role-management endpoints.

## Verification

- Migration creates catalog/pivot tables and seeds the expected menu keys.
- Four role values render in employee create/edit forms.
- Owner sees all menu entries regardless of pivot rows.
- Pegawai, Admin Cabang, and Kasir see only their checked menu entries after login.
- A non-Owner with missing permission session sees no restricted menus rather than all menus.
- Saving a role replaces its checks and persists removals.
- Existing branch selection/session behavior still works for all non-Owner roles.
- Existing role middleware behavior remains compatible: only Owner passes the existing Owner middleware; all non-Owner roles remain subject to the existing schedule/branch behavior.
- Direct route access is explicitly not covered by this feature and remains unchanged.
