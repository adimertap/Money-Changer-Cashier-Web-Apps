@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
            style="background-image:url(/../falcon/assets/img/icons/spot-illustrations/corner-4.png);"></div>
        <div class="card-body position-relative">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Filter Log Data Sesuai Tanggal Edit</h5>
                    <p class="mt-2">Pilih Tanggal Awal dan Pilih Tanggal Akhir</p>
                    <hr>
                </div>
                <div class="col-lg-6">
                    <div class="col-12 col-xl-auto mt-2">
                        <span id="total_records"></span>
                        <p></p>
                        <form id="form1">
                            <div class="row input-daterange">
                                <div class="col-md-4">
                                    <label class="small">Start Date</label>
                                    <input class="form-control datetimepicker" id="from_date" type="date"
                                        name="from_date" placeholder="From Date"
                                        data-options='{"disableMobile":true}' />
                                </div>
                                <div class="col-md-4">
                                    <label class="small">End Date</label>
                                    <input class="form-control datetimepicker" id="to_date" type="date" name="to_date"
                                        placeholder="To Date" data-options='{"disableMobile":true}' />
                                </div>
                                <div class="col-md-4">
                                    <button type="button" name="filter" onclick="filter_tanggal(event)"
                                        class="btn btn-primary px-4 mt-4">Filter</button>
                                </div>
                            </div>
                        </form>
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
                                <td class="text-center tanggal_transaksi fs--1">{{ date('d-M-Y', strtotime($item->tanggal_transaksi)) }}, {{ date('H:i:s', strtotime($item->created_at)) }} </td>
                                <td class="text-center tanggal_edit fs--1">{{ date('d-M-Y', strtotime($item->created_at)) }}, {{ date('H:i:s', strtotime($item->created_at)) }} </td>
                                <td class="text-center kode_transaksi fs--1">{{ $item->kode_transaksi }}</td>
                                <td class="text-center total text-center fs--1">Rp. {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                                <td class="text-center status text-center fs--1">
                                    @if($item->jenis_log == 'Edit')
                                    <span class="badge rounded-pill badge-soft-info">Edit</span>
                                    @else
                                    <span class="badge rounded-pill badge-soft-danger">Delete</span>

                                    @endif
                                    
                                </td>
                                <td class="text-center fs--1">
                                    <a href="{{ route('log-edit.show', $item->id_log) }}"
                                        class="btn p-0 ms-2" type="button" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a>
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

<script>
    $(document).ready(function () {
        var table = $('#example').DataTable();
    })

    function filter_tanggal(event) {
        event.preventDefault()
        var form1 = $('#form1')
        var tanggal_mulai = form1.find('input[name="from_date"]').val()
        var tanggal_selesai = form1.find('input[name="to_date"]').val()
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
        window.location.href = '/owner/jurnal-harian?from=' + tanggal_mulai + '&to=' + tanggal_selesai
    }

</script>



@endsection
