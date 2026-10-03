@extends('layouts.app')

@section('content')
<main>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-1">Unduh LKUB</h5>
            <p class="mb-0 fs--1">Laporan Kegiatan Usaha Bulanan per currency dan cabang.</p>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif
            <form method="GET" action="{{ route('laporan-lkub.download') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="lkub-month">Bulan <span class="text-danger">*</span></label>
                        <select class="form-select" id="lkub-month" name="month" required>
                            @foreach ($months as $number => $label)
                                <option value="{{ $number }}" {{ (int) old('month', request('month', now()->month)) === $number ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="lkub-year">Tahun <span class="text-danger">*</span></label>
                        <select class="form-select" id="lkub-year" name="year" required>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ (int) old('year', request('year', now()->year)) === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pilih Cabang <span class="text-danger">*</span></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="semua_cabang" value="1" id="lkub-semua-cabang" {{ request('semua_cabang') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="lkub-semua-cabang">Semua Cabang</label>
                        </div>
                        <div class="row" id="lkub-daftar-cabang">
                            @forelse ($cabangs as $cabang)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input lkub-cabang-checkbox" type="checkbox" name="cabang_ids[]" value="{{ $cabang->cabang_id }}" id="lkub-cabang-{{ $cabang->cabang_id }}" {{ in_array($cabang->cabang_id, (array) request('cabang_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="lkub-cabang-{{ $cabang->cabang_id }}">{{ $cabang->cabang_name }}</label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted">Tidak ada cabang aktif yang tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button class="btn btn-primary" type="submit" name="format" value="excel"><span class="fas fa-file-excel me-1"></span>Download Excel</button>
                    <button class="btn btn-danger" type="submit" name="format" value="pdf"><span class="fas fa-file-pdf me-1"></span>Download PDF</button>
                    <button class="btn btn-success" type="submit" name="format" value="csv"><span class="fas fa-file-csv me-1"></span>Download CSV</button>
                    <button class="btn btn-secondary" type="submit" name="format" value="txt"><span class="fas fa-file-alt me-1"></span>Download TXT</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    $(function () {
        function toggleLkubCabang() {
            $('.lkub-cabang-checkbox').prop('disabled', $('#lkub-semua-cabang').is(':checked'));
        }

        $('#lkub-semua-cabang').on('change', toggleLkubCabang);
        toggleLkubCabang();
    });
</script>
@endsection
