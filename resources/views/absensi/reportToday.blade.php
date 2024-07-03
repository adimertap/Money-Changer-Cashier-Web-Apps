@php
    $layout ='layouts.app'
@endphp
@extends($layout)


@section('content')
@if($errors->any())
<script>
    $('#modal-tambah').modal('show');
</script>
@endif
<main>
    <div class="mt-4">
        <div class="d-flex justify-content-between">
            <h5 class="mb-0" data-anchor="data-anchor">Report Jadwal Hari Ini {{ $today }}</h5>
        </div>
        <div class="card mb-3 mt-3">
            <div class="card-header">
            </div>
            <div class="card-body">
                <div id="tableExample"
                    data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs--1 mb-0" id="datatableToday">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th class="sort text-center" data-sort="no">No.</th>
                                    <th class="sort text-center" data-sort="nama">Tanggal</th>
                                    <th class="sort text-center" data-sort="in">Shift</th>
                                    <th class="sort text-center" data-sort="out">In</th>
                                    <th class="sort text-center" data-sort="out">Out</th>
                                    <th class="sort text-center" data-sort="out">Status In</th>
                                    <th class="sort text-center" data-sort="out">Status Out</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($jadwal as $item)
                                <tr role="row" class="odd">
                                    <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                    <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}
                                    </td>
                                    <td class="in">{{ $item->Shift->shift_name }} , Jam {{ $item->Shift->shift_in }} -
                                        {{ $item->Shift->shift_out }}</td>
                                    <td class="text-center">
                                        @if($item->jam_masuk == "" || !$item->jam_masuk)
                                        -
                                        @else
                                        {{ $item->jam_masuk }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->jam_keluar == "" || !$item->jam_keluar)
                                        -
                                        @else
                                        {{ $item->jam_keluar }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_in == 'Terlambat')
                                        <span class="badge rounded-pill badge-soft-danger">Terlambat</span>
                                        @elseif ($item->status_absen_in == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-warning">Belum Absen</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_out == 'Pulang Cepat')
                                        <span class="badge rounded-pill badge-soft-danger">Pulang Cepat</span>
                                        @elseif ($item->status_absen_out == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-warning">Belum Absen</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</main>

<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable();
        var tableToday = $('#datatableToday').DataTable();
    });
</script>
@endsection
