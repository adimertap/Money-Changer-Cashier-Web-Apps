@extends('layouts.app')

@section('content')
<main>
    <div class="row g-3 mb-3 mt-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-6 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Welcome, {{ Auth::user()->nama_panggilan }}!</h5>
                            <p>Tambah Data Pegawai Disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <a href="{{ route('master-pegawai.create') }}" class="btn btn-primary btn-sm me-1 mb-1"
                                type="button">Tambah Data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center">
                        <div class="col d-md-flex d-lg-block flex-between-center">
                            <h6 class="mb-md-0 mb-lg-2">Total Pegawai Anda</h6><span
                                class="badge rounded-pill badge-soft-success">Pegawai</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-3 fw-normal text-700"><span>{{ $jumlah }}</span> Pegawai</h4>
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
                    <h5 class="mb-0" data-anchor="data-anchor">Data Pegawai</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Kepegawaian</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="tableExample2"
                data-list='{"valueNames":["no","name","email","namapanggilan","role","jeniskelamin"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs--1 mb-0">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="name">Nama</th>
                                <th class="sort text-center" data-sort="namapanggilan">Nama Panggilan</th>
                                <th class="sort text-center" data-sort="email">Email</th>
                                <th class="text-center">No. Telephone</th>
                                <th class="sort text-center" data-sort="jeniskelamin">Jenis Kelamin</th>
                                <th class="sort text-center" data-sort="role">Role</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($pegawai as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="name">{{ $item->name }}</td>
                                <td class="namapanggilan">{{ $item->nama_panggilan }}</td>
                                <td class="email">{{ $item->email }}</td>
                                <td>{{ $item->phone_number }}</td>
                                <td class="jeniskelamin">{{ $item->jenis_kelamin }}</td>
                                <td class="role">{{ $item->role }}</td>
                                <td class="text-center">
                                    <a href="{{ route('master-pegawai.show', $item->id) }}" class="btn p-0"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a>
                                    <a href="{{ route('master-pegawai.edit', $item->id) }}" class="btn p-0 ms-2" type="button" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit"><span class="text-700 fas fa-edit"></span>
                                    </a>
                                    <button class="btn p-0 ms-2 deletePegawaiBtn" value="{{ $item->id }}" type="button"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><span
                                            class="text-700 fas fa-trash-alt"></span>
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
                class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form action="{{ url('owner/delete-pegawai') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Konfirmasi Hapus Data Pegawai</h4>
                    </div>
                    <div class="p-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="pegawai_delete_id" id="id_pegawai">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Pegawai ini?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" type="submit">Yes! Delete </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.deletePegawaiBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_pegawai').val(id)
            $('#deleteModal').modal('show');
        })
    })

</script>



@endsection
