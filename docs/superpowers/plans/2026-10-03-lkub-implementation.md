# LKUB Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a monthly LKUB report with branch checkbox filters and Excel/PDF downloads using cumulative journal data.

**Architecture:** Add a focused `LkubController` that validates authorized branches and builds one normalized report array per branch. Reuse Laravel Excel `FromView`/multi-sheet exports and Dompdf view rendering; keep formulas in the controller's report builder so both output formats consume identical rows. Register the menu key through the existing access-menu data path and expose the page through authenticated report routes.

**Tech Stack:** Laravel 8, Blade, Eloquent query builder, Maatwebsite Excel 3.1, barryvdh/laravel-dompdf, PHPUnit 9.

**Spec:** `docs/superpowers/specs/2026-10-03-lkub-design.md`

## Global Constraints

- Do not execute terminal commands; use repository file tools only.
- Do not add a frontend build pipeline or a new dependency.
- Use only active, authorized branches in branch filters.
- `BG. BALANCE` and `BG. BALANCE (Rp.)` use all journal rows before the first day of the selected month.
- BUY is `Debit`; SELL is `Kredit Jual`.
- `TYPE` is always `UKA`.
- Excel has one worksheet per branch; PDF has one section per branch.
- Preserve unrelated working-tree changes and do not overwrite them.

## Review Focus

- A non-owner submitting an unauthorized `cabang_id` must receive validation failure rather than data from another branch; test in `LkubControllerTest`.
- A month with no current-period rows but with prior balance must still produce a report row; test the report builder.
- A zero ending quantity must produce `MIDDLE RATE = 0`, not a division error; test the report builder.
- Selecting all branches must expand only to the user's allowed active branches; test branch selection validation.
- A branch/currency with no activity must not create misleading zero-only rows unless its current or opening balance exists; test report row selection.

---

### Task 1: Add the LKUB report data builder and formulas

**Files:**
- Create: `app/Http/Controllers/LkubController.php`
- Create: `tests/Unit/LkubReportTest.php`

Note: The rendered LKUB table has 12 columns including `NO`; the normalized calculation row has 11 named fields and receives `no` after sorting.

**Interfaces:**
- Produces `LkubController::buildReportRows(int $cabangId, int $month, int $year): \Illuminate\Support\Collection` with keys `no`, `forex`, `type`, `bg_balance`, `bg_balance_rp`, `buy`, `buy_rp`, `sell`, `sell_rp`, `balance`, `middle_rate`, and `balance_rp`.
- Produces `LkubController::allowedCabangIds(): array` and `allowedCabangs()` for page/download filtering.

- [ ] **Step 1: Write a focused formula self-check**

