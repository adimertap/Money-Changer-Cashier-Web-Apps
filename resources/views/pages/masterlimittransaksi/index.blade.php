@extends('layouts.app')

@section('content')
<main>
    <div class="row justify-content-center mt-3">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-passport me-2"></i>Batas Transaksi Paspor Customer
                        </h5>
                        <p class="mb-0 text-muted fs--1">Pengaturan batas kuncian akumulasi transaksi paspor customer</p>
                    </div>
                    <div>
                        @if ($limit->is_active)
                            <span class="badge rounded-pill bg-success fs--2 px-3 py-1">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge rounded-pill bg-danger fs--2 px-3 py-1">
                                <i class="fas fa-times-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('master-limit-transaksi.update', $limit->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Batas Nominal IDR --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="nominal_limit_idr">
                                Batas Transaksi Rupiah (IDR) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-200">Rp</span>
                                <input class="form-control @error('nominal_limit_idr') is-invalid @enderror"
                                    type="number" step="100000" id="nominal_limit_idr" name="nominal_limit_idr"
                                    value="{{ old('nominal_limit_idr', (int) $limit->nominal_limit_idr) }}" required>
                            </div>
                            <small class="text-muted">Nominal batas transaksi akumulasi paspor (default BI: Rp 180.000.000)</small>
                            @error('nominal_limit_idr')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Acuan Valas USD & Periode Hari --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="ekuivalen_usd">
                                    Acuan Valas (USD) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-200">$</span>
                                    <input class="form-control @error('ekuivalen_usd') is-invalid @enderror"
                                        type="number" step="100" id="ekuivalen_usd" name="ekuivalen_usd"
                                        value="{{ old('ekuivalen_usd', (int) $limit->ekuivalen_usd) }}" required>
                                </div>
                                <small class="text-muted">Standar acuan regulasi (USD 10.000)</small>
                                @error('ekuivalen_usd')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold" for="periode_hari">
                                    Periode Rolling (Hari) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input class="form-control @error('periode_hari') is-invalid @enderror"
                                        type="number" min="1" max="365" id="periode_hari" name="periode_hari"
                                        value="{{ old('periode_hari', $limit->periode_hari) }}" required>
                                    <span class="input-group-text bg-200">Hari</span>
                                </div>
                                <small class="text-muted">Rentang hari akumulasi (default: 30 hari)</small>
                                @error('periode_hari')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Status Validasi Switch --}}
                        <div class="mb-3 p-3 bg-light rounded">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', $limit->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">
                                    Aktifkan Validasi &amp; Pop-up Dokumen di Kasir
                                </label>
                            </div>
                            <small class="text-muted">Jika diaktifkan, transaksi kasir yang melebihi batas ini wajib melampirkan dokumen pendukung.</small>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="keterangan">Keterangan / Catatan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                id="keterangan" name="keterangan" rows="2"
                                placeholder="Catatan internal">{{ old('keterangan', $limit->keterangan) }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
