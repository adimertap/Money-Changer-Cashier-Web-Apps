@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
            style="background-image:url(/../falcon/assets/img/icons/spot-illustrations/corner-4.png);"></div>
        <div class="card-body position-relative">
            <div class="row">
                <div class="col-lg-9">
                    <h5>Filter Log Data</h5>
                    <p class="mt-2">Filter Log Data Sesuai Tanggal atau Jenis Log</p>
                    <hr>
                </div>
                <div class="col-auto">
                    <div class="col-auto col-xl-auto">
                        <button type="button" name="filter" data-bs-toggle="modal" data-bs-target="#modalfilter"
                            class="btn btn-primary px-4 mt-4">Filter</button>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="col-auto col-xl-auto">
                        <a href="{{ route('log-edit.index') }}" type="button" 
                            class="btn btn-danger px-4 mt-4">Reset</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","pegawai","tanggal_transaksi","kode_transaksi","total"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center fs--1" data-sort="no">No.</th>
                                <th class="sort text-center fs--1" data-sort="pegawai">Pegawai</th>
                                <th class="sort text-center fs--1" data-sort="tanggal_edit">Tanggal Edit</th>
                                <th class="sort text-center fs--1" data-sort="tanggal_transaksi">Tanggal Transaksi</th>
                                <th class="sort text-center fs--1" data-sort="kode_transaksi">Kode Transaksi</th>
                                <th class="sort text-center fs--1" data-sort="total">Total Transaksi</th>
                                <th class="sort text-center fs--1" data-sort="status">Jenis Log</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($log as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no fs--1">{{ $loop->iteration}}.</th>
                                <td class="text-start pegawai fs--1">{{ $item->Pegawai->name }}</td>
                                <td class="text-center tanggal_transaksi fs--1">{{ date('d-M-Y',
                                    strtotime($item->tanggal_transaksi)) }}, {{ date('H:i:s',
                                    strtotime($item->created_at)) }} </td>
                                <td class="text-center tanggal_edit fs--1">{{ date('d-M-Y',
                                    strtotime($item->created_at)) }}, {{ date('H:i:s', strtotime($item->created_at)) }}
                                </td>
                                <td class="text-center kode_transaksi fs--1">{{ $item->kode_transaksi }}</td>
                                <td class="text-center total text-center fs--1">Rp. {{ number_format($item->total, 0,
                                    ',', '.') }}
                                </td>
                                <td class="text-center status text-center fs--1">
                                    @if($item->jenis_log == 'Edit')
                                    <span class="badge rounded-pill badge-soft-info">Edit</span>
                                    @else
                                    <span class="badge rounded-pill badge-soft-danger">Delete</span>

                                    @endif

                                </td>
                                <td class="text-center fs--1">
                                    <button class="btn p-0 ms-2 detailLog" value="{{ $item->id_log }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </button>

                                    {{-- <a href="{{ route('log-edit.show', $item->id_log) }}" class="btn p-0 ms-2"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a> --}}
                                </td>
                            </tr>
                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="modaldetail" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 850px">
        <div class="modal-content position-relative">
            <div class="card mt-4">
                <div class="card-body">
                    <h4 class="mb-1">Detail Log</h4>
                    Keterangan Log:
                    <p id="keteranganLog" class="mb-4"></p>
                    <div id="tableExample"
                        data-list='{"valueNames":["no","enama_currency","enama_country","ejenis","ekurs",
                            "eketerangan","eurutan","eflag"],"page":20,"pagination":true}'>
                        <div class="table-responsive scrollbar">
                            <table class="table table-bordered table-striped fs--1 mb-0" id="datatable2">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th class="sort text-center" data-sort="enama_currency">Currency</th>
                                        <th class="sort text-center" data-sort="enama_country">Harga Currency</th>
                                        <th class="sort text-center" data-sort="ejenis">Jumlah Tukar</th>
                                        <th class="sort text-center" data-sort="ekurs">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="listdetail">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-auto mt-4">
                            <button class="btn btn-secondary btn-sm" type="button" id="clearbtn" >Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalfilter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form1">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Filter Log Data</h4>
                    </div>
                    <div class="p-4 pb-0 mb-4">
                        <p class="text-word-break fs--1 mb-3">Filter Data Berdasarkan Inputan</p>

                        <div class="row input-daterange">
                            <div class="col-md-6">
                                <label class="small">Tanggal Awal Edit/Delete</label>
                                <input class="form-control datetimepicker" id="from_date_export" type="date"
                                    name="from_date_export" placeholder="From Date"
                                    data-options='{"disableMobile":true}' />
                            </div>
                            <div class="col-md-6">
                                <label class="small">Tanggal Akhir Edit/Delete</label>
                                <input class="form-control datetimepicker" id="to_date_export" type="date"
                                    name="to_date_export" placeholder="To Date" data-options='{"disableMobile":true}' />
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label" for="jenis_log">Jenis Log</label>
                            <select name="jenis_log" id="jenis_log" class="form-select" value="{{ old('jenis_log') }}">
                                <option id="jlog" value="">Filter by Jenis Log</option>
                                <option value="Edit">Log Edit</option>
                                <option value="Delete">Log Delete</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="button" name="filter" onclick="filter_tanggal(event)">Filter
                        Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('#clearbtn').on('click', function() {
            $('.listdetail').empty();
            $('#modaldetail').modal('hide');
        });

        var table = $('#example').DataTable();
        // $('#datatable2').DataTable();
        table.on('click', '.detailLog', function () {
            var id = $(this).val();
            $('#modaldetail').modal('show');
            $.ajax({
                url: 'log-edit/getdetail/' + id,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    console.log(response)
                    $('#keteranganLog').html(response[0].keterangan_log);
                    $.each(response, function (key, value) {
                        var jumlah = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value.jumlah_currency);

                        var total = new Intl.NumberFormat('id', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value.total_tukar);
							$('#datatable2').append("<tr>\
                                <td>"+value.nama_currency+"</td>\
                                <td>"+jumlah+"</td>\
                                <td>"+value.jumlah_tukar+"</td>\
                                <td>"+total+"</td>\
                                </tr>");
						})
                },
                error: function (response) {
                    console.log(response)
                }
            });
        })
    })

    function filter_tanggal(event) {
        event.preventDefault()
        var form1 = $('#form1')
        var tanggal_mulai = form1.find('input[name="from_date_export"]').val()
        var tanggal_selesai = form1.find('input[name="to_date_export"]').val()
        var jenis_log = form1.find('select[name="jenis_log"]').val()

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
            icon: 'info',
            title: 'Mohon Tunggu, Sedang diproses ...'
        })
        window.location.href = '/log-edit?from=' + tanggal_mulai + '&to=' + tanggal_selesai + '&jenis=' + jenis_log
    }

</script>



@endsection