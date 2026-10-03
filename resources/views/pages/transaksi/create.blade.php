@extends('layouts.app')

@section('content')

<main class="mt-3">
    <div class="card bg-transparent-50 overflow-hidden mb-3">
        <div class="card-header position-relative">
            <div class="bg-holder d-none d-md-block bg-card z-index-1"
                style="background-image:url(../falcon/assets/img/illustrations/ecommerce-bg.png);background-size:170px;background-position:right bottom;z-index:-1;">
            </div>
            <div class="position-relative z-index-2">
                <div class="row">
                    <div class="col-8">
                        {{-- <h3 class="text-primary mb-1">Selamat Datang Kembali, {{ Auth::user()->nama_panggilan }}!
                        </h3> --}}
                        <h3 class="text-primary mb-1 mt-3">Transaksi Beli Valas!</h3>

                        <p>Tambah Transaksi Hari Ini {{ $today }}</p>
                        <h6 class="text-primary">Nomor Order: #{{ $kode_transaksi }}</h6>
                        <hr>

                    </div>
                    <div class="col-4">
                        <div class="d-flex py-3">
                            <div class="pe-3 mt-3">
                                @if ($modal == '')
                                <h6 class="text-600 fs--1 fw-medium">Anda Belum Menambahkan Modal Hari Ini</h6>
                                @else
                                <h6 class="text-600 fs--1 fw-medium">Modal Anda Hari Ini</h6>
                                @endif

                                <h4 class="text-primary jumlah_modal mb-2" id="jumlah_modal"
                                    data-countup="jumlah_modal"
                                    data-base-modal="{{ !empty($modal) ? (float)$modal->riwayat_modal : 0 }}"
                                    data-sisa-modal="{{ !empty($modal) ? (float)$modal->riwayat_modal : 0 }}">
                                    @if ($modal == '')

                                    @else
                                    Rp. {{ number_format($modal->riwayat_modal, 0, ',', '.') }}
                                    @endif

                                </h4>
                                <a class="fw-semi-bold fs--1 text-nowrap" href="{{ route('modal.index') }}">Tambah Modal
                                    <span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('transaksi.store') }}" id="form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gx-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body p-4">
                        <i class="mb-3">Lengkapi data Customer dibawah Ini</i>

                        @if (Auth::user()->role === 'Owner')
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="transaction_cabang_id">Cabang</label>
                            <select class="form-select" id="transaction_cabang_id" name="cabang_id" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->cabang_id }}" {{ (string) $selectedCabangId === (string) $cabang->cabang_id ? 'selected' : '' }}>{{ $cabang->cabang_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="cabang_id" value="{{ $selectedCabangId }}">
                        @endif

                        {{-- Dropdown customer (langsung memuat semua customer cabang) --}}
                        <div class="mb-2 customer-picker">
                            <label class="form-label" for="customerSelect">Customer</label>
                            <div class="input-group">
                                <select class="form-select js-choice" id="customerSelect" size="1"
                                    data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false,"searchPlaceholderValue":"Cari nama, alias, passport, NIK, atau negara","noResultsText":"Customer tidak ditemukan"}'>
                                    <option value="">Pilih Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->name }}{{ $customer->alias ? ' - ' . $customer->alias : '' }}{{ $customer->passport ? ' [' . $customer->passport . ']' : '' }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary customer-add-button" type="button" id="addCustomerButton" title="Tambah customer manual">+</button>
                            </div>
                            <small class="text-muted">Seluruh customer aktif pada cabang terpilih ditampilkan.</small>
                        </div>

                        {{-- Tombol untuk kembali ke pencarian (awalnya tersembunyi) --}}
                        <div class="mb-3" id="toggleCustomerForm" style="display: none;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="backToSearchButton">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Pencarian Customer
                            </button>
                        </div>

                        {{-- Form manual untuk customer baru (awalnya tersembunyi) --}}
                        <div id="customerForm" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label" for="nama_customer_input">Nama Customer <span class="text-danger">*</span></label>
                                <input class="form-control form-select-sm @error('nama_customer') is-invalid @enderror"
                                    name="nama_customer" id="nama_customer_input" type="text" placeholder="Input Nama Customer"
                                    value="{{ old('nama_customer') }}" />
                                @error('nama_customer')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="nomor_passport">Nomor Passport</label>
                                <input class="form-control form-select-sm @error('nomor_passport') is-invalid @enderror"
                                    name="nomor_passport" id="nomor_passport" type="text" placeholder="Input Nomor Passport"
                                    value="{{ old('nomor_passport') }}" />
                                @error('nomor_passport')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="asal_negara_select">Asal Negara</label>
                                <select class="form-select form-select-sm @error('asal_negara') is-invalid @enderror"
                                    name="asal_negara" id="asal_negara_select">
                                    <option value="">Pilih Asal Negara</option>
                                    @foreach ($countries as $code => $country)
                                        <option value="{{ $country }}" {{ old('asal_negara') === $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asal_negara')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <button class="btn btn-warning btn-sm" type="button" id="validateTerdugaButton">
                                    <i class="fas fa-search me-1"></i>Validate Terduga
                                </button>
                                <div id="validationResult" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>

                        {{-- Input hidden untuk kompatibilitas dengan sistem lama --}}
                        <input type="hidden" name="customer_id" id="customer_id">
                        <input type="hidden" name="nama_customer" id="nama_customer_hidden" value="{{ old('nama_customer') }}">
                        <input type="hidden" name="customer_alias" id="customer_alias_hidden">
                        <input type="hidden" name="screening_confirmed" id="screening_confirmed" value="0">
                    </div>

                </div>

            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                        <h5 class="mb-0">Order Summary</h5> <br>
                        <a class="btn btn-falcon-default btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#modaltambah">
                            <span class="fas fa-plus me-2" data-fa-transform="shrink-2"></span>Tambah
                            Transaksi</a>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="kode_transaksi" value="{{ $kode_transaksi }}">
                        <input type="hidden" name="tanggal_transaksi" value="{{ $today_format }}">
                        <input type="hidden" name="id_modal" value="{{ $modal->id_modal }}">
                        <input type="hidden" name="id_transaksi" value="{{ $idbaru }}">
                        <div class="table-responsive scrollbar">
                            <table class="table table-hover table-striped overflow-hidden" id="dataTableKonfirmasi">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Currency</th>
                                        <th>Harga Currency</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="konfirmasi" style="font-size: 15px!important">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="card-footer d-flex justify-content-between bg-light">
                        <div class="fs-1 fw-semi-bold">Payable Total</div>
                        <div class="fs-1 fw-bold payable_total" id="payable_total">Rp. 0.0</div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Confirm Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 col-xl-12 col-xxl-7 px-md-3 mb-xxl-0 position-relative">
                            <div class="d-flex"><img class="me-3" src="../falcon/assets/img/icons/shield.png" alt=""
                                    width="60" height="60">
                                <div class="flex-1">
                                    <h5 class="mb-1">Mohon di lakukan pengecekan kembali</h5>
                                    <p class="fs--1 mb-0">Pastikan transaksi telah sesuai dan cek kembali total
                                        transaksi
                                    </p>
                                    <div class="fs-4 mt-2 fw-semi-bold">All Total: <span class="text-primary">
                                            <span class="grand_total" id="grand_total">Rp. 0.0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="vertical-line d-none d-md-block d-xl-none d-xxl-block"> </div>
                        </div>
                        <div
                            class="col-md-5 col-xl-12 col-xxl-5 ps-lg-4 ps-xl-2 ps-xxl-5 text-center text-md-start text-xl-center text-xxl-start">
                            <div class="border-dashed-bottom d-block d-md-none d-xl-block d-xxl-none my-4"></div>

                            <button class="btn btn-success mt-3 px-5 py-3" onclick="submitdata(event)"
                                id="button_submit" type="button">Confirm &amp; Pay
                            </button>
                            {{-- <p class="fs--1 mt-3 mb-0">By clicking <strong>Confirm &amp; Pay </strong>button,
                                transaction being process --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<div class="modal fade" id="customerCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="customerCreateForm">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Tambah Customer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input class="form-control" name="name" placeholder="Input nama customer" required></div>
                        <div class="col-md-6"><label class="form-label">Alias</label><input class="form-control" name="alias" placeholder="Input alias customer"></div>
                        <div class="col-md-6"><label class="form-label">Country <span class="text-danger">*</span></label><input class="form-control" name="country" placeholder="Input negara asal" required></div>
                        <div class="col-md-6"><label class="form-label">Passport</label><input class="form-control" name="passport" placeholder="Input nomor passport"></div>
                        <div class="col-md-6"><label class="form-label">Pekerjaan</label><input class="form-control" name="pekerjaan" placeholder="Input pekerjaan"></div>
                        <div class="col-md-6"><label class="form-label">NIK</label><input class="form-control" name="nik" placeholder="Input NIK"></div>
                        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" placeholder="Input alamat customer"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="passportDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="passportDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dokumen Pendukung Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-warning">Akumulasi transaksi passport ini melewati batas 30 hari. Lengkapi dokumen pendukung untuk melanjutkan.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" name="supporting_document_type" placeholder="Contoh: Form A atau Surat Pernyataan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" name="supporting_document_number" placeholder="Input nomor dokumen" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="supporting_document_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">File Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="supporting_document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF/JPG/PNG, maksimal 10 MB.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="supporting_document_note" placeholder="Input keterangan dokumen pendukung" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="passportDocumentSubmit">Lanjutkan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modaltambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                    <h4 class="mb-0 text-white" id="authentication-modal-label">Detail Transaksi</h4>
                    <p class="fs--1 mb-0 text-white">Tambah detail transaksi untuk melengkapi Order</p>
                </div><button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2"
                    id="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-currency-store') }}" id="form1" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="border-dashed-bottom mb-2"></div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="currency">Pilih Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <select class="form-select js-choice currency-select" id="currency" size="1"
                                    name="id_currency"
                                    data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false}'>
                                    <option value="">Pilih Kurs Terlebih Dahulu</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}, {{
                                        $item->jenis_kurs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_currency">Nilai Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <div class="input-group"><span class="input-group-text">Rp. </span>
                                <input class="form-control jumlah_currency" id="jumlah_currency" name="jumlah_currency"
                                    type="number" step="any" min="0" placeholder="Input Harga Currency"
                                    value="{{ old('jumlah_currency') }}" readonly />
                            </div>
                            <p class="fs--1"> <b>Ket:</b> Nilai kurs akan otomatis terisi setelah memilih Jenis Kurs</p>

                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="jumlah_tukar">Jumlah Penukaran</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control" id="jumlah_tukar" name="jumlah_tukar" type="number" step="any" min="0.0001"
                                placeholder="Input Jumlah Penukaran" value="{{ old('jumlah_tukar') }}" required />
                        </div>
                        <p class="text-primary fs--1"> Calculate (IDR):
                            <span id="detailjumlahcurrency" class="detailjumlahcurrency">

                            </span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="button" onclick="tambahdata(event)">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<template id="template_delete_button">
    <button class="btn p-0" onclick="hapusdata(this)" type="button"><span class="text-700 fas fa-trash-alt"></span>
    </button>
</template>

<template id="template_add_button">
    <button class="btn btn-success btn-datatable" type="button" data-toggle="modal" data-target="#Modaltambah">
        <i class="fas fa-plus"></i>
    </button>
</template>

<style>
    .customer-picker {
        align-items: stretch;
    }

    .customer-picker .choices {
        flex: 1 1 auto;
        min-width: 0;
        margin-bottom: 0;
    }

    .customer-picker .choices__inner {
        min-height: 38px;
        padding: 6px 10px;
        font-size: .85rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .customer-picker .choices__list--single {
        padding: 2px 16px 2px 0;
    }

    .customer-picker .choices__list--dropdown {
        z-index: 1050;
    }

    .customer-add-button {
        align-self: stretch;
        width: 38px;
        height: 38px;
        padding: 0;
        font-size: .85rem;
        line-height: 1;
    }
</style>

<script>
    var pendingTransactionData = null;
    var initialModalAmount = @json($modal ? (float) $modal->riwayat_modal : 0);

    function submitTransaction(data, documentData) {
        var payload = new FormData();
        Object.keys(data).forEach(function (key) {
            if (key !== 'detail') payload.append(key, data[key] == null ? '' : data[key]);
        });
        data.detail.forEach(function (detail, index) {
            Object.keys(detail).forEach(function (key) {
                payload.append('detail[' + index + '][' + key + ']', detail[key]);
            });
        });
        if (documentData) {
            documentData.forEach(function (value, key) {
                payload.append(key, value);
            });
        }

        $('#button_submit, #passportDocumentSubmit').prop('disabled', true);
        $.ajax({
            method: 'post',
            url: '{{ route('transaksi.store') }}',
            data: payload,
            processData: false,
            contentType: false,
            success: function (response) {
                window.location.href = '/transaksi/create';
                window.open('/cetak/' + response.id_transaksi, '_blank');
            },
            error: function (response) {
                $('#button_submit, #passportDocumentSubmit').prop('disabled', false);
                var errorData = response.responseJSON || {};
                if (response.status === 422 && errorData.requires_screening_confirmation && pendingTransactionData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Terduga Terdeteksi!',
                        text: 'Customer terdeteksi sebagai terduga. Ingin simpan transaksi?',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal'
                    }).then(function (answer) {
                        if (answer.isConfirmed) {
                            pendingTransactionData.screening_confirmed = '1';
                            submitTransaction(pendingTransactionData, documentData);
                        } else {
                            resetTransactionForm('Transaksi dibatalkan karena terduga terdeteksi.');
                        }
                    });
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi tidak dapat disimpan',
                    text: errorData.message || 'Terjadi kesalahan saat menyimpan transaksi.'
                });
                if (response.status === 422 && pendingTransactionData && errorData.requires_supporting_document) {
                    $('#passportDocumentModal').modal('show');
                }
            }
        });
    }

    function formatCurrencyIdr(val) {
        var num = parseFloat(val) || 0;
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: num % 1 !== 0 ? 2 : 0,
            maximumFractionDigits: 2
        }).format(num);
    }

    function formatNumberDec(val, maxDec) {
        var num = parseFloat(val) || 0;
        maxDec = maxDec || 4;
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: num % 1 !== 0 ? 2 : 0,
            maximumFractionDigits: maxDec
        }).format(num);
    }

    function recalculateTotals() {
        var grandTotal = 0;
        var detail = $('#konfirmasi').children();
        for (let index = 0; index < detail.length; index++) {
            var row = $(detail[index]);
            var totalSpan = row.find('.val-total');
            var valTotal = 0;
            if (totalSpan.length && totalSpan.data('total') !== undefined) {
                valTotal = parseFloat(totalSpan.data('total'));
            } else {
                var rawText = row.children().eq(4).text().replace(/[^\d,-]/g, '').replace(',', '.');
                valTotal = parseFloat(rawText) || 0;
            }
            grandTotal += valTotal;
        }
        grandTotal = Math.round(grandTotal * 100) / 100;

        $('#grand_total').data('raw-total', grandTotal).html(formatCurrencyIdr(grandTotal));

        var baseModal = parseFloat($('#jumlah_modal').data('base-modal') !== undefined ? $('#jumlah_modal').data('base-modal') : initialModalAmount);
        var sisaModal = Math.round((baseModal - grandTotal) * 100) / 100;
        $('#jumlah_modal').data('sisa-modal', sisaModal).html(formatCurrencyIdr(sisaModal));

        return {
            grandTotal: grandTotal,
            sisaModal: sisaModal
        };
    }

    function submitdata(event) {
        event.preventDefault();
        var form = $('#form');
        var _token = form.find('input[name="_token"]').val();
        var kode_transaksi = form.find('input[name="kode_transaksi"]').val();
        var tanggal_transaksi = form.find('input[name="tanggal_transaksi"]').val();
        var id_transaksi = form.find('input[name="id_transaksi"]').val();
        var id_modal = form.find('input[name="id_modal"]').val();
        var dataform2 = [];
        var nama_customer = form.find('input[name="nama_customer"]').val();
        var customer_alias = form.find('input[name="customer_alias"]').val();
        var nomor_passport = form.find('input[name="nomor_passport"]').val();
        var asal_negara = form.find('select[name="asal_negara"]').val();

        var detail = $('#konfirmasi').children();
        for (let index = 0; index < detail.length; index++) {
            var row = $(detail[index]);
            var spanCurrency = row.find('span[id]');
            var id_currency = spanCurrency.attr('id') || spanCurrency.data('currency-id');

            var valKurs = row.find('.val-kurs').data('kurs');
            if (valKurs === undefined) {
                valKurs = parseFloat(row.children().eq(2).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            var valJumlah = row.find('.val-jumlah').data('jumlah');
            if (valJumlah === undefined) {
                valJumlah = parseFloat(row.children().eq(3).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            var valTotal = row.find('.val-total').data('total');
            if (valTotal === undefined) {
                valTotal = parseFloat(row.children().eq(4).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            if (id_currency) {
                dataform2.push({
                    currency_id: id_currency,
                    id_transaksi: id_transaksi,
                    jumlah_currency: valKurs,
                    jumlah_tukar: valJumlah,
                    total_tukar: valTotal
                });
            }
        }

        if (dataform2.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Transaksi Kosong! Tambah Transaksi Terlebih Dahulu',
            });
            return;
        }

        var totals = recalculateTotals();
        var total = totals.grandTotal;
        var jumlah_modal = totals.sisaModal;

        var data = {
            _token: _token,
            customer_id: $('#customer_id').val(),
            cabang_id: form.find('select[name="cabang_id"], input[name="cabang_id"]').val(),
            screening_confirmed: $('#screening_confirmed').val(),
            kode_transaksi: kode_transaksi,
            tanggal_transaksi: tanggal_transaksi,
            id_modal: id_modal,
            total: total,
            jumlah_modal: jumlah_modal,
            nama_customer: nama_customer,
            customer_alias: customer_alias,
            nomor_passport: nomor_passport,
            asal_negara: asal_negara,
            detail: dataform2
        };

        pendingTransactionData = data;
        $('#button_submit').prop('disabled', true);
        $.post('{{ route('api.transaksi.passport-threshold') }}', {
            _token: _token,
            nomor_passport: nomor_passport,
            total: total,
            tanggal_transaksi: tanggal_transaksi
        }).done(function (result) {
            if (!result.exceeded) {
                submitTransaction(data, null);
                return;
            }
            $('#passportDocumentModal').modal('show');
        }).fail(function (response) {
            $('#button_submit').prop('disabled', false);
            const message = response.responseJSON && response.responseJSON.message;
            Swal.fire('Gagal', message || 'Validasi batas passport gagal.', 'error');
        });
    }

    function tambahdata(event, id_sparepart) {
        var form = $('#form1');
        var currencySelect = $('#currency');
        var id_currency = currencySelect.val();
        var currencyName = currencySelect.find('option:selected').text() || currencySelect.text();
        var jumlah_currency = parseFloat(form.find('input[name="jumlah_currency"]').val());
        var jumlah_tukar = parseFloat(form.find('input[name="jumlah_tukar"]').val());

        if (!id_currency || currencyName === "" || currencyName === "Pilih Kurs Terlebih Dahulu") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Currency Tidak Boleh Kosong!',
            });
            return;
        }

        var isDuplicate = false;
        var detailRows = $('#konfirmasi').children();
        for (let index = 0; index < detailRows.length; index++) {
            var span_asu = $(detailRows[index]).find('span[id]');
            if (span_asu.attr('id') == id_currency) {
                isDuplicate = true;
                break;
            }
        }
        if (isDuplicate) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Currency Tersebut Sudah Ada, Hapus Dahulu jika ingin menambahkan!',
            });
            return;
        }

        if (isNaN(jumlah_currency) || jumlah_currency <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga Currency Tidak Boleh Bernilai 0 atau Kosong!',
            });
            return;
        }

        if (isNaN(jumlah_tukar) || jumlah_tukar <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Jumlah Tukar Tidak Boleh Bernilai 0 atau Kosong!',
            });
            return;
        }

        var total_tukar = Math.round((jumlah_tukar * jumlah_currency) * 100) / 100;

        var baseModal = parseFloat($('#jumlah_modal').data('base-modal') !== undefined ? $('#jumlah_modal').data('base-modal') : initialModalAmount);
        var currentGrandTotal = parseFloat($('#grand_total').data('raw-total')) || 0;
        var projectedTotal = Math.round((currentGrandTotal + total_tukar) * 100) / 100;

        if (projectedTotal > baseModal) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon Maaf Modal Anda Kurang Dari Transaksi, Lakukan Penambahan!',
            });
            return;
        }

        var harga_currency_display = `<span class="val-kurs" data-kurs="${jumlah_currency}">${formatCurrencyIdr(jumlah_currency)}</span>`;
        var jumlah_tukar_display = `<span class="val-jumlah" data-jumlah="${jumlah_tukar}">${formatNumberDec(jumlah_tukar)}</span>`;
        var total_tukar_display = `<span class="val-total" data-total="${total_tukar}">${formatCurrencyIdr(total_tukar)}</span>`;

        var table = $('#dataTableKonfirmasi').DataTable();
        table.row.add([
            total_tukar_display,
            `<span id="${id_currency}" data-currency-id="${id_currency}">${currencyName}</span>`,
            harga_currency_display,
            jumlah_tukar_display,
            total_tukar_display,
            total_tukar_display
        ]).draw();

        recalculateTotals();

        // Close and reset modal
        $('#btn-close-modal').click();
        $('#form1')[0].reset();
        $('#detailjumlahcurrency').html(formatCurrencyIdr(0));

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: 'success',
            title: 'Berhasil Menambahkan Data Transaksi'
        });
    }

    function hapusdata(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var table = $('#dataTableKonfirmasi').DataTable();
                var row = $(element).closest('tr');
                table.row(row).remove().draw();
                recalculateTotals();
            }
        });
    }

    $(document).ready(function () {
        let customerChoicesInstance = null;
        let screeningConfirmed = false;
        let skipNextCustomerScreening = false;
        let clearingCustomer = false;
        const customersData = @json($customers->keyBy('customer_id'));

        function initCustomerChoices() {
            const el = document.getElementById('customerSelect');
            if (!el) return;
            if (el.closest('.choices')) {
                console.log('[CustomerSelect] sudah diinisialisasi oleh theme.js');
                return;
            }
            if (typeof Choices !== 'undefined') {
                try {
                    customerChoicesInstance = new Choices(el, {
                        removeItemButton: true,
                        placeholder: true,
                        shouldSort: false,
                        searchPlaceholderValue: 'Cari nama, alias, passport, NIK, atau negara',
                        noResultsText: 'Customer tidak ditemukan',
                        itemSelectText: ''
                    });
                    console.log('[CustomerSelect] diinisialisasi manual via Choices');
                } catch (err) {
                    console.error('[CustomerSelect] Gagal inisialisasi Choices:', err);
                }
            } else {
                console.warn('[CustomerSelect] Choices belum siap');
            }
        }

        initCustomerChoices();
        $(window).on('load', initCustomerChoices);

        function clearCustomer() {
            screeningConfirmed = false;
            $('#screening_confirmed').val('0');
            $('#customer_id, #nama_customer_hidden, #customer_alias_hidden, #nomor_passport, #asal_negara_select').val('');
            // Clear visible fields juga
            $('#nama_customer_input').val('');
            if (!clearingCustomer) {
                clearingCustomer = true;
                if (customerChoicesInstance) {
                    try {
                        customerChoicesInstance.removeActiveItems();
                    } catch (e) {
                        console.warn(e);
                    }
                } else {
                    const removeBtn = document.querySelector('.customer-picker .choices__button');
                    if (removeBtn) {
                        removeBtn.click();
                    } else {
                        $('#customerSelect').val('');
                    }
                }
                clearingCustomer = false;
            }
        }

        window.resetTransactionForm = function (message) {
            var table = $('#dataTableKonfirmasi').DataTable();
            table.clear().draw();
            $('#form1')[0].reset();
            clearCustomer();
            $('#customerForm').hide();
            $('.customer-picker').show();
            $('#addCustomerButton').show();
            $('#toggleCustomerForm').hide();
            $('#validationResult').empty().hide();
            $('#grand_total').html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(0));
            $('#jumlah_modal').html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(initialModalAmount));
            $('#detailjumlahcurrency').html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR'
            }).format(0));
            $('#passportDocumentModal').modal('hide');
            $('#passportDocumentForm')[0].reset();
            pendingTransactionData = null;
            $('#button_submit, #passportDocumentSubmit').prop('disabled', false);

            if (message) {
                Swal.fire({
                    icon: 'info',
                    title: 'Transaksi Dibatalkan',
                    text: message
                });
            }
        };

        function showScreeningPrompt(onContinue, onCancel) {
            Swal.fire({
                icon: 'warning',
                title: 'Terduga terdeteksi',
                text: 'Nama atau alias masuk Daftar Terorisme. Lanjutkan transaksi?',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal'
            }).then(function (answer) {
                if (answer.isConfirmed) return onContinue();
                clearCustomer();
                if (onCancel) onCancel();
            });
        }

        $('#transaction_cabang_id').on('change', function () {
            const cabangId = $(this).val();
            if (cabangId) window.location.href = '{{ route('transaksi.create') }}?cabang_id=' + encodeURIComponent(cabangId);
        });

        $('#customerSelect').on('change', function () {
            const selected = $(this).val();
            if (clearingCustomer) return;
            if (!selected) return clearCustomer();
            const item = customersData[selected];
            if (!item) return;

            const skipScreening = skipNextCustomerScreening;
            skipNextCustomerScreening = false;
            if (!skipScreening) {
                screeningConfirmed = false;
                $('#screening_confirmed').val('0');
            }
            $('#customer_id').val(item.customer_id);
            $('#nama_customer_hidden').val(item.name);
            $('#customer_alias_hidden').val(item.alias || '');
            $('#nomor_passport').val(item.passport || '');
            $('#asal_negara_select').val(item.country || '').trigger('change');
            if (!skipScreening) screenCustomer(item.name, item.alias || '');

            // Sembunyikan form manual jika memilih dari dropdown
            $('#customerForm').hide();
            $('#toggleCustomerForm').hide();
        });

        $('#addCustomerButton').on('click', function () {
            // Tampilkan form manual dan sembunyikan dropdown
            $('#customerForm').show();
            $('.customer-picker').hide();
            $(this).hide();
            $('#toggleCustomerForm').show();
        });

        function screenCustomer(name, alias) {
            $.post('{{ route('api.customer.screen') }}', {
                _token: '{{ csrf_token() }}',
                name: name,
                alias: alias
            }).done(function (result) {
                if (!result.matched) return;
                showScreeningPrompt(function () {
                    screeningConfirmed = true;
                    $('#screening_confirmed').val('1');
                });
            }).fail(function () {
                Swal.fire('Gagal', 'Screening customer gagal.', 'error');
                clearCustomer();
            });
        }

        $('#passportDocumentForm').on('submit', function (event) {
            event.preventDefault();
            if (!pendingTransactionData) return;
            submitTransaction(pendingTransactionData, new FormData(this));
        });

        $('#customerCreateForm').on('submit', function (event) {
            event.preventDefault();
            const form = $(this);
            const data = Object.fromEntries(new FormData(this).entries());
            const cabangId = $('#transaction_cabang_id').val() || $('input[name="cabang_id"]').val();
            if (cabangId) data.cabang_terdaftar = cabangId;
            $.post('{{ route('api.customer.screen') }}', data)
                .done(function (result) {
                    const save = function () {
                        $.ajax({
                            url: '{{ route('api.customer.store') }}',
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                const item = response.customer;
                                customersData[item.customer_id] = item;
                                const label = item.name + (item.alias ? ' - ' + item.alias : '') + (item.passport ? ' [' + item.passport + ']' : '');
                                if (customerChoicesInstance) {
                                    const newCustomerChoice = {
                                        value: String(item.customer_id),
                                        label: label,
                                        customProperties: item
                                    };
                                    customerChoicesInstance.setChoices([newCustomerChoice], 'value', 'label', false);
                                    skipNextCustomerScreening = true;
                                    customerChoicesInstance.setChoiceByValue(String(item.customer_id));
                                } else {
                                    $('#customerSelect').append(new Option(label, item.customer_id, true, true));
                                }
                                $('#customerCreateModal').modal('hide');
                                skipNextCustomerScreening = true;
                                $('#customerSelect').val(String(item.customer_id)).trigger('change');
                            },
                            error: function (response) {
                                const message = response.responseJSON && response.responseJSON.message;
                                Swal.fire('Gagal', message || 'Customer tidak dapat disimpan.', 'error');
                            }
                        });
                    };
                    if (!result.matched) return save();
                    showScreeningPrompt(function () {
                        screeningConfirmed = true;
                        $('#screening_confirmed').val('1');
                        save();
                    });
                })
                .fail(function () {
                    Swal.fire('Gagal', 'Screening customer gagal.', 'error');
                });
        });

        $('.jumlah_currency').each(function () {
            $(this).on('input', function () {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(harga)

                var jumlah = $(this).parent().parent().find('.detailjumlahcurrency')
                $(jumlah).html(harga_fix);
            })
        })
        // TES
        $('select[name="id_currency"]').on('change', function () {
            var id_currency = $(this).val();

            if (id_currency) {
                $.ajax({
                    url: 'getkurs/' + id_currency,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('input[name="jumlah_currency"]').val(data[0]);
                    },
                    error: function (response) {
                        console.log(response)
                    }
                });
            } else {
                $('input[name="jumlah_currency"]').empty();
            }
        });



        $('#jumlah_tukar').on('input', function () {
            var value = parseFloat($(this).val()) || 0;
            var nilai_kurs = parseFloat($('.jumlah_currency').val()) || 0;
            var calculate = Math.round((value * nilai_kurs) * 100) / 100;
            $('#detailjumlahcurrency').html(formatCurrencyIdr(calculate));
        });

        var template = $('#template_delete_button').html()
        $('#dataTableKonfirmasi').DataTable({
            "paging": false,
            "ordering": false,
            "info": false,
            "searching": false,
            "columnDefs": [{
                    "targets": -1,
                    "data": null,
                    "defaultContent": template
                },
                {
                    "targets": 0,
                    "data": null,
                    'render': function (data, type, row, meta) {
                        return meta.row + 1
                    }
                }
            ]
        });

        // Tombol kembali ke pencarian customer - menggunakan event delegation
        $(document).on('click', '#backToSearchButton', function() {
            $('#customerForm').hide();
            $('.customer-picker').show();
            $('#addCustomerButton').show();
            $('#toggleCustomerForm').hide();
            clearCustomer();
        });

        // Sync input visible dengan input hidden
        $('#nama_customer_input').on('input', function() {
            $('#nama_customer_hidden').val($(this).val());
            // Untuk kompatibilitas, isi customer_alias dengan nilai yang sama
            $('#customer_alias_hidden').val($(this).val());
        });

        // Negara dirender dari countries.json oleh controller dan dibuat searchable.
        if (typeof Choices !== 'undefined') {
            try {
                new Choices('#asal_negara_select', {
                    searchEnabled: true,
                    shouldSort: false,
                    itemSelectText: '',
                    noResultsText: 'Negara tidak ditemukan',
                    noChoicesText: 'Ketik untuk mencari negara'
                });
            } catch (e) {
                console.warn('Init asal_negara_select Choices failed:', e);
            }
        }

        // Validasi terduga (hanya dari nama, cek ke name dan alias di tabel)
        $('#validateTerdugaButton').on('click', function() {
            const nama = $('#nama_customer_input').val().trim();

            if (!nama) {
                Swal.fire('Peringatan', 'Nama customer harus diisi.', 'warning');
                return;
            }

            const button = $(this);
            const originalText = button.html();
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Validating...');

            $.ajax({
                url: '{{ route('api.transaksi.validate-terduga') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nama: nama,
                    alias: ''  // Tidak perlu alias input terpisah
                },
                success: function(response) {
                    const resultDiv = $('#validationResult');
                    resultDiv.show();

                    if (response.terduga) {
                        resultDiv.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terduga Terdeteksi!</strong><br>
                                ${response.message}<br>
                                <small>Term yang dicari: ${response.search_terms.join(', ')}</small>
                            </div>
                        `);
                        $('#screening_confirmed').val('0');
                        showScreeningPrompt(function () {
                            screeningConfirmed = true;
                            $('#screening_confirmed').val('1');
                        }, function () {
                            resetTransactionForm('Transaksi dibatalkan karena terduga terdeteksi.');
                        });
                    } else {
                        resultDiv.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Aman</strong><br>
                                ${response.message}<br>
                                <small>Term yang dicari: ${response.search_terms.join(', ')}</small>
                            </div>
                        `);
                        $('#screening_confirmed').val('1');
                        screeningConfirmed = true;
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat validasi.', 'error');
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        });

        // Set nilai awal dari hidden fields ke visible fields jika ada data old
        @if(old('nama_customer'))
            // Jika ada data old, tampilkan form manual
            $('#customerForm').show();
            $('.customer-picker').hide();
            $('#addCustomerButton').hide();
            $('#toggleCustomerForm').show();
            $('#nama_customer_input').val('{{ old('nama_customer') }}');
        @endif
    });
</script>

@endsection