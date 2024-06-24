@extends('layouts.app')

@section('content')
@if($errors->any())
<script>
    // Open the modal-tambah
        $('#modal-tambah').modal('show');
</script>
@endif
<main>
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5 class="mb-0" data-anchor="data-anchor">Data Shift Kerja</h5>
                <button id="btnTambah" onclick="tambahFunction()" class="btn btn-sm btn-primary">Tambah</button>
            </div>
        </div>
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="nama">Nama Shift</th>
                                <th class="sort text-center" data-sort="in">Shift In</th>
                                <th class="sort text-center" data-sort="out">Shift Out</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @foreach ($shift as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="nama">{{ $item->shift_name }}</td>
                                <td class="in">{{ $item->shift_in }}</td>
                                <td class="out">{{ $item->shift_out }}</td>
                                <td>
                                    <button class="btn p-0 ms-2 editBtn" value="{{ $item->shift_id }}" type="button"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Shift"><span
                                            class="text-700 fas fa-edit"></span>
                                    </button>
                                    <button class="btn p-0 ms-2 deleteBtn" value="{{ $item->shift_id }}"
                                        onclick="deleteFunction({{ $item->shift_id }})" type="button"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><span
                                            class="text-700 fas fa-trash-alt"></span>
                                    </button>
                                    <form id="delete-form-{{ $item->shift_id }}"
                                        action="{{ route('shift.destroy', $item->shift_id) }}" method="post"
                                        style="display: none">
                                        @method('DELETE')
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 700px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('shift.store') }}" method="POST" id="shiftForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="shiftId">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1" id="modalTitle">Tambah Data Shift Kerja</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="shift_name">Nama Shift Kerja</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control @error('shift_name') is-invalid @enderror" name="shift_name"
                                type="text" placeholder="Input Nama Shift" value="{{ old('shift_name') }}" required />
                            @error('shift_name')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <label class="form-label" for="shift_in">Shift In (Waktu Masuk)</label><span
                                    class="mr-4 mb-3" style="color: red">*</span>
                                <input class="form-control @error('shift_in') is-invalid @enderror" name="shift_in"
                                    type="time" placeholder="Input Shift In" value="{{ old('shift_in') }}" required />
                                @error('shift_in')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="shift_out">Shift Out (Waktu Keluar)</label><span
                                    class="mr-4 mb-3" style="color: red">*</span>
                                <input class="form-control @error('shift_out') is-invalid @enderror" name="shift_out"
                                    type="time" placeholder="Input Shift Out" value="{{ old('shift_out') }}" required />
                                @error('shift_out')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <span class="mb-4" style="color: red">*</span> <span class="fs--1">Wajib diisi</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnModal" type="submit">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        var table = $('#datatable').DataTable();
        table.on('click', '.editBtn', function () {
            var id = $(this).val();
            console.log(id)
            $.ajax({
                method: 'get',
                url: 'shift/' + id,
                success: function (response) {
                    if(response == 404){
                        Swal.fire({
                            title: 'Warning!',
                            text: ' Data Tidak Ditemukan!',
                            icon: 'warning',
                            customClass: {
                            confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        })
                    }else{
                        $('#modal-tambah').modal('show')
                        $('#btnModal').text('Edit Data')
                        $('#modalTitle').text('Edit Data');
                        $('#shiftId').val(id);
                        $('input[name="shift_name"]').val(response.shift_name);
                        $('input[name="shift_in"]').val(response.shift_in);
                        $('input[name="shift_out"]').val(response.shift_out);

                        if (response.shift_id) {
                            let url = "{{ route('shift.update', ':id')}}"
                            url = url.replace(':id', id)
                            $('#shiftForm').attr('action', url);
                            $('#shiftForm').attr('method', 'POST');
                            $('#shiftForm').append('<input type="hidden" name="_method" value="PUT">');
                        }
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        });
    });

    function deleteFunction(itemId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.preventDefault()
                document.getElementById(`delete-form-${itemId}`).submit()
            }
        })
    }

    function tambahFunction() {
        $('#shiftId').val("");
        $('input[name="shift_name"]').val("");
        $('input[name="shift_in"]').val("");
        $('input[name="shift_out"]').val("");
        $('#modal-tambah').modal('show');
    }
</script>
@endsection