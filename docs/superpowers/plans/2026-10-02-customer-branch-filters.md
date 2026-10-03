# Customer Country and Branch Filters Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make master-customer country data consistent with `countries.json`, remove obsolete customer fields from the UI/validation, and add branch columns and `cabang_id` filters to the transaction and journal pages without weakening branch isolation.

**Architecture:** Keep the existing Laravel controllers and Blade views. Each controller loads active branches for its view and applies an optional validated `cabang_id` predicate to the existing Eloquent query; existing `BelongsToCabang` global scopes remain active. Use the existing `Cabang` relationship for row labels, and carry filter values with query strings/forms rather than adding routes or dependencies.

**Tech Stack:** Laravel 8, PHP 7.3+/8.0, Eloquent, Blade, Bootstrap 5, Choices.js, DataTables, existing Excel/PDF exporters.

**Spec:** `docs/superpowers/specs/2026-10-02-customer-branch-filters-design.md`

## Global Constraints

- Do not execute terminal commands, Composer, npm, Artisan, or Git commands; use file tools and IDE diagnostics only.
- Do not add frontend dependencies or a new asset pipeline.
- Do not delete legacy customer database columns or create a migration.
- Preserve `BelongsToCabang` global scopes and the existing `session('cabang_aktif')` visibility policy.
- Keep existing transaction, screening, date, currency, employee, role, and export behavior except for carrying the requested branch filter.
- Use server-side `base_path('countries.json')`; do not fetch the repository-root file from the browser.

## Review Focus

- Empty or invalid `cabang_id` must not broaden visibility; validate it and leave the existing global scope active. Test by inspection in each controller query task.
- Owner with no branch filter must see all visible branches while a selected branch narrows results. Pin query construction and view data in each page task.
- Non-owner branch/session restrictions must survive the new filter. Confirm no `withoutGlobalScope` or replacement of the existing query occurs.
- Missing branch relations must render safely instead of throwing in Blade. Add `optional($item->Cabang)->cabang_name ?: '-'` to each row and inspect empty-state `colspan` values.
- Country file failure/empty content must produce an empty option set without browser AJAX. Inspect defensive decoding in the customer controller and server-rendered select.

---

### Task 1: Simplify the Master Customer Country and Form Contract

**Files:**
- Modify: `app/Http/Controllers/MasterCustomerController.php:16-50`
- Modify: `app/Models/MasterCustomer.php:14-29`
- Modify: `resources/views/pages/mastercustomer/index.blade.php:15-135`

**Interfaces:**
- Consumes: existing `MasterCustomerController::rules()`, `index()`, `store()`, `update()`, `show()`, and the Blade variables `$customer`, `$cabang`, `$countries`.
- Produces: `$countries` as an alphabetically sorted ISO-code-to-country-name array from `countries.json`; customer requests that contain only active form fields; a table/modal with no Pekerjaan, Alamat, or Tanggal Terdaftar controls.

- [ ] **Step 1: Replace locale-derived country loading with the repository JSON.**

  In `MasterCustomerController::index()`, remove the `ResourceBundle`/`Locale` construction and use a guarded read so an unavailable file produces an empty option set without a browser request:

  ```php
  $countries = [];
  $countriesPath = base_path('countries.json');
  if (is_file($countriesPath)) {
      $countries = json_decode(file_get_contents($countriesPath), true) ?: [];
  }
  asort($countries);
  ```

  Remove the now-unused `ResourceBundle` and `Locale` imports. Keep `$customer`, `$cabang`, and the existing view name unchanged.

- [ ] **Step 2: Remove obsolete fields from request validation.**

  In `rules()`, retain `name`, `country`, `passport`, `nik`, `is_terduga`, `kode_densus`, `alias`, `is_active`, and `cabang_terdaftar`. Delete only these rules:

  ```php
  'pekerjaan' => 'nullable|string|max:100',
  'tanggal_terdaftar' => 'nullable|date',
  'alamat' => 'nullable|string|max:150',
  ```

  Do not change the existing defaulting of `is_active`, `created_by`, `updated_by`, or `cabang_terdaftar`.

- [ ] **Step 3: Remove obsolete mass-assignment fields while preserving legacy columns.**

  In `MasterCustomer::$fillable`, remove `pekerjaan`, `tanggal_terdaftar`, and `alamat`. Do not add a migration and do not alter the database table.

