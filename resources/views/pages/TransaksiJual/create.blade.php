@extends('layouts.app')

@section('content')

<main class="mt-3">
    <div class="card bg-transparent-50 overflow-hidden mb-3">
        <div class="card-header position-relative">
            <div class="bg-holder d-none d-md-block bg-card z-index-1" style="background-image:url(../falcon/assets/img/illustrations/ecommerce-bg.png);background-size:170px;background-position:right bottom;z-index:-1;">
            </div>
            <div class="position-relative z-index-2">
                <div class="row">
                    <div class="col-8">
                        {{-- <h3 class="text-primary mb-1">Selamat Datang Kembali, {{ Auth::user()->nama_panggilan }}!
                        </h3> --}}
                        <h3 class="text-primary mb-1 mt-2">Transaksi Jual Valas!</h3>

                        <p>Tambah Transaksi Hari Ini {{ $today }}</p>
                        <h6 class="text-primary">Nomor Order: #{{ $kode_transaksi }}</h6>
                        <hr>

                    </div>
                    <div class="col-4">
                        <div class="d-flex py-3">
                            <div class="pe-3 mt-3">
                                <p>Modal Hari Ini setelah ditambah:</p>
                                <h4 class="text-primary jumlah_modal mt-2 mb-2" id="jumlah_modal" data-countup="jumlah_modal">
                                    @if ($modal == '')
                                    0
                                    @else
                                    Rp. {{ number_format($modal->riwayat_modal) }}
                                    @endif

                                </h4>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('transaksi-jual.store') }}" id="form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gx-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                        <h5 class="mb-0">Transaksi Jual</h5> <br>
                        <a class="btn btn-falcon-default btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modaltambah">
                            <span class="fas fa-plus me-2" data-fa-transform="shrink-2"></span>Tambah
                            Transaksi</a>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="kode_transaksi" value="{{ $kode_transaksi }}">
                        <input type="hidden" name="tanggal_transaksi" value="{{ $today_format }}">
                        {{-- <input type="hidden" name="id_modal" value="{{ $modal->id_modal }}"> --}}
                        <input type="hidden" name="id_transaksi" value="{{ $idbaru2 }}">
                        <div class="table-responsive scrollbar">
                            <table class="table table-hover table-striped overflow-hidden" id="dataTableKonfirmasi">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Currency</th>
                                        <th>Harga Jual</th>
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
        <div class="mt-3">
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Confirm Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 col-xl-12 col-xxl-7 px-md-3 mb-xxl-0 position-relative">
                            <div class="d-flex"><img class="me-3" src="../falcon/assets/img/icons/shield.png" alt="" width="60" height="60">
                                <div class="flex-1">
                                    <h5 class="mb-1">Mohon di lakukan pengecekan kembali</h5>
                                    <p class="fs--1 mb-0">Pastikan transaksi telah sesuai dan cek kembali total
                                        transaksi
                                    </p>
                                    <div class="fs-4 mt-2 fw-semi-bold">All Total: <span class="text-primary">
                                            <span class="grand_total" id="grand_total">Rp. 0.0</span>
                                    </div>

                                    </a>
                                </div>
                            </div>
                            <div class="vertical-line d-none d-md-block d-xl-none d-xxl-block"> </div>
                        </div>
                        <div class="col-md-5 col-xl-12 col-xxl-5 ps-lg-4 ps-xl-2 ps-xxl-5 text-center text-md-start text-xl-center text-xxl-start">
                            <div class="border-dashed-bottom d-block d-md-none d-xl-block d-xxl-none my-4"></div>

                            <button class="btn btn-success mt-3 px-5 py-3" onclick="submitdata(event)" id="button_submit" type="button">Confirm &amp; Pay
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

