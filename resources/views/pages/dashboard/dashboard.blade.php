@extends('layouts.app')

@section('content')
<main>
    <div class="row g-3 mb-3">
        <div class="col-xxl-8">
            <div class="col-12 mb-3">
                <div class="card bg-transparent-50 overflow-hidden">
                    <div class="card-header position-relative">
                        <div class="bg-holder d-none d-md-block bg-card z-index-1"
                            style="background-image:url(../falcon/assets/img/illustrations/ecommerce-bg.png);background-size:230px;background-position:right bottom;z-index:-1;">
                        </div>
                        <!--/.bg-holder-->
                        <div class="position-relative z-index-2">
                            <div>
                                <h3 class="text-primary mb-1">Welcome Back, {{ Auth::user()->name }}!</h3>
                                <p>Here’s what happening with your store today </p>
                            </div>
                            <div class="d-flex py-3">
                                <div class="pe-3">
                                    <p class="text-600 fs--1 fw-medium">Jumlah Transaksi Hari Ini</p>
                                    <h4 class="text-800 mb-0">{{ $jumlah_hari_ini }} Transaksi</h4>
                                </div>
                                <div class="ps-3">
                                    <p class="text-600 fs--1">Total Transaksi Hari Ini</p>
                                    <h4 class="text-800 mb-0">Rp. {{ number_format($total_hari_ini) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
                            <div class="d-flex flex-between-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="icon-item icon-item-sm bg-soft-primary shadow-none me-2 bg-soft-primary">
                                        <span class="fs--2 fas fa-dollar-sign text-primary"></span>
                                    </div>
                                    <h6 class="mb-0">Data Currency</h6>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex">
                                    <p class="font-sans-serif lh-1 mb-1 fs-2 pe-2">{{ $currency }} Currency</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                            <div class="d-flex flex-between-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-item icon-item-sm bg-soft-primary shadow-none me-2 bg-soft-info">
                                        <span class="fs--2 fas fa-user text-info"></span>
                                    </div>
                                    <h6 class="mb-0">Pegawai Anda</h6>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex">
                                    <p class="font-sans-serif lh-1 mb-1 fs-2 pe-2">{{ $pegawai }} Pegawai</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                            <div class="d-flex flex-between-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-item icon-item-sm bg-soft-primary shadow-none me-2 bg-soft-info">
                                        <span class="fs--2 fas fa-wallet text-info"></span>
                                    </div>
                                    <h6 class="mb-0">Pengajuan Modal Hari Ini</h6>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="d-flex">
                                    <p class="font-sans-serif lh-1 mb-1 fs-2 pe-2">{{ $modal }} Pengajuan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-light mb-3">
                <div class="card-body p-3">
                    <p class="fs--1 mb-0"><a href="{{ route('jurnal-bulanan.index') }}">
                            <span class="fas fa-exchange-alt me-2" data-fa-transform="rotate-90"></span>
                            Klik untuk lihat <strong>Jurnal Bulanan </strong>Perusahaan Anda</a>. Data dibawah
                        menampilkan informasi penting Perusahaan Anda</p>
                </div>
            </div>
            <div class="card py-3 mb-3">
                <div class="card-body py-3">
                    <div class="row g-0">
                        <div class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                            <h6 class="pb-1 text-700">Transaksi Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $jumlah_hari_ini }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-end pb-4 ps-3">
                            <h6 class="pb-1 text-700">Total Transaksi Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_hari_ini) }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Hari Ini</h6>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-4 border-200 border-bottom border-end border-md-end-0 pb-4 pt-4 pt-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Sisa Modal Hari Ini </h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($sisa_modal->riwayat_modal) }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Dari Rp. {{ number_format($sisa_modal->jumlah_modal) }} </h6>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-bottom-0 border-md-end pt-4 pb-md-0 ps-3 ps-md-0">
                            <h6 class="pb-1 text-700">Transaksi Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $jumlah_bulan_ini }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                                </h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-bottom-0 border-end pt-4 pb-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Total Transaksi Bulan Ini </h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_bulan_ini) }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Bulan {{ $bulan_ini }}</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 pb-0 pt-4 ps-3">
                            <h6 class="pb-1 text-700">Total Modal Terpakai Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_modal_bulan_ini) }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Bulan {{ $bulan_ini }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Last Activity Today</h6>
                </div>
                <div class="card-body scrollbar recent-activity-body-height ps-2">
                    @forelse ($transaksi as $item)
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
                                    <h6 class="text-800 mb-1">New Order:#{{ $item->kode_transaksi }}</h6>
                                    @forelse ($item->detailTransaksi as $tes)
                                        <p class="fs--1 text-600 mb-0">{{ $tes->Currency->nama_currency }} Harga Rp. {{ number_format($tes->jumlah_currency) }} Jumlah {{ $tes->jumlah_tukar }}</p>
                                    @empty
                                        
                                    @endforelse
                                 
                                </div>
                                <div class="col-auto text-end">
                                    <p class="fs--2 text-500 mb-0">Pukul {{ date('H:i:s', strtotime($item->created_at)) }}</p>
                                    <p class="fs--2 text-500 mb-0">Pegawai {{ $item->Pegawai->nama_panggilan }}</p>
                                    <p class="fs--1 text-primary mb-0">Total Rp. {{ number_format($item->total) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</main>
@endsection