- [ ] **Step 4: Remove obsolete controls and table cells from the Blade view.**

  In `resources/views/pages/mastercustomer/index.blade.php`:

  - Remove the Pekerjaan `<th>` and `<td>`.
  - Change the empty-state `colspan` from `7` to `6`.
  - Remove the Pekerjaan, Tanggal Terdaftar, and Alamat form controls.
  - Keep Country as the existing Choices.js-backed `<select>` whose options come from `$countries`.
  - Keep Passport, NIK, and Cabang Terdaftar controls unchanged.

- [ ] **Step 5: Remove obsolete edit-field assignments.**

  Delete the three JavaScript assignments in the `.editCustomer` callback:

  ```javascript
  $('#customerPekerjaan').val(item.pekerjaan);
  $('#customerTanggal').val(item.tanggal_terdaftar);
  $('#customerAlamat').val(item.alamat);
  ```

  Keep the country choice assignment and all remaining customer fields intact.

- [ ] **Step 6: Inspect the resulting customer contract.**

  Verify by file inspection that no form input named `pekerjaan`, `alamat`, or `tanggal_terdaftar` remains in this view, no rules or fillable entries remain for those fields, and `$countries` is passed to the view from `countries.json`.

---

### Task 2: Add Branch Filtering to the Daily Transaction Page

**Files:**
- Modify: `app/Http/Controllers/TransaksiController.php:35-101,128-190`
- Modify: `resources/views/pages/transaksi/index.blade.php:70-410`
- Modify: `resources/views/pages/transaksi/owner.blade.php:70-412`

**Interfaces:**
- Consumes: existing `TransaksiController::index()`, `Export_dokumen()`, `Export_dokumen_jual()`, the `Transaksi::Cabang()` relationship, and existing `$currency`, `$pegawai`, `$transaksi`, `$report`, `$valas` view data.
- Produces: validated optional `cabang_id` filtering on daily transaction queries and exports, `$cabang` active-branch options in both transaction views, and a Cabang table column.

- [ ] **Step 1: Add controller dependencies and active branch data.**

  Import `App\Models\MasterCabang`. At the start of `index(Request $request)`, validate only the optional filter without changing other request behavior:

  ```php
  $request->validate([
      'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
  ]);
  ```

  Load active branch options with:

  ```php
  $cabang = MasterCabang::where('is_active', 1)
      ->orderBy('cabang_name')
      ->get(['cabang_id', 'cabang_name']);
  ```

- [ ] **Step 2: Apply the branch predicate to the displayed transaction query.**

  After the existing owner/employee condition and before pagination, add:

  ```php
  if ($request->filled('cabang_id')) {
      $transaksiQuery->where('tb_transaksi.cabang_id', $request->cabang_id);
  }
  ```

  Keep the existing Eloquent global scope intact. Eager-load `Cabang` together with the existing employee relation:

  ```php
  $transaksiQuery = Transaksi::with(['Pegawai', 'Cabang'])
  ```

  Paginate with `->withQueryString()` so `cabang_id`, `per_page`, and existing report parameters survive links. Pass `$cabang` in both owner and non-owner view `compact(...)` calls.

- [ ] **Step 3: Apply the branch predicate to daily buy/sell exports.**

  In both owner and non-owner branches of `Export_dokumen()` and `Export_dokumen_jual()`, add the same validated optional filter before `get()`:

  ```php
  if ($request->filled('cabang_id')) {
      $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
  }
  ```

  Use `tb_transaksi.cabang_id` because these exporters join detail and currency tables. Do not change existing employee/currency/date/type predicates.

- [ ] **Step 4: Add branch selectors to both transaction views.**

  Add a `name="cabang_id"` select to the existing filter modal in `index.blade.php` and `owner.blade.php`, using `$cabang` and preserving the current request:

  ```blade
  <select class="form-select js-choice" name="cabang_id">
      <option value="">Semua Cabang</option>
      @foreach ($cabang as $item)
          <option value="{{ $item->cabang_id }}" {{ (string) request('cabang_id') === (string) $item->cabang_id ? 'selected' : '' }}>
              {{ $item->cabang_name }}
          </option>
      @endforeach
  </select>
  ```

