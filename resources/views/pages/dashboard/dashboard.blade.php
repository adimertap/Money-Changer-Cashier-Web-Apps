@extends('layouts.app')

@section('content')
<style>
    .dash-hero-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.025em;
    }
    .dash-hero-sub {
        color: #64748b;
        font-size: 0.875rem;
    }
    .dash-btn-primary {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: #ffffff !important;
        border-radius: 10px;
        padding: 9px 20px;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.28);
        transition: all 0.2s ease;
    }
    .dash-btn-primary:hover {
        background: linear-gradient(135deg, #4338ca, #3730a3);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
        color: #ffffff;
    }
    .dash-stat-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.025);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dash-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
    }
    .dash-stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .dash-stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }
    .dash-stat-label {
        font-size: 0.825rem;
        font-weight: 600;
        color: #475569;
        margin-left: 8px;
    }
    .dash-stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        letter-spacing: -0.015em;
    }
    .dash-growth-pill {
        font-size: 0.775rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .dash-growth-green {
        background-color: #ecfdf5;
        color: #10b981;
    }
    .dash-growth-danger {
        background-color: #fef2f2;
        color: #dc2626;
    }
    .dash-growth-neutral {
        background-color: #f1f5f9;
        color: #475569;
    }
    .dash-pill-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .dash-pill-success {
        background-color: #ecfdf5;
        color: #059669;
    }
    .dash-pill-warning {
        background-color: #fffbeb;
        color: #d97706;
    }
    .dash-pill-danger {
        background-color: #fef2f2;
        color: #dc2626;
    }
    .dash-pill-primary {
        background-color: #eff6ff;
        color: #2563eb;
    }
    .dash-pill-neutral {
        background-color: #f1f5f9;
        color: #475569;
    }
    .dash-section-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 22px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.025);
    }
    .dash-pill-tabs {
        background: #f1f5f9;
        border-radius: 9999px;
        padding: 4px 6px;
        display: inline-flex;
        gap: 4px;
    }
    .dash-pill-tab {
        padding: 5px 16px;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dash-pill-tab.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .dash-branch-card {
        background: #ffffff;
        border: 1px solid #e9edf4;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 18px;
        transition: all 0.2s ease;
    }
    .dash-branch-card:last-child {
        margin-bottom: 0;
    }
    .dash-branch-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }
    .dash-branch-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .dash-inner-metrics {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 18px 22px;
        margin-top: 14px;
    }
    .dash-metric-title {
        font-size: 0.775rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
    }
    .dash-metric-num {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        letter-spacing: -0.015em;
    }
    .dash-metric-sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 5px;
    }
    .dash-panel-card {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.025);
    }
    .dash-table-head {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
    }
    .dash-table-head th {
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding-top: 14px;
        padding-bottom: 14px;
    }
</style>

