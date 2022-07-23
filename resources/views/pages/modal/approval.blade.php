@extends('layouts.app')

@section('content')
<main>
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
                    <h5 class="mb-0" data-anchor="data-anchor">Approval Modal</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Modal</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","tanggal_modal",,"pegawai","jumlah_modal","status_modal"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="tanggal_modal">Tanggal</th>
                                <th class="sort text-center" data-sort="pegawai">Pegawai</th>
                                <th class="sort text-center" data-sort="jumlah_modal">Modal Awal</th>
                                <th class="sort text-center" data-sort="pengajuan_tambah">Tambahan</th>
                                <th class="sort text-center" data-sort="status_modal">Status Modal</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($modal as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="tanggal_modal">{{ $item->tanggal_modal }}</td>
                                <td class="pegawai">{{ $item->Pegawai->name }}</td>
                                <td class="jumlah_modal">Rp. {{ number_format($item->jumlah_modal) }}</td>
                                <td class="pengajuan_tambah">
                                    @if ($item->pengajuan_tambah != null)
                                    Rp. {{ number_format($item->pengajuan_tambah) }}
                                    @else
                                    -
                                    @endif
                                    
                                </td>
                                <td class="status_modal text-center">
                                    @if ($item->status_modal == 'Pending')
                                        <span class="badge rounded-pill badge-soft-primary">Pending, Menunggu Approval</span>
                                    @elseif ($item->status_modal == 'Terima')
                                        <span class="badge rounded-pill badge-soft-success">Diterima</span>
                                    @else
                                        <span class="badge rounded-pill badge-soft-danger">Ditolak</span>
                                    @endif    
                                </td>
                                <td class="text-center">
                                    @if ($item->status_modal == 'Pending')
                                    <button class="btn btn-success btn-sm p-2 me-1 mb-1 terimaModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Terima Data">Terima</span>
                                    </button>
                                    <button class="tn btn-danger btn-sm p-2 me-1 mb-1 tolakModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Tolak Data">Tolak</span>
                                    </button>
                                    @elseif ($item->status_modal == 'Terima')
                                        <span class="badge rounded-pill badge-soft-success">Diterima</span>
                                    @else
                                        <span class="badge rounded-pill badge-soft-danger">Ditolak</span>
                                    @endif    
                                  
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

<div class="modal fade" id="TerimaModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ route('approval-modal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-success rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Konfirmasi Approve Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_id" id="id_modal">
                                        <input type="hidden" name="status_modal" id="status" value="{{ "Terima" }}">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Melakukan Approve Terhadap Data Modal ini?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm" type="submit">Ya! Approve </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="TolakModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ route('approval-modal.store') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Konfirmasi Tolak Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_id" id="id_modal2">
                                        <input type="hidden" name="status_modal" id="status" value="{{ "Tolak" }}">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Melakukan Tolak Terhadap Data Modal ini? Berikan Keterangan!
                                            <div class="col-12">
                                                <label class="form-label" for="keterangan_approval">Keterangan Penolakan</label><span
                                                class="mr-4 mb-3" style="color: red">*</span>
                                                <textarea class="form-control" id=" keterangan_approval" name="keterangan_approval" type="text"
                                                    placeholder="Input Keterangan Penolakan" value="{{ old('keterangan_approval') }}"></textarea>
                                            </div>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger btn-sm" type="submit">Ya! Tolak </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.terimaModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_modal').val(id)
            $('#TerimaModal').modal('show');
        })

        $('.tolakModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_modal2').val(id)
            $('#TolakModal').modal('show');
        })
    })

</script>



@endsection