<div class="modal fade" id="modaltambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                    <h4 class="mb-0 text-white" id="authentication-modal-label">Tambah Jual Valas</h4>
                    <p class="fs--1 mb-0 text-white">Tambah detail transaksi untuk melengkapi Order</p>
                </div><button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" id="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-currency-store') }}" id="form1" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="border-dashed-bottom mb-2"></div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="currency">Pilih Kurs</label><span class="mr-4 mb-3" style="color: red">*</span>
                                <select class="form-select js-choice currency-select" id="currency" size="1" name="id_currency" data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false}'>
                                    <option value="">Pilih Kurs Terlebih Dahulu</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}, {{
                                        $item->jenis_kurs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3" style="display: none">
                            <label class="form-label" for="jumlah_actual">Jumlah Actual (Lembar)</label>
                            <input class="form-control" id="jumlah_actual" name="jumlah_actual" type="number" value="{{ old('jumlah_actual') }}" readonly />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_currency">Nilai Kurs</label><span class="mr-4 mb-3" style="color: red">*</span>
                            <div class="input-group"><span class="input-group-text">Rp. </span>
                                <input class="form-control jumlah_currency" id="jumlah_currency" name="jumlah_currency" type="number" placeholder="Input Nilai JUal Kurs" value="{{ old('jumlah_currency') }}" />
                            </div>

                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="jumlah_tukar">Jumlah Jual</label><span class="mr-4 mb-3" style="color: red">*</span>
                            <input class="form-control" id="jumlah_tukar" name="jumlah_tukar" type="number" min="1" placeholder="Input Jumlah Penukaran" value="{{ old('jumlah_tukar') }}" required />
                        </div>
                        <p class="text-primary fs--1"> Calculate (IDR):
                            <span id="detailjumlahcurrency" class="detailjumlahcurrency"></span>
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

<script>
    function submitdata(event) {
        event.preventDefault()
        var form = $('#form')
        var _token = form.find('input[name="_token"]').val()
        var kode_transaksi = form.find('input[name="kode_transaksi"]').val()
        var tanggal_transaksi = form.find('input[name="tanggal_transaksi"]').val()
        var id_transaksi = form.find('input[name="id_transaksi"]').val()
        // var id_modal = form.find('input[name="id_modal"]').val()
        var dataform2 = []
        var grand_total = $('#grand_total').html()
        var check_grand = grand_total.includes("Rp.");
        // var nama_customer = form.find('input[name="nama_customer"]').val()
        // var nomor_passport = form.find('input[name="nomor_passport"]').val()
        // var asal_negara = form.find('input[name="asal_negara"]').val()

        if (check_grand == true) {
            Swal.fire({
                icon: 'error'
                , title: 'Oops...'
                , text: 'Transaksi Kosong! Tambah Transaksi Terlebih Dahulu'
            , })
        } else {
            var total = grand_total.split('Rp&nbsp;')[1].replace('.', '').replace('.', '').trim()
            var modal = $('#jumlah_modal').html()
            var jumlah_modal = modal.split('Rp&nbsp;')[1].replace('.', '').replace('.', '').trim()
            var detail = $('#konfirmasi').children()
            for (let index = 0; index < detail.length; index++) {
                var children = $(detail[index]).children()

                var td_currency = children[1]
                var span = $(td_currency).children()[0]
                var id_currency = $(span).attr('id')

                var td_jumlah_currency = children[2]
                var jumlah_currency_trim = $(td_jumlah_currency).html()
                var jumlah_currency = jumlah_currency_trim.split('Rp&nbsp;')[1].replace(',', '.').replace('.', '')
                    .trim()

                var td_jumlah_tukar = children[3]
                var jumlah_tukar = $(td_jumlah_tukar).html()

                var total_tukar = children[4]
                var total_tukar_trim = $(total_tukar).html()
                var total_tukar = total_tukar_trim.split('Rp&nbsp;')[1].replace(',', '.').replace('.', '').replace(
                    '.', '').trim()

                dataform2.push({
                    currency_id: id_currency,
                    // id_transaksi: id_transaksi,
                    jumlah_currency: jumlah_currency
                    , jumlah_tukar: jumlah_tukar
                    , total_tukar: total_tukar
                });
            }


            if (dataform2.length == 0) {
                Swal.fire({
                    icon: 'error'
                    , title: 'Oops...'
                    , text: 'Transaksi Kosong!, Isi Transaksi Terlebih Dahulu'
                , })
            } else {
                var data = {
                    _token: _token
                    , kode_transaksi: kode_transaksi
                    , tanggal_transaksi: tanggal_transaksi
                    , total: total
                    , jumlah_modal: jumlah_modal
                    , detail: dataform2
                }

                $('#button_submit').prop('disabled', true);

                $.ajax({
                    method: 'post'
                    , url: '/transaksi-jual'
                    , data: data
                    , success: function(response) {
                        console.log(response)
                        window.location.href = '/transaksi-jual/create'
                        window.open(
                            '/cetak/' + response
                            , '_blank'
                        );

                        const Toast = Swal.mixin({
                            toast: true
                            , position: 'top-end'
                            , showConfirmButton: false
                            , timer: 3000
                            , timerProgressBar: true
                            , didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        Toast.fire({
                            icon: 'success'
                            , title: 'Data Masih Diproses Mohon Tunggu'
                        });
                    }
                    , error: function(response) {
                        console.log(response)
                        $('#button_submit').prop('disabled', false);
                        Swal.fire({
                            icon: 'error'
                            , title: 'Oops...'
                            , text: response.error
                        })
                    }
                });

            }
        }
    }

    function tambahdata(event, id_sparepart) {
        var form = $('#form1')
        var currency = $('#currency').html()
        var id_currency = $('#currency').val()
        var jumlah_currency = form.find('input[name="jumlah_currency"]').val()
        var jumlah_tukar = form.find('input[name="jumlah_tukar"]').val()
        var total_tukar = jumlah_tukar * jumlah_currency;
        var jumlah_actual = form.find('input[name="jumlah_actual"]').val();
        var harga_currency = new Intl.NumberFormat('id', {
            style: 'currency'
            , currency: 'IDR'
            , minimumFractionDigits: 0
        , }).format(jumlah_currency);
        var detail_asu = $('#konfirmasi').children()
        for (let index = 0; index < detail_asu.length; index++) {
            var children = $(detail_asu[index]).children()

            var td_currency_asu = children[1]
            var span_asu = $(td_currency_asu).children()[0]
            var id_asu = $(span_asu).attr('id')
        }

        var total_tukar_rp = new Intl.NumberFormat('id', {
            style: 'currency'
            , currency: 'IDR'
            , minimumFractionDigits: 0
        , }).format(jumlah_tukar * jumlah_currency)

        if (currency == "" | currency == "Pilih Currency") {
            Swal.fire({
                icon: 'error'
                , title: 'Oops...'
                , text: 'Currency Tidak Boleh Kosong!'
            , })
        } else if (id_asu == id_currency) {
            Swal.fire({
                icon: 'error'
                , title: 'Oops...'
                , text: 'Currency Tersebut Sudah Ada, Hapus Dahulu jika ingin menambahkan!'
            , })

        } else if (jumlah_currency == "" | jumlah_currency == 0) {
            Swal.fire({
                icon: 'error'
                , title: 'Oops...'
                , text: 'Harga Currency Tidak Boleh Bernilai 0 atau Kosong!'
            , })
        } else if (jumlah_tukar == "" | jumlah_tukar == 0) {
            Swal.fire({
                icon: 'error'
                , title: 'Oops...'
                , text: 'Jumlah Tukar Tidak Boleh Bernilai 0 atau Kosong!'
            , });
        // } else if (jumlah_actual == 0 || jumlah_actual == "") {
        //     Swal.fire({
        //         icon: 'error'
        //         , title: 'Oops...'
        //         , text: 'Jumlah Lembar Valas 0!'
        //     , });
        //     return;
        // } else if (parseInt(jumlah_tukar) > parseInt(jumlah_actual)) {
        //     Swal.fire({
        //         icon: 'error'
        //         , title: 'Oops...'
        //         , text: 'Jumlah Lembar Jual Tidak Boleh Lebih Besar!'
        //     , });
        //     return;
        } else {
            var jumlah_modal = $('#jumlah_modal').html()
            var check_modal = jumlah_modal.includes("Rp.")
            console.log(jumlah_modal)
            console.log(check_modal)
            if (check_modal == true) {
                var jumlah_modal_trim = jumlah_modal.split('Rp.')[1].replace(',', '').replace(',', '').trim()
                var jumlah_total_fix = parseInt(jumlah_modal_trim) + parseInt(total_tukar)
            } else {
                if (jumlah_modal == 0) {
                    var jumlah_modal_trim = 0
                } else {
                    var jumlah_modal_trim = jumlah_modal.split('Rp&nbsp;')[1].replace('.', '').replace('.', '').trim()
                }
                var jumlah_total_fix = parseInt(jumlah_modal_trim) + parseInt(total_tukar)
            }

            var jumlah_total_idr = new Intl.NumberFormat('id', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            , }).format(jumlah_total_fix)
            $('#jumlah_modal').html(jumlah_total_idr)

            // GRAND TOTAL
            var grand_total = $('#grand_total').html()
            var check_grand = grand_total.includes("Rp.");
            if (check_grand == true) {
                var grand_total_trim = grand_total.split('Rp')[1].replace('.', '').replace('.', '').trim()
                var grand_total_fix = parseInt(grand_total_trim) + parseInt(total_tukar)
            } else {
                var grand_total_trim = grand_total.split('Rp&nbsp;')[1].replace('.', '').replace('.', '').trim()
                var grand_total_fix = parseInt(grand_total_trim) + parseInt(total_tukar)
            }
            var grand_total_idr = new Intl.NumberFormat('id', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
            , }).format(grand_total_fix)
            $('#grand_total').html(grand_total_idr)

            var table = $('#dataTableKonfirmasi').DataTable()
            var row = $(`#${id_currency.trim()}`).parent().parent()
            table.row(row).remove().draw();

            // DRAW DATATABLE
            $('#dataTableKonfirmasi').DataTable().row.add([
                total_tukar_rp, `<span id=${id_currency}>${currency}</span>`, harga_currency, jumlah_tukar
                , total_tukar_rp, total_tukar_rp
            ]).draw();

            // CLOSE DAN RESET MODAL
            $('#btn-close-modal').click();
            $('#form1')[0].reset();
            var tes = 0;
            var tes_fix = new Intl.NumberFormat('id', {
                style: 'currency'
                , currency: 'IDR'
            }).format(tes)
            $('#detailjumlahcurrency').html(tes_fix)

            const Toast = Swal.mixin({
                toast: true
                , position: 'top-end'
                , showConfirmButton: false
                , timer: 3000
                , timerProgressBar: true
                , didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success'
                , title: 'Berhasil Menambahkan Data'
            })
        }
    }

    function hapusdata(element) {
        Swal.fire({
            title: 'Are you sure?'
            , text: "You won't be able to revert this!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#3085d6'
            , cancelButtonColor: '#d33'
            , confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var table = $('#dataTableKonfirmasi').DataTable()
                var row = $(element).parent().parent()
                table.row(row).remove().draw();
                var table = $('#dataTable').DataTable()

                var jumlah = $(row.children()[4]).text()
                var jumlah_trim = jumlah.split('Rp')[1].replace('.', '').replace('.', '').trim()

                // // PAYABLE
                // var payable_total = $('#payable_total').html()
                // var payable_total_trim = payable_total.split('Rp&nbsp;')[1].replace('.', '').replace('.', '')
                //     .trim()
                // var payable_total_fix = parseInt(payable_total_trim) - parseInt(jumlah_trim)
                // var payable_total_idr = new Intl.NumberFormat('id', {
                //     style: 'currency',
                //     currency: 'IDR',
                //     minimumFractionDigits: 0,
                // }).format(payable_total_fix)
                // $('#payable_total').html(payable_total_idr)

                // GRAND TOTAL
                var grand_total = $('#grand_total').html()
                var grand_total_trim = grand_total.split('Rp&nbsp;')[1].replace('.', '').replace('.', '').trim()
                var grand_total_fix = parseInt(grand_total_trim) - parseInt(jumlah_trim)
                var grand_total_idr = new Intl.NumberFormat('id', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                , }).format(grand_total_fix)
                $('#grand_total').html(grand_total_idr)

                // MODAL
                var jumlah_modal = $('#jumlah_modal').html()
                var jumlah_modal_trim = jumlah_modal.split('Rp&nbsp;')[1].replace('.', '').replace('.', '')
                    .trim()
                var jumlah_total_fix = parseInt(jumlah_modal_trim) - parseInt(jumlah_trim)
                var jumlah_total_idr = new Intl.NumberFormat('id', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                , }).format(jumlah_total_fix)
                $('#jumlah_modal').html(jumlah_total_idr)
            }
        })

    }

    $(document).ready(function() {
        $('.jumlah_currency').each(function() {
            $(this).on('input', function() {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency'
                    , currency: 'IDR'
                    , minimumFractionDigits: 0
                , }).format(harga)

                var jumlah = $(this).parent().parent().find('.detailjumlahcurrency')
                $(jumlah).html(harga_fix);
            })
        })

        $('select[name="id_currency"]').on('change', function() {
            var id_currency = $(this).val();

            if (id_currency) {
                $.ajax({
                    url: 'getkursJumlah/' + id_currency
                    , type: "GET"
                    , dataType: "json"
                    , success: function(data) {
                        if (data != 0) {
                            $('input[name="jumlah_actual"]').val(data);
                        } else {
                            $('input[name="jumlah_actual"]').val(0);
                            Swal.fire({
                                icon: 'error'
                                , title: 'Oops...'
                                , text: 'Jumlah Lembar Valas 0!'
                            , })
                        }


                    }
                    , error: function(response) {
                        console.log(response)
                    }
                });
            } else {
                $('input[name="jumlah_actual"]').empty();
            }
        });

        $('#jumlah_tukar').on('input', function() {
            var value = $(this).val()
            var nilai_kurs = $('.jumlah_currency').val()
            var calculate = parseFloat(value) * parseFloat(nilai_kurs)
            // var hasil_calc = calculate.toFixed(2)

            var hasil_calc = new Intl.NumberFormat('id', {
                style: 'currency'
                , currency: 'IDR'
                , minimumFractionDigits: 0
                , maximumFractionDigits: 0
            }).format(calculate);

            $('#detailjumlahcurrency').html(hasil_calc)
        });

        var template = $('#template_delete_button').html()
        $('#dataTableKonfirmasi').DataTable({
            "paging": false
            , "ordering": false
            , "info": false
            , "searching": false
            , "columnDefs": [{
                    "targets": -1
                    , "data": null
                    , "defaultContent": template
                }
                , {
                    "targets": 0
                    , "data": null
                    , 'render': function(data, type, row, meta) {
                        return meta.row + 1
                    }
                }
            ]
        });
    });

</script>

@endsection