<main>
    @if(Auth::user()->role == 'Owner')
    {{-- ================= OWNER DASHBOARD ================= --}}
    <!-- Top Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="dash-hero-title mb-1">Ringkasan Operasional & Cabang</h2>
            <p class="dash-hero-sub mb-0">PT Riastavalasindo &bull; Monitoring transaksi valas, modal kas, dan approval cabang realtime</p>
        </div>
        <!-- <div class="d-flex align-items-center gap-2">
            @if(Route::has('transaksi.create'))
            <a href="{{ route('transaksi.create') }}" class="dash-btn-primary">
                <i class="fas fa-plus"></i> Transaksi Baru
            </a>
            @endif
        </div> -->
    </div>

    <!-- 4 Top Stat Cards (Sesuai Referensi Gambar) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Transaksi Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dash-stat-card h-100">
                <div class="dash-stat-header">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-primary text-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <span class="dash-stat-label">Total Transaksi Hari Ini</span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-2">
                    <div class="dash-stat-value text-primary">Rp {{ number_format($total_hari_ini, 0, ',', '.') }}</div>
                    <span class="dash-growth-pill dash-growth-green">
                        <i class="fas fa-arrow-up fs--2"></i> Hari Ini
                    </span>
                </div>
                <div class="fs--2 text-500 mt-2">
                    <i class="fas fa-check-circle text-success me-1"></i>Transaksi beli hari ini
                </div>
            </div>
        </div>

        <!-- Card 2: Count Transaksi Hari Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dash-stat-card h-100">
                <div class="dash-stat-header">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-info text-info">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <span class="dash-stat-label">Count Transaksi Hari Ini</span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-2">
                    <div class="dash-stat-value">{{ number_format($jumlah_hari_ini, 0, ',', '.') }} <span class="fs--1 text-500 fw-normal">Trx</span></div>
                    <span class="dash-growth-pill dash-growth-green">
                        <i class="fas fa-bolt fs--2"></i> Realtime
                    </span>
                </div>
                <div class="fs--2 text-500 mt-2">
                    <i class="fas fa-sync text-info me-1"></i>Seluruh cabang aktif
                </div>
            </div>
        </div>

        <!-- Card 3: Total Transaksi Bulan Ini (dengan comparison bulan lalu) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dash-stat-card h-100">
                <div class="dash-stat-header">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-success text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="dash-stat-label">Total Transaksi Bulan Ini</span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-2">
                    <div class="dash-stat-value text-success">Rp {{ number_format($total_bulan_ini, 0, ',', '.') }}</div>
                    @if($diff_total_bulan >= 0)
                    <span class="dash-growth-pill dash-growth-green">
                        <i class="fas fa-arrow-up fs--2"></i> +{{ number_format($diff_total_bulan, 1) }}%
                    </span>
                    @else
                    <span class="dash-growth-pill dash-growth-danger">
                        <i class="fas fa-arrow-down fs--2"></i> {{ number_format($diff_total_bulan, 1) }}%
                    </span>
                    @endif
                </div>
                <div class="fs--2 text-500 mt-2">
                    @if($diff_total_bulan >= 0)
                    <span class="text-success fw-semi-bold"><i class="fas fa-arrow-up me-1"></i>Naik vs bln lalu</span> (Rp {{ number_format($total_bulan_lalu, 0, ',', '.') }})
                    @else
                    <span class="text-danger fw-semi-bold"><i class="fas fa-arrow-down me-1"></i>Turun vs bln lalu</span> (Rp {{ number_format($total_bulan_lalu, 0, ',', '.') }})
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 4: Count Transaksi Bulan Ini -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dash-stat-card h-100">
                <div class="dash-stat-header">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-warning text-warning">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <span class="dash-stat-label">Count Transaksi Bulan Ini</span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline justify-content-between mt-2">
                    <div class="dash-stat-value">{{ number_format($jumlah_bulan_ini, 0, ',', '.') }} <span class="fs--1 text-500 fw-normal">Trx</span></div>
                    <span class="dash-growth-pill dash-growth-neutral">{{ count($cabangStats) }} Cabang</span>
                </div>
                <div class="fs--2 text-500 mt-2">
                    <i class="fas fa-calendar-alt text-warning me-1"></i>Periode {{ $bulan_ini }}
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Masing-Masing Cabang (Container "All Integrations" Style Sesuai Gambar) -->
    @if(isset($cabangStats) && count($cabangStats) > 0)
    <div class="dash-section-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h3 class="fw-bold mb-0 text-900" style="font-size: 1.25rem; letter-spacing: -0.02em;">Semua Cabang Aktif</h3>
            <div class="dash-pill-tabs">
                <span class="dash-pill-tab active">Semua Cabang ({{ count($cabangStats) }})</span>
            </div>
        </div>

        <div class="row g-3">
            @foreach($cabangStats as $stat)
            <div class="col-12">
                <div class="dash-branch-card">
                    <!-- Header Cabang (Bersih tanpa tombol action) -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="dash-branch-icon me-3">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-900" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ $stat->cabang_name }}</h4>
                            <div class="text-500 fs--1 mt-1">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $stat->lokasi }}
                                <span class="mx-2 text-300">&bull;</span>
                                <i class="fas fa-users text-primary me-1"></i>{{ $stat->pegawai_count }} Pegawai Ditugaskan
                                @if($stat->pengajuan_pending_count > 0)
                                <span class="mx-2 text-300">&bull;</span>
                                <span class="text-warning fw-semi-bold">
                                    <i class="fas fa-clock me-1"></i>{{ $stat->pengajuan_pending_count }} Pengajuan Modal Pending
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Inner Metrics Container (5 Kolom: Tambah Transaksi Hari Ini di kiri Transaksi Bulan Ini) -->
                    <div class="dash-inner-metrics">
                        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5">
                            <!-- 1. Transaksi Hari Ini (Beli) -->
                            <div class="col border-xl-end border-200">
                                <div class="px-1">
                                    <div class="dash-metric-title">Transaksi Hari Ini (Beli)</div>
                                    <div class="dash-metric-num text-primary">Rp {{ number_format($stat->total_hari_ini, 0, ',', '.') }}</div>
                                    <div class="dash-metric-sub">
                                        <span class="badge bg-soft-primary text-primary px-2 py-0 me-1">{{ $stat->jumlah_hari_ini }} Trx</span>
                                        Realtime
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Transaksi Bulan Ini (Beli) dengan perbandingan bulan lalu -->
                            <div class="col border-xl-end border-200">
                                <div class="px-1">
                                    <div class="dash-metric-title">Transaksi Bulan Ini (Beli)</div>
                                    <div class="dash-metric-num text-900">Rp {{ number_format($stat->total_bulan_ini, 0, ',', '.') }}</div>
                                    <div class="dash-metric-sub">
                                        @if($stat->diff_bulan >= 0)
                                        <span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>+{{ number_format($stat->diff_bulan, 1) }}%</span>
                                        @else
                                        <span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i>{{ number_format($stat->diff_bulan, 1) }}%</span>
                                        @endif
                                        <span class="text-500 ms-1">vs bln lalu</span> &bull; <span class="badge bg-soft-secondary text-600">{{ $stat->jumlah_bulan_ini }} Trx</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Jual Valas Bulan Ini -->
                            <div class="col border-xl-end border-200">
                                <div class="px-1">
                                    <div class="dash-metric-title">Jual Valas Bulan Ini</div>
                                    <div class="dash-metric-num text-danger">Rp {{ number_format($stat->total_jual_bulan_ini, 0, ',', '.') }}</div>
                                    <div class="dash-metric-sub">
                                        <span class="badge bg-soft-danger text-danger px-2 py-0 me-1">{{ $stat->jumlah_jual_bulan_ini }} Trx</span>
                                        Periode {{ $bulan_ini }}
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Sisa Modal Hari Ini -->
                            <div class="col border-xl-end border-200">
                                <div class="px-1">
                                    <div class="dash-metric-title">Sisa Modal Hari Ini</div>
                                    <div class="dash-metric-num text-success">
                                        @if($stat->sisa_modal !== null)
                                        Rp {{ number_format($stat->sisa_modal, 0, ',', '.') }}
                                        @else
                                        <span class="text-400">-</span>
                                        @endif
                                    </div>
                                    <div class="dash-metric-sub">
                                        @if($stat->modal_awal)
                                        Modal awal: Rp {{ number_format($stat->modal_awal, 0, ',', '.') }}
                                        @else
                                        Belum ada kas hari ini
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- 5. Modal Terpakai Bulan Ini -->
                            <div class="col">
                                <div class="px-1">
                                    <div class="dash-metric-title">Modal Terpakai (Bulan Ini)</div>
                                    <div class="dash-metric-num text-900">Rp {{ number_format($stat->total_modal_terpakai_bulan_ini, 0, ',', '.') }}</div>
                                    <div class="dash-metric-sub">
                                        Akumulasi kas terpakai cabang
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Table Approval Modal (col-12 full grid) dengan proses terima dan tolak langsung -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dash-panel-card">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-warning text-warning me-2">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-900">Approval Modal</h5>
                            <span class="text-500 fs--2">Persetujuan pengajuan modal kas harian pegawai cabang</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('approval-modal.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs--1">
                            Halaman Approval <i class="fas fa-arrow-right ms-1 fs--2"></i>
                        </a>
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover fs--1 mb-0 align-middle">
                            <thead class="dash-table-head">
                                <tr>
                                    <th class="ps-4">Cabang</th>
                                    <th>Tanggal</th>
                                    <th>Pegawai</th>
                                    <th class="text-end">Total Modal</th>
                                    <th class="text-end">Tambahan</th>
                                    <th class="text-center">Status Modal</th>
                                    <th class="text-center pe-4" style="width: 190px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($approvalModal as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-900">
                                        <span class="badge bg-soft-primary text-primary fs--2 px-2 py-1">
                                            <i class="fas fa-building me-1"></i>{{ optional($item->Cabang)->cabang_name ?: '-' }}
                                        </span>
                                    </td>
                                    <td>{{ date('d-M-Y', strtotime($item->tanggal_modal)) }}</td>
                                    <td class="fw-semi-bold">{{ optional($item->Pegawai)->name ?: '-' }}</td>
                                    <td class="text-end fw-bold text-900">Rp {{ number_format($item->riwayat_modal, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        @if ($item->pengajuan_tambah != null)
                                        <span class="text-primary fw-bold">Rp {{ number_format($item->pengajuan_tambah, 0, ',', '.') }}</span>
                                        @else
                                        <span class="text-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status_modal == 'Pending')
                                        <span class="dash-pill-badge dash-pill-warning">Menunggu Approval</span>
                                        @elseif ($item->status_modal == 'Terima')
                                        <span class="dash-pill-badge dash-pill-success">Diterima</span>
                                        @else
                                        <span class="dash-pill-badge dash-pill-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        @if ($item->status_modal == 'Pending')
                                        <button class="btn btn-success btn-sm rounded-pill py-1 px-3 me-1 terimaModalBtn fw-semi-bold"
                                            value="{{ $item->id_modal }}" type="button" title="Terima Data">
                                            <i class="fas fa-check me-1"></i>Terima
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm rounded-pill py-1 px-3 tolakModalBtn fw-semi-bold"
                                            value="{{ $item->id_modal }}" type="button" title="Tolak Data">
                                            <i class="fas fa-times me-1"></i>Tolak
                                        </button>
                                        @else
                                        <span class="text-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-500">
                                        <i class="fas fa-check-double text-success fa-2x mb-2 d-block"></i>
                                        Tidak ada pengajuan modal yang menunggu approval saat ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Activity Today (col-12 dibawah approval modal) -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="dash-panel-card">
                <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom bg-white">
                    <div class="d-flex align-items-center">
                        <div class="dash-stat-icon bg-soft-info text-info me-2">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-900">Aktivitas Transaksi Hari Ini</h5>
                            <span class="text-500 fs--2">Riwayat transaksi beli valas yang masuk realtime</span>
                        </div>
                    </div>
                    <span class="dash-pill-badge dash-pill-neutral fs--2">Hari Ini</span>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover fs--1 mb-0 align-middle">
                            <thead class="dash-table-head">
                                <tr>
                                    <th class="ps-4">Kode Order</th>
                                    <th>Waktu</th>
                                    <th>Cabang</th>
                                    <th>Pegawai</th>
                                    <th>Detail Currency</th>
                                    <th class="text-end pe-4">Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#{{ $item->kode_transaksi }}</td>
                                    <td><i class="far fa-clock text-400 me-1"></i>{{ date('H:i:s', strtotime($item->created_at)) }}</td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">
                                            {{ $item->Cabang->cabang_name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="fw-semi-bold">{{ $item->Pegawai->nama_panggilan ?? '-' }}</td>
                                    <td>
                                        @forelse ($item->detailTransaksi as $tes)
                                        <span class="d-inline-block me-2">
                                            <strong>{{ $tes->Currency->nama_currency }}</strong>: {{ $tes->jumlah_tukar }} @ Rp {{ number_format($tes->jumlah_currency, 0, ',', '.') }}
                                        </span>
                                        @empty
                                        <span class="text-400">-</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-900 fs--1">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-500">
                                        Belum ada aktivitas transaksi hari ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ================= PEGAWAI DASHBOARD ================= --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-8">
            <div class="card bg-transparent-50 overflow-hidden mb-3">
                <div class="card-header position-relative">
                    <div class="bg-holder d-none d-md-block bg-card z-index-1"
                        style="background-image:url(../falcon/assets/img/illustrations/ecommerce-bg.png);background-size:230px;background-position:right bottom;z-index:-1;">
                    </div>
                    <div class="position-relative z-index-2">
                        <div>
                            <h3 class="text-primary mb-1">Welcome Back, {{ Auth::user()->name }}!</h3>
                            <p class="mb-0">
                                <span class="fas fa-store text-primary me-1"></span>Cabang:
                                @forelse (session('cabangs', []) as $c)
                                <span class="badge rounded-pill badge-soft-primary me-1">{{ $c['cabang_name'] }}</span>
                                @empty
                                <span class="text-500">Belum di-assign ke cabang</span>
                                @endforelse
                            </p>
                        </div>
                        <div class="d-flex py-3">
                            <div class="pe-3">
                                <p class="text-600 fs--1 fw-medium">Jumlah Transaksi Anda Hari Ini</p>
                                <h4 class="text-800 mb-0">{{ $pegawai_count_money_today }} Transaksi</h4>
                            </div>
                            <div class="ps-3">
                                <p class="text-600 fs--1">Total Transaksi Anda Hari ini</p>
                                <h4 class="text-800 mb-0">Rp. {{ number_format($pegawai_money_today_total, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pegawai -->
            <div class="card py-3 mb-3">
                <div class="card-body py-3">
                    <div class="row g-0">
                        <div class="col-6 border-200 border-bottom border-end pb-3">
                            <h6 class="pb-1 text-700 fs--1">Transaksi Beli Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $pegawai_count_money_today }}</p>
                            <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                        </div>
                        <div class="col-6 border-200 border-bottom pb-3 ps-3">
                            <h6 class="pb-1 text-700 fs--1">Total Transaksi Beli Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($pegawai_money_today_total, 0, ',', '.') }}</p>
                            <h6 class="fs--1 text-500 mb-0">Hari Ini</h6>
                        </div>
                        <div class="col-6 border-200 border-end pt-3">
                            <h6 class="pb-1 text-700 fs--1">Total Transaksi Beli Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($pegawai_sum_money_bulan, 0, ',', '.') }}</p>
                            <h6 class="fs--1 text-500 mb-0">Bulan Ini: {{ $bulan_ini }}</h6>
                        </div>
                        <div class="col-6 pt-3 ps-3">
                            <h6 class="pb-1 text-700 fs--1">Total Transaksi Jual Valas Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($pegawai_sum_money_bulan_jual, 0, ',', '.') }}</p>
                            <h6 class="fs--1 text-500 mb-0">Bulan Ini: {{ $bulan_ini }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 text-800">Last Activity Money Changer</h6>
                </div>
                <div class="card-body scrollbar recent-activity-body-height ps-2">
                    @forelse ($transaksi_pegawai_money as $item)
                    <div class="row g-3 timeline timeline-primary timeline-past pb-card">
                        <div class="col-auto ps-4 ms-2">
                            <div class="ps-2">
                                <div class="icon-item icon-item-sm rounded-circle bg-200 shadow-none">
                                    <span class="text-primary fas fa-money-bill"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row gx-0 border-bottom pb-card">
                                <div class="col">
                                    <h6 class="text-800 mb-1 fs--1">#{{ $item->kode_transaksi }}</h6>
                                    @forelse ($item->detailTransaksi as $tes)
                                    <p class="fs--2 text-600 mb-0">{{ $tes->Currency->nama_currency }} Harga Rp. {{
                                        number_format($tes->jumlah_currency, 0, ',', '.') }} ({{
                                        $tes->jumlah_tukar }})</p>
                                    @empty
                                    @endforelse
                                </div>
                                <div class="col-auto text-end">
                                    <p class="fs--2 text-500 mb-0">{{ date('H:i', strtotime($item->created_at)) }}</p>
                                    <p class="fs--1 text-primary fw-semi-bold mb-0">Rp. {{ number_format($item->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-500 fs--1">
                        Belum ada riwayat transaksi Anda hari ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
</main>

<div class="modal fade" style="margin-top: 130px" id="modalMenu" data-bs-keyboard="false" data-bs-backdrop="static"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centerd" role="document">
        <div class="modal-content border-0">
            <div class="modal-body p-0">
                <div class="bg-light rounded-top-lg py-3 ps-4 pe-6">
                    <h4 class="mb-1" id="staticBackdropLabel">Aplikasi PT Riastavalasindo</h4>
                    <p class="fs--2 mb-0">Pilih Aplikasi yang ingin dituju</a></p>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="d-flex"><span class="fa-stack ms-n1 me-3"><i
                                        class="fas fa-circle fa-stack-2x text-200"></i><i
                                        class="fa-inverse fa-stack-1x text-primary fas fa-align-left"
                                        data-fa-transform="shrink-2"></i></span>
                                <div class="flex-1">
                                    <h5 class="mb-2 fs-0">Pilih Aplikasi</h5>
                                    <p class="text-word-break fs--1">Terdapat 2 Aplikasi Klik Button untuk menuju
                                        aplikasi yang ingin dituju</p>
                                </div>
                            </div>
                            <hr>
                            <div class="rounded-1 p-2">
                                <div class="d-flex justify-content-center mb-3">
                                    <button id="aplikasi-money" class="btn btn-primary btn-lg me-1 mb-1"
                                        style="height: 100px; width: 300px; margin-right:50px" type="button">
                                        <span class="far fa-bookmark me-1" data-fa-transform="shrink-3"></span>Money
                                        Changer
                                    </button>
                                    <button id="aplikasi-laundry" class="btn btn-primary btn-lg me-1 mb-1"
                                        style="height: 100px; width: 300px; margin-left: 50px" type="button">
                                        <span class="far fa-bookmark me-1" data-fa-transform="shrink-3"></span>Laundry
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(Auth::user()->role == 'Owner')
<div class="modal fade" id="TerimaModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ route('approval-modal.store') }}" id="form_terima" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-success rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Konfirmasi Approve Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_id" id="id_modal">
                                        <input type="hidden" name="status_modal" id="status" value="Terima">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Melakukan Approve Terhadap
                                            Data Modal ini?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success btn-sm" onclick="terima(event)" id="submit_terima" type="button">Ya, Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="TolakModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ route('approval-modal.store') }}" id="form_tolak" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Konfirmasi Tolak Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_id" id="id_modal2">
                                        <input type="hidden" name="status_modal" id="status" value="Tolak">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Melakukan Tolak Terhadap Data
                                            Modal ini? Berikan Keterangan!</p>
                                        <div class="col-12 mt-2">
                                            <label class="form-label" for="keterangan_approval">Keterangan Penolakan <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="keterangan_approval"
                                                name="keterangan_approval" rows="3"
                                                placeholder="Input Keterangan Penolakan" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger btn-sm" onclick="tolak(event)" id="submit_tolak" type="button">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function terima(event) {
        event.preventDefault();
        var form = $('#form_terima');
        var _token = form.find('input[name="_token"]').val();
        var modal_id = form.find('input[name="modal_id"]').val();
        var status_modal = form.find('input[name="status_modal"]').val();

        var data = {
            _token: _token,
            modal_id: modal_id,
            status_modal: status_modal,
        };
        $('#submit_terima').prop('disabled', true);

        $.ajax({
            method: 'post',
            url: '{{ route("approval-modal.store") }}',
            data: data,
            success: function (response) {
                window.location.reload();
            },
            error: function (response) {
                $('#submit_terima').prop('disabled', false);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error! Approval tidak dapat disimpan.',
                    });
                } else {
                    alert('Error! Approval tidak dapat disimpan.');
                }
            }
        });
    }

    function tolak(event) {
        event.preventDefault();
        var form = $('#form_tolak');
        var _token = form.find('input[name="_token"]').val();
        var modal_id = form.find('input[name="modal_id"]').val();
        var status_modal = form.find('input[name="status_modal"]').val();
        var keterangan_approval = form.find('textarea[name="keterangan_approval"]').val();

        if (!keterangan_approval) {
            alert('Keterangan penolakan wajib diisi!');
            return;
        }

        var data = {
            _token: _token,
            modal_id: modal_id,
            status_modal: status_modal,
            keterangan_approval: keterangan_approval
        };
        $('#submit_tolak').prop('disabled', true);

        $.ajax({
            method: 'post',
            url: '{{ route("approval-modal.store") }}',
            data: data,
            success: function (response) {
                window.location.reload();
            },
            error: function (response) {
                $('#submit_tolak').prop('disabled', false);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error! Penolakan tidak dapat disimpan.',
                    });
                } else {
                    alert('Error! Penolakan tidak dapat disimpan.');
                }
            }
        });
    }

    $(document).ready(function () {
        $('.terimaModalBtn').click(function (e) {
            e.preventDefault();
            var id = $(this).val();
            $('#id_modal').val(id);
            $('#TerimaModal').modal('show');
        });

        $('.tolakModalBtn').click(function (e) {
            e.preventDefault();
            var id = $(this).val();
            $('#id_modal2').val(id);
            $('#TolakModal').modal('show');
        });
    });
</script>
@endif
@endsection
