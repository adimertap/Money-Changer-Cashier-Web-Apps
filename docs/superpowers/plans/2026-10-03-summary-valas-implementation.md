# Summary Valas Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an authorized Summary Valas report with date, branch, and currency filters plus numeric-IDR Excel and landscape PDF downloads.

**Architecture:** Reuse the existing `Jurnal` ledger as the source of historical balances. `SummaryValasController` aggregates opening and period rows once; Blade views consume the same normalized rows for Excel and PDF. The existing menu-access, route, Laravel Excel, and Dompdf patterns are reused without new dependencies.

**Tech Stack:** Laravel 8, Blade, Eloquent, Maatwebsite Excel 3.1, Dompdf, PHPUnit 9.

**Spec:** `docs/superpowers/specs/2026-10-03-summary-valas-design.md`

## Global Constraints

- Do not execute terminal commands; use repository file tools only.
- Do not add dependencies or a frontend build pipeline.
- Use only active, authorized branches in branch filters.
- `Debit` is BUY and `Kredit Jual` is SELL.
- Excel IDR mutation cells remain numeric and use a currency number format.
- PDF output is A4 landscape.
- Preserve unrelated working-tree changes.

## Review Focus

- A user submitting an unauthorized branch must fail validation rather than read another branch.
- An unchecked Semua Currency filter must require one valid currency.
- A period with opening-only currency must retain its beginning balance row.
- A zero or missing aggregate must normalize to numeric zero without division errors.
- Excel IDR cells must be numbers, not strings containing `Rp.`.

---

### Task 1: Verify report formulas with pure unit tests

**Files:**
- Create: `tests/Unit/SummaryValasReportTest.php`
- Modify: `app/Http/Controllers/SummaryValasController.php` only if the helper contract needs correction.

**Interfaces:**
- Consumes `SummaryValasController::calculateRow(string $code, string $name, array $values): array`.
- Consumes `SummaryValasController::reportCurrencyIds(Collection $opening, Collection $period): Collection`.

- [ ] **Step 1: Add tests for beginning, mutations, ending, IDR, zero defaults, and opening/period selection.**
- [ ] **Step 2: Run the focused unit test if terminal execution is permitted by project policy.**
- [ ] **Step 3: Read the controller and compare each assertion to the implementation.**

---

### Task 2: Complete filter presentation and route coverage

**Files:**
- Modify: `resources/views/pages/laporan/summary-valas/index.blade.php`
- Modify: `app/Http/Controllers/SummaryValasController.php`
- Create: `tests/Feature/SummaryValasControllerTest.php`

**Interfaces:**
- GET `summary-valas.index` renders authorized branches and currencies.
- GET `summary-valas.download` accepts `cabang_id`, `start_date`, `end_date`, `currency_id`, `semua_currency`, and `format`.

- [ ] **Step 1: Render currency code and country in the selector.**
- [ ] **Step 2: Disable and clear the currency selector when Semua Currency is checked; require it when unchecked.**
- [ ] **Step 3: Add route registration assertions.**
- [ ] **Step 4: Read routes and controller to confirm route names and validation names match.**

---

### Task 3: Document and review output adapters

**Files:**
- Create: `docs/superpowers/specs/2026-10-03-summary-valas-design.md`
- Modify: `app/Exports/SummaryValasSheet.php`

**Interfaces:**
- Excel view emits numeric values for columns G/H.
- PDF view emits formatted IDR text and is rendered landscape by the controller.

- [ ] **Step 1: Document tables, mapping, formulas, date boundaries, access, and output formatting.**
- [ ] **Step 2: Remove unused exporter imports.**
- [ ] **Step 3: Read controller, export, and both output views for consistent eight-column labels and data keys.**
- [ ] **Step 4: Perform source-level verification; runtime tests remain unrun under the repository terminal restriction.**