- [ ] **Step 5: Add safe Cabang cells and preserve filters in links.**

  Add a Cabang header immediately before Actions and a matching cell in each transaction row:

  ```blade
  <td class="text-center cabang fs--1">{{ optional($item->Cabang)->cabang_name ?: '-' }}</td>
  ```

  Change existing pagination calls to `withQueryString()` or the equivalent `appends(request()->query())`, preserving `per_page` and `cabang_id`. Ensure the per-page JavaScript redirect appends the current `cabang_id` instead of replacing it.

- [ ] **Step 6: Inspect column counts and export form inputs.**

  Confirm both transaction tables have matching headers/cells, the empty states do not use stale column counts, and both export forms submit `cabang_id` along with their existing filters.

---

### Task 3: Add Branch Filtering to Daily Buy/Sell Journals

**Files:**
- Modify: `app/Http/Controllers/JurnalHarianController.php:24-96,108-214`
- Modify: `resources/views/pages/jurnal/harian/index.blade.php:25-245`
- Modify: `resources/views/pages/jurnal/harian/jual/index.blade.php:25-237`

**Interfaces:**
- Consumes: existing date-filtered `index()`/`jual()` queries, daily export methods, `Transaksi::Cabang()`, and the existing `$pegawai`, `$currency`, `$transaksi`, `$jumlah`, `$total` variables.
- Produces: `$cabang` active-branch options, branch-aware buy/sell listing and exports, and navigation links that preserve `from`, `to`, and `cabang_id`.

- [ ] **Step 1: Validate and load branches in `index()` and `jual()`.**

  Import `MasterCabang`. At the beginning of each method, validate:

  ```php
  $request->validate([
      'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
  ]);
  ```

  Load active branches in each method with the same ordered query used by the other pages.

- [ ] **Step 2: Apply branch filtering and eager-load the relationship.**

  Change each query to eager-load `Cabang`:

  ```php
  $transaksiQuery = Transaksi::with(['Pegawai', 'Cabang'])
  ```

  After the existing date predicates, add:

  ```php
  if ($request->filled('cabang_id')) {
      $transaksiQuery->where('cabang_id', $request->cabang_id);
  }
  ```

  Use `withQueryString()` on both paginators. Pass `$cabang` in the buy and sell view data. Keep the existing date window, role/global-scope behavior, counts, and totals.

- [ ] **Step 3: Carry branch filtering through daily journal exports.**

  In `Export_dokumen()` and `Export_dokumen_jual()`, validate the optional `cabang_id` and add:

  ```php
  if ($request->filled('cabang_id')) {
      $transaksi->where('tb_transaksi.cabang_id', $request->cabang_id);
  }
  ```

  Add the branch selector to both export modals, preserving `request('cabang_id')`. Do not alter the pre-existing export date predicates.

- [ ] **Step 4: Add branch filters and columns to the buy/sell views.**

  Add a Cabang select to the date filter row or existing filter modal. The date navigation JavaScript must build the URL with `cabang_id` using `encodeURIComponent`, for example:

  ```javascript
  var cabang = $('#cabang_id').val() || '';
  window.location.href = '/owner/jurnal-harian?from=' + encodeURIComponent(tanggal_mulai)
      + '&to=' + encodeURIComponent(tanggal_selesai)
      + '&cabang_id=' + encodeURIComponent(cabang);
  ```

  Add a Cabang header and safe relationship cell to both tables:

  ```blade
  <td class="text-center cabang fs--1">{{ optional($item->Cabang)->cabang_name ?: '-' }}</td>
  ```

- [ ] **Step 5: Preserve branch when switching buy/sell pages.**

  Update the two tab links to append the current `from`, `to`, and `cabang_id` values, either with Blade query parameters or a small URL helper in the view. Keep the existing route names unchanged.

- [ ] **Step 6: Inspect daily journal table and empty-state structure.**

  Confirm every added header has one cell in every row branch, including the empty state, and both paginator links preserve all three filters.

---

### Task 4: Add Branch-Aware Monthly Aggregation and Detail

