@extends('layouts.app')

@section('content')
<div class="row g-3 mb-3 mt-3">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="row flex-between-center g-0">
                    <div class="col-6 d-lg-block flex-between-center">
                        <h5 class="text-primary mb-1">Welcome, {{ Auth::user()->nama_panggilan }}!</h5>
                        <p>Rekapan Transaksi Hari Ini</p>
                    </div>
                    <div class="col-auto h-100">
                        <button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0" type="button"
                            data-bs-toggle="modal" data-bs-target="#modalfilter"><span class="fas fa-arrow-down me-1">
                            </span>Download Laporan Hari Ini
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="row flex-between-center">
                    <div class="col d-md-flex d-lg-block flex-between-center">
                        <h6 class="mb-md-0 mb-lg-2">Jumlah Transaksi Hari Ini</h6>
                        <span class="badge rounded-pill badge-soft-success">{{ $count }} Transaksi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="row flex-between-center">
                    <div class="col d-md-flex d-lg-block flex-between-center">
                        <h6 class="mb-md-0 mb-lg-2">Total Transaksi Hari Ini</h6>
                        <span class="badge rounded-pill badge-soft-success">Rp.
                            {{ number_format($total_transaksi) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Rekapan Data Transaksi Anda Hari Ini
                </h5>
                <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Transaksi</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="tableExample"
            data-list='{"valueNames":["no","pegawai","tanggal_transaksi","kode_transaksi","total"],"page":20,"pagination":true}'>
            <div class="table-responsive scrollbar">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="sort text-center fs--1" data-sort="no">No.</th>
                            <th class="sort text-center fs--1" data-sort="pegawai">Pegawai</th>
                            <th class="sort text-center fs--1" data-sort="tanggal_transaksi">Tanggal</th>
                            <th class="sort text-center fs--1" data-sort="kode_transaksi">Kode Transaksi</th>
                            <th class="sort text-center fs--1" data-sort="total">Total Transaksi</th>
                            <th class="sort text-center fs--1" data-sort="print">Print</th>
                            <th class="sort text-center fs--1" data-sort="status">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse ($transaksi as $item)
                        <tr role="row" class="odd">
                            <th scope="row" class="no fs--1">{{ $loop->iteration}}.</th>
                            <td class="text-start pegawai fs--1">{{ $item->Pegawai->name }}</td>
                            <td class="text-center tanggal_transaksi fs--1">{{ date('d-M-Y', strtotime($item->tanggal_transaksi)) }}</td>
                            <td class="text-center kode_transaksi fs--1">{{ $item->kode_transaksi }}</td>
                            <td class="text-center total text-center fs--1">Rp. {{ number_format($item->total) }}</td>
                            <td class="text-center status text-center fs--1">
                                <span class="badge rounded-pill badge-soft-success">Lunas</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('cetak', $item->id_transaksi) }}" target="_blank" class="btn p-0 ms-1"
                                    type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><span
                                        class="text-700 fas fa-print"></span>
                                </a>
                            </td>
                            <td class="text-center fs--1">
                                <a href="{{ route('transaksi.show', $item->id_transaksi) }}" class="btn p-0 ms-1"
                                    type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><span
                                        class="text-700 fas fa-eye"></span>
                                </a>
                                <a href="{{ route('transaksi.edit', $item->id_transaksi) }}" class="btn p-0 ms-1"
                                    type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><span
                                        class="text-700 fas fa-edit"></span>
                                </a>
                                <button class="btn p-0 deleteModalBtn" value="{{ $item->id_transaksi }}"
                                    type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Delete"><span class="text-700 fas fa-trash-alt"></span>
                                </button>
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


<div class="modal fade" id="deleteModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ url('/delete-transaksi') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="transaksi_id" id="id_transaksi">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Transaksi ini? Dengan Menghapus Data Transaksi Modal akan Bertambah Sesuai dengan Total Transaksi
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger btn-sm" type="submit">Yes! Delete </button>
                </div>
            </form>
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
            <form action="{{ route('export-dokumen-harian') }}" id="form2">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Filter Data untuk Export</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1 mb-3">Filter Data Berdasarkan Inputan</p>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault1" type="radio" value="excel"
                                        name="radio_input" checked />
                                    <label class="form-check-label" for="flexRadioDefault1">Export Excel</label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault2" type="radio" value="pdf"
                                        name="radio_input" />
                                    <label class="form-check-label" for="flexRadioDefault2">Export PDF</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <label for="currency">Filter by Currency</label>
                                <select class="form-select js-choice" id="id_currency" name="id_currency"
                                    data-options='{"removeItemButton":true,"placeholder":true}'>
                                    <option value="">Pilih Currency</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Export Data </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.deleteModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_transaksi').val(id)
            $('#deleteModal').modal('show');
        })

        var table = $('#datatable').DataTable();
    })

</script>



@endsection
