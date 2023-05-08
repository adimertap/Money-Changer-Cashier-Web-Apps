@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
            style="background-image:url(/../../falcon/assets/img/icons/spot-illustrations/corner-4.png);"></div>
        <div class="card-body position-relative">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Transaksi Tanggal {{ $transaksi[0]->tanggal_transaksi }}</h5>
                    <p class="mt-2">Data Transaksi dikelompokan berdasarkan Tanggal</p>
                    <hr>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div id="tableExample"
            data-list='{"valueNames":["no","pegawai","tanggal_transaksi","total","kode_transaksi"],"page":20,"pagination":true}'>
            <div class="table-responsive scrollbar">
                <table id="example2" class="table table-striped" style="width:100%">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="sort text-center fs--1" data-sort="no">No.</th>
                            <th class="sort text-center fs--1" data-sort="pegawai">Pegawai</th>
                            <th class="sort text-center fs--1" data-sort="kode_transaksi">Kode Transaksi</th>
                            <th class="sort text-center fs--1" data-sort="nama_customer">Customer</th>
                            <th class="sort text-center fs--1" data-sort="nomor_passport">Passport</th>
                            <th class="sort text-center fs--1" data-sort="negara_asal">Negara Asal</th>
                            <th class="sort text-center fs--1" data-sort="total">Total Transaksi</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse ($transaksi as $item)
                        <tr role="row" class="odd">
                            <th scope="row" class="no fs--1">{{ $loop->iteration}}.</th>
                            <td class="text-center pegawai fs--1">{{ $item->Pegawai->name }}</td>
                            <td class="text-center kode_transaksi fs--1">{{ $item->kode_transaksi }}</td>
                            <td class="text-center nama_customer fs--1">{{ $item->nama_customer }}</td>
                            <td class="text-center nomor_passport fs--1">{{ $item->nomor_passport }}</td>
                            <td class="text-center negara_asal fs--1">{{ $item->negara_asal }}</td>
                            <td class="text-center total fs--1">Rp. {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="text-center fs--1">
                                <a href="{{ route('bulanan-transaksi', $item->id_transaksi) }}"
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
        var table = $('#example2').DataTable();
    })
</script>



@endsection