**Files:**
- Modify: `app/Http/Controllers/JurnalBulananController.php:44-160`
- Modify: `resources/views/pages/jurnal/bulan/index.blade.php:18-86`
- Modify: `resources/views/pages/jurnal/bulan/detail.blade.php:22-129`
- Modify: `resources/views/pages/jurnal/bulan/detailtanggal.blade.php:18-69`
- Modify: `resources/views/pages/jurnal/bulan/detailtransaksi.blade.php` if its transaction-row table is rendered from the existing `DetailTransaksi()` action

**Interfaces:**
- Consumes: existing month/year aggregation, `show(Request $request, $month)`, custom detail links, and `Transaksi::Cabang()`.
- Produces: `$cabang`, branch-filtered aggregation/detail queries, branch labels in monthly tables, and detail links carrying `cabang_id`.

- [ ] **Step 1: Validate the monthly branch filter and load active branches.**

  Change `index()` to accept `Request $request`, validate the optional branch ID, and load active branches. Pass `$cabang` and the selected filter to the view.

- [ ] **Step 2: Group the monthly aggregation by branch without losing the existing year grid.**

  Add a left join to `tb_master_cabang` and select/group `tb_transaksi.cabang_id` and `tb_master_cabang.cabang_name` alongside month/year. Apply the optional branch predicate before `groupBy`:

  ```php
  if ($request->filled('cabang_id')) {
      $query->where('tb_transaksi.cabang_id', $request->cabang_id);
  }
  ```

  Build `$data` as rows keyed by branch and month, with `cabang_name`, `month_name`, and the existing `totals[$year]` values. When no branch is selected, emit one row per visible branch/month; when a branch is selected, emit that branch's rows. Keep `$years` derived from the aggregate result and keep the existing totals meaning.

- [ ] **Step 3: Add the monthly branch selector and column.**

  In `resources/views/pages/jurnal/bulan/index.blade.php`, add a GET filter form with `name="cabang_id"` and active branch options. Add a `Cabang` header before `Bulan` and render `$month['cabang_name'] ?: '-'`. Make the detail link include the branch filter:

  ```blade
  <a href="{{ route('jurnal-bulanan.edit', ['jurnal_bulanan' => $month['month'], 'cabang_id' => request('cabang_id')]) }}">
  ```

  Use the repository's actual generated route parameter name if Laravel's resource route requires it; do not add a new route.

- [ ] **Step 4: Apply branch/date filters to monthly detail.**

  In `show(Request $request, $month)`, validate `cabang_id`, apply it to both the grouped daily query and `$transaksi_seluruh`, and eager-load `Cabang` for detail rows. Preserve existing `from` and `to` filters. Apply the same branch predicate to `edit($tanggal_transaksi)` and `DetailTransaksi($id)` only where their existing query path receives the filter or needs to enforce the selected branch.

- [ ] **Step 5: Add branch cells and filter navigation to detail views.**

  Add a Cabang column and safe relationship cell to `detail.blade.php` and any active `detailtanggal.blade.php` table. Add a branch filter control or preserve the selected query parameter in detail links and back/navigation links. Keep existing customer, passport, country, totals, and action links unchanged.

- [ ] **Step 6: Inspect monthly row shape and empty data behavior.**

  Verify that every `$data` row contains `cabang_name`, `month_name`, and all year keys, and that the view does not access an undefined branch field when there are no aggregates or a legacy transaction has no matching branch.

---

### Task 5: Add Branch Filtering to the Debit/Credit Journal

**Files:**
- Modify: `app/Http/Controllers/JurnalKreditDebitController.php:21-122`
- Modify: `resources/views/pages/jurnal/kredit&debit/index.blade.php:37-223`
- Modify: `resources/views/pages/jurnal/kredit&debit/pdf.blade.php` only if the export output needs an explicit branch column/name
- Modify: `app/Exports/ExcelDebitKredit.php` only if the export output needs an explicit branch column/name

**Interfaces:**
- Consumes: existing role/user restriction, paginated `$jurnal`, currency options, export query builders, and `Jurnal::Cabang()`.
- Produces: validated optional `cabang_id` filtering for the page and export, `$cabang` view data, and safe branch labels in the journal table/export.

- [ ] **Step 1: Validate and load branches in `index()`.**

  Import `MasterCabang`, validate nullable integer `cabang_id` against `tb_master_cabang.cabang_id`, and load active branches. Keep the existing Owner/non-Owner query selection exactly as the first visibility restriction.

