@extends('layouts.app')

@section('content')
<main>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-1">Unduh Laporan Rekapitulasi Cabang</h5>
            <p class="mb-0 fs--1">Rekap currency operasional dan sisa modal per cabang.</p>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif
            <form method="GET" action="{{ route('laporan-rekap-cabang.download') }}">
                <input type="hidden" name="radio_input" value="excel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" name="from_date_export" value="{{ old('from_date_export', request('from_date_export')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input class="form-control" type="date" name="to_date_export" value="{{ old('to_date_export', request('to_date_export')) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pilih Cabang <span class="text-danger">*</span></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="semua_cabang" value="1" id="semuaCabang" {{ request('semua_cabang') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="semuaCabang">Semua Cabang</label>
                        </div>
                        <div class="row" id="daftarCabang">
                            @forelse ($cabangs as $cabang)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input cabang-checkbox" type="checkbox" name="cabang_ids[]" value="{{ $cabang->cabang_id }}" id="cabang-{{ $cabang->cabang_id }}" {{ in_array($cabang->cabang_id, (array) request('cabang_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cabang-{{ $cabang->cabang_id }}">{{ $cabang->cabang_name }}</label>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-muted">Tidak ada cabang aktif yang tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-4" type="submit"><span class="fas fa-file-excel me-1"></span>Download Excel</button>
            </form>
        </div>
    </div>
</main>
<script>
    $(function () {
        function toggleCabang() {
            const semua = $('#semuaCabang').is(':checked');
            $('.cabang-checkbox').prop('disabled', semua);
        }

        $('#semuaCabang').on('change', toggleCabang);
        toggleCabang();
    });
</script>
@endsection