Create a small PHPUnit unit test for a public/static pure calculation helper (or the controller's report-row calculation after extracting a minimal helper) covering:

```php
public function test_lkub_calculates_opening_balance_and_middle_rate(): void
{
    $row = LkubController::calculateRow('USD', [
        'opening_quantity' => 100,
        'opening_rupiah' => 1500000,
        'buy_quantity' => 25,
        'buy_rupiah' => 375000,
        'sell_quantity' => 10,
        'sell_rupiah' => 160000,
    ]);

    $this->assertSame(115.0, $row['balance']);
    $this->assertSame(1715000.0, $row['balance_rp']);
    $this->assertSame(14913.04347826087, $row['middle_rate']);
}

public function test_lkub_uses_zero_middle_rate_when_balance_is_zero(): void
{
    $row = LkubController::calculateRow('JPY', [
        'opening_quantity' => 0,
        'opening_rupiah' => 0,
        'buy_quantity' => 10,
        'buy_rupiah' => 100000,
        'sell_quantity' => 10,
        'sell_rupiah' => 100000,
    ]);

    $this->assertSame(0.0, $row['balance']);
    $this->assertSame(0.0, $row['middle_rate']);
}
```

- [ ] **Step 2: Implement the smallest calculation helper and query builder**

Use `Jurnal::withoutGlobalScope('cabang')` and `tb_jurnal.cabang_id` explicitly. Join `tb_currency` for names. For each currency appearing in either the opening or selected-period aggregate, calculate:

```php
$balance = $openingQuantity + $buyQuantity - $sellQuantity;
$balanceRp = $openingRupiah + $buyRupiah - $sellRupiah;
$middleRate = $balance == 0.0 ? 0.0 : $balanceRp / $balance;
```

Aggregate opening rows with `tanggal_jurnal < first day`; aggregate current rows with `whereBetween` on the selected month. Preserve negative quantities and amounts. Return rows ordered by currency name and assign `no` after filtering currencies that have no opening/current activity.

- [ ] **Step 3: Run the focused formula test**

Run: `vendor/bin/phpunit tests/Unit/LkubReportTest.php`

Expected: PASS after the helper and formula are implemented. If the repository test database is unavailable, keep the test pure and do not require database fixtures for this formula check.

---

### Task 2: Add controller endpoints, branch authorization, and routes

**Files:**
- Modify: `app/Http/Controllers/LkubController.php`
- Modify: `routes/web.php:111-114`
- Create: `tests/Feature/LkubControllerTest.php`

**Interfaces:**
- `GET /laporan-lkub` named `laporan-lkub.index` renders `pages.laporan.lkub.index` with `cabangs`, `months`, and `years`.
- `GET /laporan-lkub/download` named `laporan-lkub.download` accepts `month`, `year`, `format`, `semua_cabang`, and `cabang_ids[]` and returns Excel/PDF.

- [ ] **Step 1: Add validation/authorization tests**

Cover these request shapes:

```php
$this->get(route('laporan-lkub.download', [
    'month' => 9,
    'year' => 2026,
    'format' => 'excel',
    'cabang_ids' => [$unauthorizedBranchId],
]))->assertSessionHasErrors('cabang_ids.0');

$this->get(route('laporan-lkub.download', [
    'month' => 9,
    'year' => 2026,
    'format' => 'excel',
    'semua_cabang' => 1,
]))->assertOk();
```

Use the existing authentication/role conventions and isolate database-dependent assertions if the legacy schema is not available in the test environment.

- [ ] **Step 2: Implement index and download actions**

`index()` loads active allowed branches, month labels, and a bounded year list (current year ± 5 years). `download()` validates `month` in `1..12`, `year` as a four-digit year, `format` in `excel,pdf`, and branch IDs with `Rule::in($allowed)`. Resolve `semua_cabang=1` to all allowed IDs; otherwise use the distinct submitted IDs. Build reports for each selected branch and reject an entirely empty result with a session error redirect.

- [ ] **Step 3: Register routes**

Import `LkubController` and add the two routes next to the existing Rekap Cabang routes, inside the authenticated/jadwal-protected group:

```php
Route::get('/laporan-lkub', [LkubController::class, 'index'])->name('laporan-lkub.index');
Route::get('/laporan-lkub/download', [LkubController::class, 'download'])->name('laporan-lkub.download');
```

- [ ] **Step 4: Run route/controller tests**

Run: `vendor/bin/phpunit tests/Feature/LkubControllerTest.php`

Expected: validation and authorization tests pass; database-dependent tests should be skipped or fixture-backed according to the repository's existing testing setup.

---

### Task 3: Add the LKUB filter page and menu access key

**Files:**
- Modify: `resources/views/layouts/navbar.blade.php:37-39,136-175`
- Create: `resources/views/pages/laporan/lkub/index.blade.php`
- Modify: the repository's existing access-menu migration/seed path discovered before implementation, or create `database/migrations/<timestamp>_add_lkub_access_menu.php` if no seed path exists.

**Interfaces:**
- The page submits `month`, `year`, `format`, `semua_cabang`, and `cabang_ids[]` to `laporan-lkub.download`.
- Navbar renders LKUB after Rekapitulasi Cabang under `@menuAccess('lkub')`.

- [ ] **Step 1: Add the menu key registration**

Insert one `access_menus` row with `menu_key = 'lkub'`, label `LKUB`, and the same report group used by `laporan-rekap-cabang`. Make the migration idempotent with a uniqueness check and provide a down path that removes only the `lkub` row and its role-menu rows.

- [ ] **Step 2: Build the form view**

Render month and year dropdowns, an `Semua Cabang` checkbox, authorized branch checkboxes, and two submit buttons that set `format` to `excel` or `pdf`. Preserve selected values after validation errors. Disable individual branch checkboxes while all-branches is checked, matching the existing Rekap Cabang UI.

- [ ] **Step 3: Add navbar/open-state wiring**

Include `laporan-lkub.*` in the Pelaporan open-state route matcher and render the LKUB item immediately below Rekapitulasi Cabang. Use the existing `@menuAccess` directive and an existing Font Awesome icon.

- [ ] **Step 4: Review the rendered Blade inputs**

Verify names, route names, and checkbox IDs by reading the resulting view and navbar source. Ensure the PDF button does not accidentally submit `excel` due to a hidden fixed format field.

---

### Task 4: Add Excel and PDF output adapters

**Files:**
- Create: `app/Exports/LkubExport.php`
- Create: `app/Exports/LkubSheet.php`
- Create: `resources/views/pages/laporan/lkub/excel.blade.php`
- Create: `resources/views/pages/laporan/lkub/pdf.blade.php`
- Modify: `app/Http/Controllers/LkubController.php`

**Interfaces:**
- `LkubExport::__construct(array $reports, string $periodLabel)` implements `WithMultipleSheets`.
- `LkubSheet::__construct(array $report, string $periodLabel, string $title)` implements `FromView`, `WithTitle`, `ShouldAutoSize`, and sheet styling events.
- Both views consume the same report shape returned by the controller.

- [ ] **Step 1: Add output-shape checks**

Add one unit assertion that every report row exposes exactly the 13 required output fields and that each branch report carries a branch model/name and period label.

- [ ] **Step 2: Implement the Excel multi-sheet adapter**

Create one worksheet per branch, sanitize worksheet names to Excel's 31-character limit, render title/period/branch header and the required columns, and apply borders/bold/centered header styling with PhpSpreadsheet events.

- [ ] **Step 3: Implement the PDF view**

Render one section per branch with the same 13 columns, landscape CSS, compact font, borders, Indonesian period label, and page break between branches. Use Dompdf's existing facade in `download()` and name the file `LKUB <Month> <Year>.pdf`.

- [ ] **Step 4: Wire format selection and filenames**

For `format=excel`, return `Excel::download(new LkubExport(...), 'LKUB <Month> <Year>.xlsx')`. For `format=pdf`, return `Pdf::loadView(...)->setPaper('a4', 'landscape')->download(...)`.

- [ ] **Step 5: Review the output templates**

Read both templates and confirm the labels exactly match `NO`, `FOREX`, `TYPE`, `BG. BALANCE`, `BG. BALANCE (Rp.)`, `BUY`, `BUY (Rp.)`, `SELL`, `SELL (Rp.)`, `BALANCE`, `MIDDLE RATE`, and `BALANCE (Rp.)`.

---

### Task 5: Verify the complete change

**Files:**
- Modify: `docs/superpowers/specs/2026-10-03-lkub-design.md` only if implementation details materially differ from the approved design.

- [ ] **Step 1: Run the focused tests**

Run: `vendor/bin/phpunit tests/Unit/LkubReportTest.php tests/Feature/LkubControllerTest.php`

Expected: PASS, or a clearly reported environment failure caused by missing legacy test database setup.

- [ ] **Step 2: Inspect the final changed-file set**

Review all LKUB files plus route/navbar/menu changes. Confirm no unrelated user modifications were overwritten.

- [ ] **Step 3: Perform a manual static checklist**

Check route names match Blade actions, request parameter names match validation, `semua_cabang` cannot bypass allowed-branch filtering, PDF and Excel consume identical formulas, and zero balances cannot divide by zero.

- [ ] **Step 4: Run the project's available verification command if permitted**

Run: `vendor/bin/phpunit`

Expected: existing tests remain green; if execution is disallowed by the repository's terminal restriction, report that verification was not run and list the exact manual checks completed.