- [ ] **Step 2: Apply the branch predicate and eager-load Cabang.**

  Build the query as:

  ```php
  $jurnalQuery = Auth::user()->role === 'Owner'
      ? Jurnal::with('Cabang')->orderBy('updated_at', 'DESC')->take(200)
      : Jurnal::with('Cabang')->where('id_pegawai', Auth::user()->id)->orderBy('updated_at', 'DESC')->take(300);

  if ($request->filled('cabang_id')) {
      $jurnalQuery->where('cabang_id', $request->cabang_id);
  }
  ```

  Paginate with `withQueryString()` and pass `$cabang` to the view. Do not remove the Jurnal global scope.

- [ ] **Step 3: Add the branch selector to the page and export modal.**

  Add a GET-compatible branch selector to the page filter area and a `cabang_id` selector to the existing export modal, using the active options and preserving `request('cabang_id')`. Keep date and `filter_jenis` fields unchanged.

- [ ] **Step 4: Apply the branch predicate to all export queries.**

  In `create(Request $request)`, add the validated optional predicate to `$jurnal`, `$totalDebit`, `$totalKredit`, `$totalModal`, and the currency aggregation query:

  ```php
  if ($request->filled('cabang_id')) {
      $jurnal->where('cabang_id', $request->cabang_id);
      $totalDebit->where('cabang_id', $request->cabang_id);
      $totalKredit->where('cabang_id', $request->cabang_id);
      $totalModal->where('cabang_id', $request->cabang_id);
      $currency->where('tb_jurnal.cabang_id', $request->cabang_id);
  }
  ```

  Preserve existing role/user restrictions and the existing unrelated date predicates, including the known comparison behavior outside this feature.

- [ ] **Step 5: Add a safe Cabang column to the journal table/export.**

  Add a table header and row cell after the date or code column:

  ```blade
  <td class="text-center cabang fs--1">{{ optional($item->Cabang)->cabang_name ?: '-' }}</td>
  ```

  If PDF/Excel templates expose tabular journal rows, add the same branch name there and update header/summary colspans. Do not change debit/credit calculations.

- [ ] **Step 6: Inspect pagination and polymorphic journal rows.**

  Confirm branch rendering works for Debit, Kredit Jual, and Modal rows, pagination preserves `cabang_id`, and all rows have matching cells after the new column.

---

### Task 6: Cross-File Verification and Handoff

**Files:**
- Inspect: all files modified in Tasks 1–5
- Optional Test: an existing focused test file only if a database-independent assertion can be added without changing the configured test infrastructure

**Interfaces:**
- Consumes: completed controller/view edits from Tasks 1–5.
- Produces: an inspection record of requirement coverage and any diagnostics output; no runtime completion claim unless fresh diagnostics support it.

- [ ] **Step 1: Inspect all changed controllers for filter consistency.**

  Check every `cabang_id` path for nullable integer/existing validation, `filled()` filtering, qualified columns on joined transaction queries, preserved global scopes, and branch options passed to the corresponding view.

- [ ] **Step 2: Inspect all changed Blade views for structural consistency.**

  Match every new Cabang header with a cell in every row branch, update empty-state `colspan` values, verify `request('cabang_id')` selection, and verify pagination/tab/detail links preserve query parameters.

- [ ] **Step 3: Run IDE diagnostics on changed PHP files.**

  Use the IDE diagnostics tool for each changed PHP file. Record any diagnostics; do not claim PHP/Laravel runtime validity from static inspection alone.

- [ ] **Step 4: Perform a requirement checklist against the approved spec.**

  Confirm:

  - Master customer country options come from `countries.json`.
  - Pekerjaan, Alamat, and Tanggal Terdaftar are absent from customer UI/rules/fillable data.
  - Transaction, daily journal, monthly journal, and debit/credit pages have branch columns and filters.
  - Existing branch global scope, role restrictions, date filters, pagination, and exports remain intact.
  - No schema, route, dependency, or unrelated export-date changes were introduced.

- [ ] **Step 5: Report verification limits accurately.**

  Since project instructions prohibit terminal commands and the configured database test connection is not set up for SQLite in-memory testing, report that runtime tests were not executed unless IDE diagnostics or an explicitly available non-terminal test facility provides fresh evidence.
