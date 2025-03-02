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
                        <div class="position-relative z-index-2">
                            <div>
                                <h3 class="text-primary mb-1">Welcome Back, {{ Auth::user()->name }}!</h3>
                                <p>Dashboard Pegawai Khusus untuk Pegawai</p>
                            </div>
                            @if(Auth::user()->role == 'Owner')
                            <hr>
                            <div class="d-flex py-3 ">
                                <div class="border p-3 px-5">
                                    <p class="text-700 fs--1 fw-medium">Transaksi <br>Hari Ini</p>
                                    <h5 class="text-800 mb-0">{{ $jumlah_hari_ini }} Transaksi</h5>
                                </div>
                                <div class="border p-3 px-5">
                                    <p class="text-700 fs--1">Total Transaksi <br>Hari Ini</p>
                                    <h5 class="text-800 mb-0">Rp. {{ number_format($total_hari_ini, 0, ',', '.') }}</h5>
                                </div>
                                <div class="border p-3 px-5">
                                    <p class="text-700 fs--1 fw-medium">Transaksi <br>Jual Valas Hari Ini</p>
                                    <h5 class="text-800 mb-0">{{ $jumlah_jual_hari_ini }} Transaksi</h5>
                                </div>
                                <div class="border p-3 px-5">
                                    <p class="text-700 fs--1">Total Transaksi <br>Jual Valas Hari Ini</p>
                                    <h5 class="text-800 mb-0">Rp. {{ number_format($total_jual_hari_ini, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            @else
                            <div class="d-flex py-3">
                                <div class="pe-3">
                                    <p class="text-600 fs--1 fw-medium">Jumlah Transaksi Anda Hari Ini</p>
                                    <h4 class="text-800 mb-0">{{ $pegawai_count_money_today }} Transaksi</h4>
                                </div>
                                <div class="ps-3">
                                    <p class="text-600 fs--1">Total Transaksi Anda Hari ini</p>
                                    <h4 class="text-800 mb-0">Rp.
                                        {{ number_format($pegawai_money_today_total, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()->role == 'Owner')
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
            {{-- <div class="card bg-light mb-3">
                <div class="card-body p-3">
                    <p class="fs--1 mb-0"><a href="{{ route('jurnal-bulanan.index') }}">
                            <span class="fas fa-exchange-alt me-2" data-fa-transform="rotate-90"></span>
                            Klik untuk lihat <strong>Jurnal Bulanan </strong>Perusahaan Anda</a>. Data dibawah
                        menampilkan informasi penting Perusahaan Anda</p>
                </div>
            </div> --}}
            @endif

            @if(Auth::user()->role == 'Owner')
            <div class="card py-3 mb-3">
                <div class="card-body py-3">
                    <div class="row g-0">
                        {{-- <div class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                            <h6 class="pb-1 text-700">Transaksi Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $jumlah_hari_ini }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-end pb-4 ps-3">
                            <h6 class="pb-1 text-700">Total Transaksi Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_hari_ini, 0, ',', '.')
                                }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Hari Ini</h6>
                            </div>
                        </div> --}}
                        <div
                        class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                        <h6 class="pb-1 text-700">Transaksi Jual Valas Bulan Ini</h6>
                        <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $jumlah_jual_bulan_ini, 0, ',', '.' }}</p>
                        <div class="d-flex align-items-center">
                            <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                            </h6>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-end pb-4 ps-3">
                        <h6 class="pb-1 text-700">Total Transaksi Jual Valas Bulan Ini </h6>
                        <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_jual_bulan_ini, 0, ',',
                            '.') }}</p>
                        <div class="d-flex align-items-center">
                            <h6 class="fs--1 text-500 mb-0">Bulan {{ $bulan_ini }}</h6>
                        </div>
                    </div>
                        <div
                            class="col-6 col-md-4 border-200 border-bottom border-end border-md-end-0 pb-4 pt-4 pt-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Sisa Modal Hari Ini </h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                @if ($sisa_modal == null)

                                @else
                                Rp. {{ number_format($sisa_modal->riwayat_modal, 0, ',', '.') }}
                                @endif
                            </p>

                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">
                                    @if ($sisa_modal == null)
                                    Hari Ini Belum Terdapat Modal
                                    @else
                                    Dari Rp. {{ number_format($sisa_modal->total_modal_backup, 0, ',', '.') }}
                                    @endif
                                </h6>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-bottom-0 border-md-end pt-4 pb-md-0 ps-3 ps-md-0">
                            <h6 class="pb-1 text-700">Transaksi Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $jumlah_bulan_ini, 0, ',', '.' }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                                </h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-bottom-0 border-end pt-4 pb-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Total Transaksi Bulan Ini </h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_bulan_ini, 0, ',',
                                '.') }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Bulan {{ $bulan_ini }}</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 pb-0 pt-4 ps-3">
                            <h6 class="pb-1 text-700">Total Modal Terpakai Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($total_modal_bulan_ini, 0,
                                ',', '.') }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Bulan {{ $bulan_ini }}</h6>
                            </div>
                        </div>
                        <hr class="mt-3">

                    </div>
                </div>
            </div>
            @else
            {{-- PEGAWAI --}}
            <div class="card py-3 mb-3">
                <div class="card-body py-3">
                    <div class="row g-0">
                        <div class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                            <h6 class="pb-1 text-700">Transaksi Money Changer Anda Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $pegawai_count_money_today }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-end pb-4 ps-3">
                            <h6 class="pb-1 text-700">Total Transaksi Money Changer Anda Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($pegawai_money_today_total, 0, ',', '.')
                                }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Hari Ini</h6>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-4 border-200 border-bottom border-end border-md-end-0 pb-4 pt-4 pt-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Total Transaksi Money Changer Anda Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                Rp. {{ number_format($pegawai_sum_money_bulan, 0, ',', '.')
                            }}
                                <div class="d-flex align-items-center">
                                    <h6 class="fs--1 text-500 mb-0">Bulan Ini: {{ $bulan_ini }}</h6>
                                </div>
                            </p>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-bottom border-end pb-4">
                            <h6 class="pb-1 text-700">Transaksi Jual Valas</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">{{ $pegawai_count_money_today_jual }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Transaksi</h6>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 border-200 border-md-200 border-bottom border-md-end pb-4 ps-3">
                            <h6 class="pb-1 text-700">Total Transaksi Jual Valas Anda Hari Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">Rp. {{ number_format($pegawai_money_today_total_jual, 0, ',', '.')
                                }}</p>
                            <div class="d-flex align-items-center">
                                <h6 class="fs--1 text-500 mb-0">Hari Ini</h6>
                            </div>
                        </div>
                        <div
                            class="col-6 col-md-4 border-200 border-bottom border-end border-md-end-0 pb-4 pt-4 pt-md-0 ps-md-3">
                            <h6 class="pb-1 text-700">Total Transaksi Jual Valas Anda Bulan Ini</h6>
                            <p class="font-sans-serif lh-1 mb-1 fs-2">
                                Rp. {{ number_format($pegawai_sum_money_bulan_jual, 0, ',', '.')
                            }}
                                <div class="d-flex align-items-center">
                                    <h6 class="fs--1 text-500 mb-0">Bulan Ini: {{ $bulan_ini }}</h6>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>

        @if(Auth::user()->role == 'Owner')
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
                                    <p class="fs--1 text-600 mb-0">{{ $tes->Currency->nama_currency }} Harga Rp. {{
                                        number_format($tes->jumlah_currency, 0, ',', '.') }} Jumlah {{
                                        $tes->jumlah_tukar }}</p>
                                    @empty

                                    @endforelse

                                </div>
                                <div class="col-auto text-end">
                                    <p class="fs--2 text-500 mb-0">Pukul {{ date('H:i:s', strtotime($item->created_at))
                                        }}</p>
                                    <p class="fs--2 text-500 mb-0">Pegawai {{ $item->Pegawai->nama_panggilan }}</p>
                                    <p class="fs--1 text-primary mb-0">Total Rp. {{ number_format($item->total, 0, ',',
                                        '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty



                    @endforelse

                </div>
            </div>
        </div>
        @else
        <div class="col-xxl-8">

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Last Activity Money Changer</h6>
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

                                    <h6 class="text-800 mb-1">New Order:#{{ $item->kode_transaksi }}</h6>
                                    @forelse ($item->detailTransaksi as $tes)
                                    <p class="fs--1 text-600 mb-0">{{ $tes->Currency->nama_currency }} Harga Rp. {{
                                                        number_format($tes->jumlah_currency, 0, ',', '.') }} Jumlah {{
                                                        $tes->jumlah_tukar }}</p>
                                    @empty

                                    @endforelse

                                </div>
                                <div class="col-auto text-end">
                                    <p class="fs--2 text-500 mb-0">Pukul {{ date('H:i:s', strtotime($item->created_at))
                                                        }}</p>
                                    <p class="fs--2 text-500 mb-0">Pegawai {{ $item->Pegawai->nama_panggilan }}</p>
                                    <p class="fs--1 text-primary mb-0">Total Rp. {{ number_format($item->total, 0, ',',
                                                        '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty



                    @endforelse

                </div>
            </div>
        </div>

        @endif

    </div>
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
@endsection
