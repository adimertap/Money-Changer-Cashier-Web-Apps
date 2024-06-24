@extends('layouts.app')

@section('content')
@if($errors->any())
<script>
    $('#modal-tambah').modal('show');
</script>
@endif
<main>
    <div class="mt-4">
        <div class="card mb-3 mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0" data-anchor="data-anchor">Laporan Absensi, Pilih User
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div id="tableExample"
                    data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs--1 mb-0" id="datatableToday">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th class="sort text-center" data-sort="no">No.</th>
                                    <th class="sort text-center" data-sort="nama">Nama</th>
                                    <th class="sort text-center" data-sort="in">Role</th>
                                    <th class="sort text-center" data-sort="out">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($user as $item)
                                <tr role="row" class="odd">
                                    <th scope="row" class="no">{{ $loop->iteration }}.</th>
                                    <td class="text-center">{{ $item->name }}
                                    </td>
                                    <td class="in">{{ $item->role }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('jadwal-laporan.index', $item->id) }}" class="btn btn-xs btn-primary" style="font-size: 12px!important" type="button">Report Absensi</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable();
        var tableToday = $('#datatableToday').DataTable();
    });
</script>
@endsection