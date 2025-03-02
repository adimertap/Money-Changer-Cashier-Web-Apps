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
                            @if (count($modal_today) == 0)
                            <p>Tambah Data Modal Hari Ini</p>
                            @else
                            <p>Anda Telah Menambahkan Modal Hari Ini</p>
                            @endif

                        </div>
                        <div class="col-auto h-100">
                            @if (count($modal_today) == 0)
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#modaltambah">Tambah Modal</button>

                            @endif

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
                            <h6 class="mb-md-0 mb-lg-2">Sisa Modal Hari Ini</h6>
                            <span class="badge rounded-pill badge-soft-success">Today</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-3 fw-normal text-700">
                                @if (count($modal_today) == 0)
                                <p class="small mb-2">Modal Hari Ini Belum Ditetapkan</p>
                                @else
                                @if (empty($jumlah_modal_today))
                                <p class="small mb-2">Menunggu Approval</p>
                                @else
                                Rp. {{ number_format($jumlah_modal_today['riwayat_modal'], 0, ',', '.') }}
                                </span>
                            </h4>
                            <span class="badge rounded-pill badge-soft-success">Dari Rp. {{
                                number_format($jumlah_modal_today['total_modal_backup'], 0, ',', '.') }}</span>
                            @endif

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">
                @if (Auth::user()->role == 'Pegawai')
                    <h5 class="mb-0" data-anchor="data-anchor">Rekapan Data Modal Anda Hari Ini</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Modal</p>
                    @else
                    <h5 class="mb-0" data-anchor="data-anchor">Rekapan Data Modal</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Modal</p>
                    @endif
            </h5>
            <div class="d-flex justify-content-end">
                <label for="perPageSelect" class="me-2">Show</label>
                <select id="perPageSelect" class="form-select w-auto">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-striped" id="example">
                    <thead class="bg-200 text-900">
                        <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="tanggal_modal">Tanggal</th>
                                <th class="sort text-center" data-sort="pegawai">Pegawai</th>
                                <th class="sort text-center" data-sort="jumlah_modal">Jumlah Awal Modal</th>
                                <th class="sort text-center" data-sort="sisa_modal">Sisa Modal</th>
                                <th class="sort text-center" data-sort="status_modal">Status Pengajuan</th>
                                <th class="sort text-center" data-sort="tambah_modal">Tambah/Edit</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($modal as $index => $item)
                            <tr role="row" class="odd">
                                <td class="text-center">{{ $modal->firstItem() + $index }}</td>
                                <td class="tanggal_modal">{{ date('d-M-Y', strtotime($item->tanggal_modal)) }}</td>
                                <td class="pegawai">{{ $item->Pegawai->name }}</td>
                                <td class="jumlah_modal text-center">Rp. {{ number_format($item->jumlah_modal, 0, ',',
                                    '.') }}</td>
                                <td class="sisa_modal text-center"><span id="{{ $item->pengajuan_tambah }}">Rp. {{
                                        number_format($item->riwayat_modal, 0, ',', '.') }}</span></td>
                                {{-- <input type="hidden" class="pengajuan_tambah"
                                    value="{{ $item->pengajuan_tambah }}"> --}}
                                <td class="status_modal text-center">
                                    @if ($item->status_modal == 'Pending')
                                    <span class="badge rounded-pill badge-soft-primary">Pending, Menunggu
                                        Approval</span>
                                    @elseif ($item->status_modal == 'Terima')
                                    <span class="badge rounded-pill badge-soft-success">Diterima</span>
                                    @else
                                    <span class="badge rounded-pill badge-soft-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->riwayat_modal != 0 && $item->tanggal_modal != $today)
                                    <button class="btn p-0 ms-2 transferModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Transfer Modal"><span class="text-700 fas fa-sync-alt"></span>
                                    </button>
                                    @endif
                                    @if ($item->tanggal_modal == $today && $item->status_modal == 'Terima')
                                    <button class="btn p-0 ms-2 ajukanModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Ajukan Penambahan"><span class="text-700 fas fa-plus-circle"></span>
                                    </button>
                                    {{-- <button class="btn p-0 ms-2" type="button" data-bs-toggle="modal"
                                        value="{{ $item->id_modal }}"
                                        data-bs-target="#modaltambah-{{ $item->id_modal }}"><span
                                            class="text-700 fas fa-plus-circle"></span></button> --}}
                                    @endif

                                    @if ($item->status_modal == 'Tolak')
                                    <button class="btn p-0 ms-2 editModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit"><span class="text-700 fas fa-edit"></span>
                                    </button>
                                    @elseif ($item->status_modal == 'Pending')
                                    <span class="badge rounded-pill badge-soft-primary">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status_modal == 'Pending')
                                    <button class="btn p-0 ms-2 deleteModalBtn" value="{{ $item->id_modal }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Delete"><span class="text-700 fas fa-trash-alt"></span>
                                    </button>
                                    @elseif ($item->status_modal == 'Terima')
                                    <span class="badge rounded-pill badge-soft-success">Diterima</span>
                                    @else
                                    Keterangan: {{ $item->keterangan_approval }}
                                    @endif
                                </td>
                            </tr>

                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $modal->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modaltambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('modal.store') }}" id="form_tambah_baru" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Tambah Modal</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1 mb-3">Lengkapi Form Modal berikut ini</p>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_modal">Jumlah Modal</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control tambah_jumlah_modal" id="tambah_jumlah_modal" name="jumlah_modal"
                                type="text" placeholder="Input Jumlah Modal" value="{{ old('jumlah_modal') }}"
                                required />
                            <p class="text-primary"> IDR:
                                <span id="detailtambahmodal" class="detailtambahmodal">
                                </span>
                            </p>
                            @error('jumlah_modal')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="button" id="btn_form_tambah_baru" onclick="form_tambah_baru(event)">Tambah Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalpengajuan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/tambah-modal') }}" id="form_ajukan_tambah" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Ajukan Penambahan Modal</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1 mb-3">Tambah Pengajuan Modal</p>
                        <input type="hidden" name="ajukan_modal_id" id="ajukan_modal_id">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_modal">Jumlah Modal</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control jumlah_modal_edit @error('jumlah_modal') is-invalid @enderror"
                                id="jumlah_modal_edit" name="jumlah_modal" type="number"
                                placeholder="Input Jumlah Modal" value="{{ old('jumlah_modal') }}" required />
                            <p class="text-primary"> IDR:
                                <span id="detaileditmodal" class="detaileditmodal"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btn_form_ajukan_tambah" onclick="form_ajukan_tambah(event)" type="button">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modaltransfer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/transfer-modal') }}" id="form_transfer" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-primary rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Transfer Sisa Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <input type="hidden" name="modal_transfer_id" id="modal_transfer_id">
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Melakukan Transfer Modal Ini?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" onclick="form_transfer(event)" id="btn_form_transfer" type="button">Ya! Transfer </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ url('/modal') }}" method="POST" id="editForm">
                @method('PUT')
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-primary rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Edit Data Modal</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="modal_edit_id" id="modal_edit_id">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="jumlah_modal">Jumlah Modal</label><span
                                                class="mr-4 mb-3" style="color: red">*</span>
                                            <input class="form-control jumlah_modal_update" id="jumlah_modal_update"
                                                name="jumlah_modal" type="number" placeholder="Input Jumlah Modal"
                                                value="{{ old('jumlah_modal') }}" required />
                                            <p class="text-primary"> Pengajuan Sebelumnya:
                                                <span id="detailupdatemodal" class="detailupdatemodal">

                                                </span>
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-sm" type="submit">Yes! Edit </button>
                </div>
            </form>
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
            <form action="{{ url('/delete-modal') }}" method="POST">
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
                                        <input type="hidden" name="modal_delete_id" id="id_modal">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Modal ini?
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





    function form_tambah_baru(event) {
        event.preventDefault()
        var form = $('#form_tambah_baru')
        var _token = form.find('input[name="_token"]').val()
        var jumlah_modal = form.find('input[name="jumlah_modal"]').val()

        var data = {
            _token: _token,
            jumlah_modal: jumlah_modal,
        }
        $('#btn_form_tambah_baru').prop('disabled', true);

        $.ajax({
            method: 'post',
            url: '/modal',
            data: data,
            success: function(response) {
                window.location.href = '/modal'

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
                    icon: 'success',
                    title: 'Data Masih Diproses Mohon Tunggu'
                })
            },
            error: function(response) {
                $('#btn_form_tambah_baru').prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error! Transaksi Tidak dapat disimpan, Hubungi Developer',
                })
            }
        });

    };

    function form_ajukan_tambah(event) {
        event.preventDefault()
        var form = $('#form_ajukan_tambah')
        var _token = form.find('input[name="_token"]').val()
        var jumlah_modal = form.find('input[name="jumlah_modal"]').val()
        var ajukan_modal_id = form.find('input[name="ajukan_modal_id"]').val()
        var data = {
            _token: _token,
            jumlah_modal: jumlah_modal,
            ajukan_modal_id: ajukan_modal_id
        }
        $('#btn_form_ajukan_tambah').prop('disabled', true);

        $.ajax({
            method: 'post',
            url: '/tambah-modal',
            data: data,
            success: function(response) {
                window.location.href = '/modal'

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
                    icon: 'success',
                    title: 'Data Masih Diproses Mohon Tunggu'
                })
            },
            error: function(response) {
                console.log(response)
                $('#btn_form_ajukan_tambah').prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error! Transaksi Tidak dapat disimpan, Hubungi Developer',
                })
            }
        });

    };

    function form_transfer(event) {
        event.preventDefault()
        var form = $('#form_transfer')
        var _token = form.find('input[name="_token"]').val()
        var modal_transfer_id = form.find('input[name="modal_transfer_id"]').val()

        var data = {
            _token: _token,
            modal_transfer_id: modal_transfer_id,
        }
        console.log(data)

        $('#btn_form_transfer').prop('disabled', true);

        $.ajax({
            method: 'post',
            url: '/transfer-modal',
            data: data,
            success: function(response) {
                window.location.href = '/modal'

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
                    icon: 'success',
                    title: 'Data Masih Diproses Mohon Tunggu'
                })
            },
            error: function(response) {
                console.log(response)
                $('#btn_form_transfer').prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error! Transaksi Tidak dapat disimpan, Hubungi Developer',
                })
            }
        });

    };






    $(document).ready(function () {
        var table = $('#example').DataTable({
            paging:false
        });
        $('#perPageSelect').on('change', function () {
            var perPage = $(this).val();
            window.location.href = '?per_page=' + perPage;
        });
        table.on('click', '.editModalBtn', function () {
            var id = $(this).val();
            $('#modal_edit_id').val(id)

            $tr = $(this).closest('tr');
            if ($($tr).hasClass('clid')) {
                $tr = $tr.prev('.parent')
            }

            var data = table.row($tr).data();
            var jumlah = data[3].split('Rp.')[1].replace(',', '').replace(',', '').trim()

            var tes = data[4]
            var pengajuan = $(tes).attr('id')
            var pengajuan_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
            }).format(pengajuan)

            $('#jumlah_modal_update').val(jumlah)
            $('#detailupdatemodal').html(pengajuan_fix)
            // $('#pengajuan_sebelumnya').html(pengajuan_fix)
            $('#editForm').attr('action', '/modal/' + id)
            $('#editModal').modal('show');

        })

        $('.transferModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#modal_transfer_id').val(id)
            $('#modaltransfer').modal('show');
        });

        $('.ajukanModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#ajukan_modal_id').val(id)
            $('#modalpengajuan').modal('show');
        });

        $('.deleteModalBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_modal').val(id)
            $('#deleteModal').modal('show');
        });

        $('.tambah_jumlah_modal').each(function () {
            $(this).on('input', function () {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(harga)

                var jumlah = $(this).parent().parent().find('.detailtambahmodal')
                $(jumlah).html(harga_fix);
            })
        });

        $('.jumlah_modal_update').each(function () {
            $(this).on('input', function () {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(harga)

                var jumlah = $(this).parent().parent().find('.detailupdatemodal')
                $(jumlah).html(harga_fix);
            })
        });

        $('.jumlah_modal_edit').each(function () {
            $(this).on('input', function () {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(harga)

                var jumlah = $(this).parent().parent().find('.detaileditmodal')
                $(jumlah).html(harga_fix);
            })
        });


    });

</script>



@endsection
