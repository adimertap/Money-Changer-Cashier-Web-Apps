# Customer Country and Branch Filters

**Date:** 2026-10-02
**Status:** Design approved in conversation; awaiting written-spec review

## Goal

Update the customer master and transaction/journal pages so country data is sourced consistently from `countries.json`, obsolete customer fields are removed from the UI and request validation, and owners can inspect/filter transaction and journal records by branch without bypassing the application's existing branch isolation rules.

## Scope

### Customer master (`/owner/master-customer`)

- Load the country map from the repository-root `countries.json` on the server with `base_path('countries.json')`.
- Continue rendering the country options server-side and keep Choices.js search enabled.
- Remove `pekerjaan`, `alamat`, and `tanggal_terdaftar` from the add/edit form.
- Remove those fields from customer validation rules and controller-managed request data.
- Remove the Pekerjaan column from the customer table and edit-form JavaScript.
- Keep the corresponding legacy database columns intact; no migration or destructive schema change is in scope.
- Keep passport, NIK, alias, active status, and registered branch behavior unchanged unless a touched controller path requires preserving them.

### Branch-aware transaction and journal pages

The following pages receive a branch column and a `cabang_id` filter:

- `/transaksi`
- `/owner/jurnal-harian` (including the buy and sell views)
- `/owner/jurnal-bulanan` and its detail view
- `/jurnal-debit-kredit`

The branch column displays the related branch name through the existing `Cabang` relationship rather than exposing only the numeric ID.

## Branch data and access behavior

- Provide active branches from `MasterCabang` to each filter-capable view, ordered by the existing branch-name field.
- Apply `where('cabang_id', $request->cabang_id)` only when the request contains a valid selected branch.
- Preserve the `BelongsToCabang` global scope on `Transaksi` and `Jurnal`; do not use `withoutGlobalScope` for these pages.
- Owner users with no active branch may see all branches and may narrow the result with the filter.
- Non-owner users remain constrained by `session('cabang_aktif')`; the new filter cannot expand their visibility.
- Pagination links retain the active branch and other existing query parameters using the repository's pagination conventions, preferably `withQueryString()`.

## Page behavior

### Transactions

- Add a branch selector to the existing transaction filter UI.
- Apply the branch filter to the displayed transaction query.
- Add a Cabang table column using `optional($item->Cabang)->cabang_name` with a safe fallback.
- Pass the same branch filter to the existing daily transaction export endpoints where their controller query supports the page filters. Do not alter unrelated export semantics.

### Daily journal

- Add a branch selector alongside the existing date filters.
- Apply it in both `index()` (buy) and `jual()` (sell).
- Add the Cabang column to both tables.
- Preserve branch/date parameters when switching between buy and sell pages and when rendering pagination.
- Pass the branch filter through existing daily export forms/endpoints where supported.

### Monthly journal

- Accept an optional branch filter in the monthly index and detail flows.
- Restrict the monthly aggregation and detail transaction query by branch.
- Keep the existing month/year presentation; add a Cabang column or branch grouping so the selected branch is explicit without changing unrelated totals.
- Preserve the branch parameter in detail links and pagination/filter navigation.

### Debit/credit journal

- Add a branch selector to the page/export filter UI.
- Apply the filter after the existing Owner/non-Owner visibility condition and before pagination.
- Add the Cabang column to the journal table.
- Pass the filter to the existing PDF/Excel export path where supported.
- Do not change existing date comparison behavior unrelated to the requested branch filter.

## Validation and error handling

- Validate `cabang_id` as nullable and an existing branch ID at the request boundary where controller validation is already used.
- Treat an empty branch selection as no additional filter.
- If a selected branch is not visible under the current global-scope/user rules, the query must return no unauthorized records rather than bypassing the scope.
- Use null-safe relationship rendering so records with missing legacy branch references do not break the page.
- Read `countries.json` defensively; an unreadable or invalid file results in an empty country option set rather than a browser-side request to a root file that is not publicly served.

## Files expected to change

- `app/Http/Controllers/MasterCustomerController.php`
- `app/Models/MasterCustomer.php` only if obsolete fields are no longer needed for mass assignment
- `resources/views/pages/mastercustomer/index.blade.php`
- `app/Http/Controllers/TransaksiController.php`
- `resources/views/pages/transaksi/index.blade.php`
- `app/Http/Controllers/JurnalHarianController.php`
- `resources/views/pages/jurnal/harian/index.blade.php`
- `resources/views/pages/jurnal/harian/jual/index.blade.php`
- `app/Http/Controllers/JurnalBulananController.php`
- `resources/views/pages/jurnal/bulan/index.blade.php`
- `resources/views/pages/jurnal/bulan/detail.blade.php` if the detail view is the active route template
- `app/Http/Controllers/JurnalKreditDebitController.php`
- `resources/views/pages/jurnal/kredit&debit/index.blade.php`
- Existing export view/controller files only where required to carry `cabang_id` consistently

No route changes or frontend dependency changes are expected.

## Verification strategy

Because the repository instructions prohibit terminal commands, verification will use file-level inspection and IDE diagnostics where available:

1. Inspect each changed controller for balanced query/filter flow, request validation, and compact variable passing.
2. Inspect each changed Blade view for matching table headers/cells, filter names, query-string preservation, and removed customer fields.
3. Use IDE diagnostics on changed PHP and Blade-adjacent files if available.
4. Leave one small runnable application-level check or focused test only if the existing test structure can verify the branch-filter query without requiring prohibited commands or an unavailable database setup. Otherwise report that runtime tests were not executed.

## Out of scope

- Removing legacy customer columns from the database.
- Refactoring all exports into a shared service.
- Changing branch session switching, middleware, or global-scope policy.
- Fixing unrelated pre-existing export date comparison issues.
- Changing transaction business rules, screening behavior, or customer search behavior.
