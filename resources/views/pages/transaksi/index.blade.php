@extends('layouts.app')

@section('content')
<main>
    <div class="row g-3 mb-3 mt-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-8 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Welcome, {{ Auth::user()->nama_panggilan }}!</h5>
                            <p>Rekapan Transaksi Anda Hari Ini</p>
                        </div>
                        <div class="col-auto h-100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center">
                        <div class="col d-md-flex d-lg-block flex-between-center">
                            <h6 class="mb-md-0 mb-lg-2">Total Transaksi Anda</h6>
                            <span class="badge rounded-pill badge-soft-success">Today</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-3 fw-normal text-700">
                                <p class="small mb-2">5 Transaksi</p>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('messageberhasil'))
    <div class="alert alert-success border-2 d-flex align-items-center" role="alert" id="alertsukses">
        <div class="bg-success me-3 icon-item"><span class="fas fa-check-circle text-white fs-3"></span></div>
        <p class="mb-0 flex-1">{{ session('messageberhasil') }}</p><button class="btn-close" type="button"
            data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('messagehapus'))
    <div class="alert alert-danger border-2 d-flex align-items-center" role="alert" id="alertgagal">
        <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
        <p class="mb-0 flex-1">{{ session('messagehapus') }}</p><button class="btn-close" type="button"
            data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Rekapan Data Transaksi {{ Auth::user()->nama_panggilan }}
                    </h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Transaksi</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","tanggal_transaksi","kode_transaksi","total"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="tanggal_transaksi">Tanggal</th>
                                <th class="sort text-center" data-sort="kode_transaksi">Kode Transaksi</th>
                                <th class="sort text-center" data-sort="total">Total Transaksi</th>
                                <th class="sort text-center" data-sort="print">Print</th>
                                <th class="sort text-center" data-sort="status">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($transaksi as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="tanggal_transaksi">{{ $item->tanggal_transaksi }}</td>
                                <td class="kode_transaksi">{{ $item->kode_transaksi }}</td>
                                <td class="total text-center">Rp. {{ number_format($item->total) }}</td>
                                <td class="status text-center">
                                    <span class="badge rounded-pill badge-soft-success">Lunas</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('cetak', $item->id_transaksi) }}" class="btn p-0 ms-2"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-download"></span>
                                    </a>
                                    <a href="{{ route('cetak', $item->id_transaksi) }}" class="btn p-0 ms-2"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-print"></span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transaksi.show', $item->id_transaksi) }}" class="btn p-0 ms-2"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a>
                                    <button class="btn p-0 ms-2 deleteModalBtn" value="{{ $item->id_transaksi }}"
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
                <div class="row align-items-center mt-3">
                    <div class="pagination d-none"></div>
                    <div class="col">
                        <p class="mb-0 fs--1">
                            <span class="d-none d-sm-inline-block" data-list-info="data-list-info"></span>
                            <span class="d-none d-sm-inline-block"> &mdash; </span>
                            <a class="fw-semi-bold" href="#!" data-list-view="*">View all<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a
                                class="fw-semi-bold d-none" href="#!" data-list-view="less">View Less<span
                                    class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                        </p>
                    </div>
                    <div class="col-auto d-flex"><button class="btn btn-sm btn-primary" type="button"
                            data-list-pagination="prev"><span>Previous</span></button><button
                            class="btn btn-sm btn-primary px-4 ms-2" type="button"
                            data-list-pagination="next"><span>Next</span></button></div>
                </div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="deleteModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ url('/transaksi-delete') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Transaksi</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_delete_id" id="id_modal">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Modal ini?, Dengan Menghapus Data Transaksi Modal akan Bertambah Sesuai dengan Total Transaksi
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


<script>
    $(document).ready(function () {
        $('.deleteModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_modal').val(id)
            $('#deleteModal').modal('show');
        })

        var table = $('#datatable').DataTable();
    })

</script>



@endsection
