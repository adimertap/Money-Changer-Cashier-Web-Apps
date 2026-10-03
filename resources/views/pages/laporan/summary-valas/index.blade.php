@extends('layouts.app')

@section('content')
<main>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-1">Summary Valas</h5>
            <p class="mb-0 fs--1">Ringkasan saldo dan mutasi currency berdasarkan periode.</p>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif
            <form method="GET" action="{{ route('summary-valas.download') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="summary-cabang">Cabang</label>
                        <select class="form-select" id="summary-cabang" name="cabang_id">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->cabang_id }}" {{ (string) request('cabang_id') === (string) $cabang->cabang_id ? 'selected' : '' }}>{{ $cabang->cabang_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="summary-start">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="summary-start" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="summary-end">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" id="summary-end" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="summary-currency">Currency</label>
                        <select class="form-select" id="summary-currency" name="currency_id">
                            <option value="">Pilih Currency</option>
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->id_currency }}" {{ (string) request('currency_id') === (string) $currency->id_currency ? 'selected' : '' }}>{{ $currency->nama_currency }} - {{ $currency->country }}</option>
                            @endforeach
                        </select>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="semua_currency" value="1" id="summary-semua-currency" {{ request()->boolean('semua_currency', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="summary-semua-currency">Semua Currency</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" type="submit" name="format" value="excel"><span class="fas fa-file-excel me-1"></span>Download Excel</button>
                    <button class="btn btn-danger" type="submit" name="format" value="pdf"><span class="fas fa-file-pdf me-1"></span>Download PDF</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var allCurrency = document.getElementById('summary-semua-currency');
        var currency = document.getElementById('summary-currency');

        function syncCurrencyFilter() {
            currency.disabled = allCurrency.checked;
            currency.required = !allCurrency.checked;
            if (allCurrency.checked) currency.value = '';
        }

        allCurrency.addEventListener('change', syncCurrencyFilter);
        syncCurrencyFilter();
    });
</script>
@endsection
