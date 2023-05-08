@extends('layouts.app')

@section('content')
<main class="mt-3">
    <div class="card bg-light mb-3">
        <div class="card-body p-3">
            <p class="fs--1 mb-0"> <span class="fas fa-exchange-alt me-2" data-fa-transform="rotate-90"></span>
                Edit Data Transaksi Kode <strong>{{ $transaksi->kode_transaksi }}</strong> Pada Tanggal
                {{ $transaksi->tanggal_transaksi }} Oleh <strong>{{ Auth::user()->nama_panggilan }}</strong>
        </div>
    </div>
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <label class="form-label" for="nama_customer">Nama Customer</label>
                        <input class="form-control form-select-sm" id="nama_customer" name="nama_customer" type="text"
                            placeholder="Input Nama Customer" value="{{ $transaksi->nama_customer }}" />
                    </div>
                    <div class="col-4">
                        <label class="form-label" for="nomor_passport">Nomor Passport</label>
                        <input class="form-control form-select-sm " id="nomor_passport" name="nomor_passport"
                            type="number" placeholder="Input Nomor Passport" value="{{ $transaksi->nomor_passport }}" />
                    </div>
                    <div class="col-4">
                        <label class="form-label" for="asal_negara">Asal Negara</label>
                        <input class="form-control form-select-sm negara_asal" id="negara_asal" name="asal_negara"
                            type="text" placeholder="Input Asal Negara" value="{{ $transaksi->negara_asal }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2 g-3">
        <div class="col-xl-7 order-xl-1">
            <div class="card">
                <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                    <h5 class="mb-0">Order Summary</h5>
                    <a class="btn btn-falcon-default btn-sm" type="button" data-bs-toggle="modal"
                        data-bs-target="#modaltambah">
                        <span class="fas fa-plus me-2" data-fa-transform="shrink-2"></span>Tambah
                        Transaksi</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive scrollbar">
                        <table class="table table-hover table-striped overflow-hidden" id="dataTableKonfirmasi">
                            <thead>
                                <tr>
                                    <th class="small">No.</th>
                                    <th class="small">Currency</th>
                                    <th class="small">Harga Currency</th>
                                    <th class="small">Jumlah</th>
                                    <th class="small">Total</th>
                                    <th class="small">Action</th>
                                </tr>
                            </thead>
                            <tbody id="konfirmasi">
                                @forelse ($transaksi->detailTransaksi as $item)
                                <tr id="item-{{ $item->id_detail_transaksi }}" role="row" class="odd">
                                    <td></td>
                                    <td class="currency_edit"><span
                                            id="{{ $item->Currency->id_currency }}">{{ $item->Currency->nama_currency }}</span>
                                    </td>
                                    <td class="harga_currency_edit">IDR&nbsp;{{ number_format($item->jumlah_currency) }}
                                    </td>
                                    <td class="jumlah_edit">{{ $item->jumlah_tukar }}</td>
                                    <td class="total_edit">IDR&nbsp;{{ number_format($item->total_tukar)}}</td>
                                    <td>

                                    </td>
                                </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light">
                    <div class="fw-semi-bold">Payable Total</div>
                    <div class="fw-bold payable_total" id="payable_total">
                        IDR&nbsp;{{ number_format($transaksi->total) }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" id="form" method="POST"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="card mb-3">
                            <div class="bg-holder d-none d-lg-block bg-card"
                                style="background-image:url(../../falcon/assets/img/icons/spot-illustrations/corner-4.png);opacity: 0.7;">
                            </div>
                            <div class="card-body position-relative">
                                <h6>Order Code: #{{ $transaksi->kode_transaksi }}</h6>
                                <input type="hidden" name="id_modal" value="{{ $transaksi->Modal->id_modal }}">
                                <p class="fs--1">{{ $today }}</p>
                                <div><strong class="me-2">Status: </strong>
                                    <div class="badge rounded-pill badge-soft-info fs--2">On Progress
                                        <span class="fas fa-check ms-1" data-fa-transform="shrink-2"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card overflow-hidden" style="min-width: 12rem">
                            <div class="bg-holder bg-card"
                                style="background-image:url(../../falcon/assets/img/icons/spot-illustrations/corner-2.png);">
                            </div>
                            <div class="card-body position-relative">
                                <h6>Modal Anda Hari Ini</h6>
                                <h4 class="text-primary jumlah_modal" id="jumlah_modal" data-countup="jumlah_modal">
                                    IDR&nbsp;{{ number_format($modal->riwayat_modal) }}
                                </h4>
                                <a class="fw-semi-bold fs--1 text-nowrap" href="{{ route('modal.index') }}">Tambah Modal
                                    <span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Confirm Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 col-xl-12 col-xxl-7 px-md-3 mb-xxl-0 position-relative">
                                <div class="d-flex"><img class="me-3" src="../../falcon/assets/img/icons/shield.png"
                                        alt="" width="60" height="60">
                                    <div class="flex-1">
                                        <h5 class="mb-2">Check Kembali</h5>
                                        <div class="form-check mb-0"><input class="form-check-input" id="check_1"
                                                type="checkbox"><label class="form-check-label mb-0">Check Kembali Order
                                                Summary<br class="d-none d-md-block d-lg-none"></label></div>
                                        <div class="form-check mb-0"><input class="form-check-input" id="check_2"
                                                type="checkbox"><label class="form-check-label mb-0">Check All Total
                                                Payment<br class="d-none d-md-block d-lg-none"></label></div>
                                        <p class="fs--1 mb-0">Pastikan transaksi telah sesuai, centang untuk melanjutkan
                                        </p>
                                        </a>
                                    </div>
                                </div>
                                <div class="vertical-line d-none d-md-block d-xl-none d-xxl-block"> </div>
                            </div>
                            <div
                                class="col-md-5 col-xl-12 col-xxl-5 ps-lg-4 ps-xl-2 ps-xxl-5 text-center text-md-start text-xl-center text-xxl-start">
                                <div class="border-dashed-bottom d-block d-md-none d-xl-block d-xxl-none my-4"></div>
                                <div class="fs-2 fw-semi-bold">All Total: <span class="text-primary">
                                        <span class="grand_total" id="grand_total">IDR&nbsp;
                                            {{ number_format($transaksi->total) }}</span>
                                </div>
                                <button class="btn btn-primary mt-3 px-4" data-bs-toggle="modal"
                                    data-bs-target="#error-modal" type="button">Confirm Data
                                </button>
                                <p class="fs--1 mt-3 mb-0">By clicking <strong>Confirm &amp; Edit </strong>button,
                                    transaction being process
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<div class="modal fade" id="error-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                    <h5 class="mb-1" id="modalExampleDemoLabel">Keterangan</h3>
                </div>
                <div class="p-4 pb-0">
                    <form>
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="mb-3">
                            <label class="col-form-label" for="keterangan">Keterangan:</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <textarea class="form-control" id="keterangan" rows="5"></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" type="button" id="btnSubmit"
                    onclick="submitdata(event, {{ $transaksi->id_transaksi }})">Kirim dan Update </button>
            </div>
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
                                <select class="form-select js-choice" id="currency" size="1" name="id_currency"
                                    data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false}'>
                                    <option value="">Pilih Kurs Terlebih Dahulu</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }},
                                        {{ $item->jenis_kurs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_currency">Nilai Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <div class="input-group"><span class="input-group-text">Rp. </span>
                                <input class="form-control jumlah_currency" id="jumlah_currency" name="jumlah_currency"
                                    type="number" min="1000" placeholder="Input Harga Currency"
                                    value="{{ old('jumlah_currency') }}" readonly />
                            </div>
                            <p class="fs--1"> <b>Ket:</b> Nilai kurs akan otomatis terisi setelah memilih Jenis Kurs</p>
                        </div>
                        <input type="hidden" value="{{ $transaksi->id_transaksi }}" id="transaksi_id">
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="jumlah_tukar">Jumlah Penukaran</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control" id="jumlah_tukar" name="jumlah_tukar" type="number" min="1"
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

<script>
    function submitdata(event, id_transaksi) {
        event.preventDefault()
        var form = $('#form')
        var _token = form.find('input[name="_token"]').val()
        var id_modal = form.find('input[name="id_modal"]').val()
        var keterangan = $('#keterangan').val()
        var dataform2 = []
        var grand_total = $('#grand_total').html()
        var nama_customer = $('#nama_customer').val()
        var nomor_passport = $('#nomor_passport').val()
        var negara = $('.negara_asal').val()
        console.log(negara)


        if (grand_total.includes("IDR&nbsp;0")) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Transaksi Kosong! Tambah Transaksi Terlebih Dahulu',
            })
        } else {
            var total = grand_total.split('IDR&nbsp;')[1].replace(',', '').replace(',', '').trim()
            var check_1 = $('#check_1').is(":checked")
            var check_2 = $('#check_2').is(":checked")

            var modal = $('#jumlah_modal').html()
            var jumlah_modal = modal.split('IDR&nbsp;')[1].replace(',', '').replace(',', '').trim()

            if (check_1 == false) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Check Terlebih Dahulu!',
                })
            } else if (check_2 == false) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Check Terlebih Dahulu!',
                })
            } else if (check_1 == false && check_2 == false) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Check Terlebih Dahulu!',
                })
            } else if (check_1 == true && check_2 == true) {

                var detail = $('#konfirmasi').children()
                for (let index = 0; index < detail.length; index++) {
                    var children = $(detail[index]).children()

                    var td_currency = children[1]
                    var span = $(td_currency).children()[0]
                    var id_currency = $(span).attr('id')

                    var td_jumlah_currency = children[2]
                    var jumlah_currency_trim = $(td_jumlah_currency).html()
                    var tes = jumlah_currency_trim.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                        .trim()
                    var jumlah_currency = parseFloat(tes).toFixed(0)


                    var td_jumlah_tukar = children[3]
                    var jumlah_tukar = $(td_jumlah_tukar).html()

                    var total_tukar = children[4]
                    var total_tukar_trim = $(total_tukar).html()
                    var total_tukar = total_tukar_trim.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                        .trim()

                    dataform2.push({
                        currency_id: id_currency,
                        id_transaksi: id_transaksi,
                        jumlah_currency: jumlah_currency,
                        jumlah_tukar: jumlah_tukar,
                        total_tukar: total_tukar,
                    })
                }

                if (dataform2.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Transaksi Kosong!, Isi Transaksi Terlebih Dahulu',
                    })
                } else {
                    var data = {
                        _token: _token,
                        id_modal: id_modal,
                        total: total,
                        keterangan: keterangan,
                        jumlah_modal: jumlah_modal,
                        nama_customer: nama_customer,
                        nomor_passport: nomor_passport,
                        asal_negara: negara,
                        detail: dataform2
                    }

                    console.log(data)

                    $.ajax({
                        method: 'put',
                        url: '/transaksi/' + id_transaksi,
                        data: data,
                        beforeSend: function () {
                            $('#btnSubmit').prop('disabled', true);
                        },
                        success: function (response) {
                            window.location.href = '/transaksi'
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'success',
                                title: 'Data Masih Diproses Mohon Tunggu'
                            })
                        },
                        error: function (response) {
                            console.log(response)
                            $('#btnSubmit').prop('disabled', false);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Error! Transaksi Tidak dapat disimpan',
                            })
                        },
                        complete: function () {
                            $('#btnSubmit').prop('disabled', false);
                        }
                    });
                }
            }
        }
    }

    function tambahdata(event, id_sparepart) {
        var form = $('#form1')
        var currency = $('#currency').html()
        var currency_2 = $('#currency').text()
        var id_currency = $('#currency').val()
        var jumlah_currency = form.find('input[name="jumlah_currency"]').val()
        var jumlah_tukar = form.find('input[name="jumlah_tukar"]').val()
        var total_tukar = jumlah_tukar * jumlah_currency;

        var harga_currency = new Intl.NumberFormat('locale', {
            style: 'currency',
            currency: 'IDR',
            separator: ',',
            minimumFractionDigits: 0,
        }).format(jumlah_currency)

        var total_tukar_rp = new Intl.NumberFormat('locale', {
            style: 'currency',
            currency: 'IDR',
            type: 'group',
            minimumFractionDigits: 0,
        }).format(jumlah_tukar * jumlah_currency)

        if (currency == "" | currency == "Pilih Currency") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Currency Tidak Boleh Kosong!',
            })
        } else if (jumlah_currency == "" | jumlah_currency == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga Currency Tidak Boleh Bernilai 0 atau Kosong!',
            })
        } else if (jumlah_tukar == "" | jumlah_tukar == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Jumlah Tukar Tidak Boleh Bernilai 0 atau Kosong!',
            })
        } else {
            // PENGURANGAN MODAL
            var jumlah_modal = $('#jumlah_modal').html()
            // console.log(jumlah_modal)
            var check_modal = jumlah_modal.includes("IDR&nbsp;")
            if (check_modal == true) {
                var jumlah_modal_trim = jumlah_modal.split('IDR&nbsp;')[1].replace(',', '').replace(',', '').trim()
                var jumlah_total_fix = parseInt(jumlah_modal_trim) - parseInt(total_tukar)
            } else {
                var jumlah_modal_trim = jumlah_modal.split('IDR')[1].replace(',', '').replace(',', '').trim()
                var jumlah_total_fix = parseInt(jumlah_modal_trim) - parseInt(total_tukar)
            }

            // JIKA TRANSAKSI LEBIH DARI MODAL
            if (total_tukar > jumlah_modal_trim) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Mohon Maaf Modal Anda Kurang Dari Transaksi, Lakukan Penambahan!',
                })
            } else {
                // PENGURANGAN MODAL
                var jumlah_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(jumlah_total_fix)
                $('#jumlah_modal').html(jumlah_total_idr)

                // PAYABLE TOTAL
                var payable_total = $('#payable_total').html()
                var check_payable = payable_total.includes("IDR&nbsp;");
                if (check_payable == true) {
                    var payable_total_trim = payable_total.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                        .trim()
                    var payable_total_fix = parseInt(payable_total_trim) + parseInt(total_tukar)
                } else {
                    var payable_total_trim = payable_total.split('Rp&nbsp;')[1].replace(',', '').replace(',', '').trim()
                    var payable_total_fix = parseInt(payable_total_trim) + parseInt(total_tukar)
                }
                var payable_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(payable_total_fix)
                $('#payable_total').html(payable_total_idr)

                // GRAND TOTAL
                var grand_total = $('#grand_total').html()
                var check_grand = grand_total.includes("IDR&nbsp;");
                if (check_grand == true) {
                    var grand_total_trim = grand_total.split('IDR&nbsp;')[1].replace(',', '').replace(',', '').trim()
                    var grand_total_fix = parseInt(grand_total_trim) + parseInt(total_tukar)
                } else {
                    var grand_total_trim = grand_total.split('Rp&nbsp;')[1].replace(',', '').replace(',', '').trim()
                    var grand_total_fix = parseInt(grand_total_trim) + parseInt(total_tukar)
                }
                var grand_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    type: 'group',
                    minimumFractionDigits: 0,
                }).format(grand_total_fix)
                $('#grand_total').html(grand_total_idr)

                // var detail = $('#konfirmasi').children()
                // for (let index = 0; index < detail.length; index++) {
                //     var children1 = $(detail[index]).children()

                //     var td_currency = children1[1]
                //     var span1 = $(td_currency).children()[0]
                //     var id_currency2 = $(span1).attr('id')
                //     var kurs_text = $(span1).text()

                //     var tod = $(detail[index]).parent()
                //     var tes1 = $(tod).children()[index]
                //     var tes2 = $(tes1).attr('id')
                //     var fixx = tes2.split('item-')[1]

                // } 

                // DRAW DATATABLE
                $('#dataTableKonfirmasi').DataTable().row.add([
                    total_tukar_rp, `<span id=${id_currency}>${currency}</span>`, harga_currency, jumlah_tukar,
                    total_tukar_rp, total_tukar_rp
                ]).draw();

                // CLOSE DAN RESET MODAL
                $('#btn-close-modal').click();
                $('#form1')[0].reset();
                var tes = 0;
                var tes_fix = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(tes)
                $('#detailjumlahcurrency').html(tes_fix)

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil Menambahkan Data Transaksi'
                })


            }



        }
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
                var table = $('#dataTableKonfirmasi').DataTable()
                var row = $(element).parent().parent()
                table.row(row).remove().draw();
                var table = $('#dataTable').DataTable()

                var jumlah = $(row.children()[4]).text()
                var check_jumlah = jumlah.includes("&nbsp;");
                if (check_jumlah == true) {
                    var jumlah_trim = jumlah.split('IDR&nbsp;')[1].replace(',', '').replace(',', '').trim()
                } else {
                    var jumlah_trim = jumlah.split('IDR')[1].replace(',', '').replace(',', '').trim()
                }

                // PAYABLE 
                var payable_total = $('#payable_total').html()
                var payable_total_trim = payable_total.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                    .trim()
                var payable_total_fix = parseInt(payable_total_trim) - parseInt(jumlah_trim)
                var payable_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    type: 'group',
                    minimumFractionDigits: 0,
                }).format(payable_total_fix)
                $('#payable_total').html(payable_total_idr)

                // GRAND TOTAL
                var grand_total = $('#grand_total').html()
                var grand_total_trim = grand_total.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                    .trim()
                var grand_total_fix = parseInt(grand_total_trim) - parseInt(jumlah_trim)
                var grand_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    type: 'group',
                    minimumFractionDigits: 0,
                }).format(grand_total_fix)
                $('#grand_total').html(grand_total_idr)

                // MODAL
                var jumlah_modal = $('#jumlah_modal').html()


                var jumlah_modal_trim = jumlah_modal.split('IDR&nbsp;')[1].replace(',', '').replace(',', '')
                    .trim()
                // console.log(jumlah_modal, jumlah_modal_trim)
                var jumlah_total_fix = parseInt(jumlah_modal_trim) + parseInt(jumlah_trim)
                var jumlah_total_idr = new Intl.NumberFormat('locale', {
                    style: 'currency',
                    currency: 'IDR',
                    type: 'group',
                    minimumFractionDigits: 0,
                }).format(jumlah_total_fix)
                $('#jumlah_modal').html(jumlah_total_idr)
            }
        })

    }

    $(document).ready(function () {
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

        $('select[name="id_currency"]').on('change', function () {
            var id_currency = $(this).val();
            var transaksi_id = $('#transaksi_id').val()

            if (id_currency) {
                $.ajax({
                    url: '/edit/getkurs/' + id_currency,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('input[name="jumlah_currency"]').val(data[0]);
                        // console.log(data[0])
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
            var value = $(this).val()
            var nilai_kurs = $('.jumlah_currency').val()
            var calculate = parseFloat(value) * parseFloat(nilai_kurs)
            // var hasil_calc = calculate.toFixed(2)

            var hasil_calc = new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(calculate);

            $('#detailjumlahcurrency').html(hasil_calc)
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
    });
</script>

@endsection