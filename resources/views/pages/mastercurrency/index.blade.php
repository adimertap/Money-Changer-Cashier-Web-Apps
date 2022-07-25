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
                            <p>Tambah Data Currency Disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#error-modal">Tambah Data</button>
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
                            <h6 class="mb-md-0 mb-lg-2">Total Currency</h6><span
                                class="badge rounded-pill badge-soft-success">Currency</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-3 fw-normal text-700"><span>{{ $jumlah }}</span> Currency</h4>
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
                    <h5 class="mb-0" data-anchor="data-anchor">Data Currency</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Currency</p>
                </div>
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
                                <th class="sort text-center" data-sort="nama_currency">Nama Currency</th>
                                <th class="sort text-center" data-sort="country">Country</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($currency as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="nama_currency">{{ $item->nama_currency }}</td>
                                <td class="country">{{ $item->country }}</td>
                                <td class="text-center">
                                    <button class="btn p-0 ms-2 editCurrencyBtn" value="{{ $item->id_currency }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit"><span class="text-700 fas fa-edit"></span>
                                    </button>
                                    <button class="btn p-0 ms-2 deleteCurrencyBtn" value="{{ $item->id_currency }}"
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
</main>


<div class="modal fade" id="error-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-currency-store') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Edit Data Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form Currency berikut ini</p>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="nama_currency">Nama Currency</label>
                            <input class="form-control @error('nama_currency') is-invalid @enderror"
                                name="nama_currency" type="text" placeholder="Input Nama Currency"
                                value="{{ old('nama_currency') }}" required />
                            @error('nama_currency')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="country">Country</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control @error('country') is-invalid @enderror"
                                name="country" type="text" placeholder="Input Country Currency"
                                value="{{ old('country') }}" required />
                            @error('country')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteCurrency" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ url('owner/delete-currency') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Currency</h4>
                       </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="currency_delete_id" id="id_currency">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Currency ini?
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

<div class="modal fade" id="editCurrency" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/owner/update-currency') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Edit Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <input type="hidden" name="edit_currency_id" id="edit_currency_id">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="nama_currency">Nama Currency</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control nama_currency" id="fnama"
                                name="nama_currency" type="text" placeholder="Input Nama Currency"
                                value="{{ old('nama_currency') }}" required />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="country">Country</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control country" id="fcountry"
                                name="country" type="text" placeholder="Input Country Currency"
                                value="{{ old('country') }}" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Edit Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('.deleteCurrencyBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_currency').val(id)
            $('#deleteCurrency').modal('show');
        })

        var table = $('#datatable').DataTable();

        table.on('click', '.editCurrencyBtn', function () {
            var id = $(this).val();
            $('#edit_currency_id').val(id)

            $tr = $(this).closest('tr');
            if ($($tr).hasClass('clid')) {
                $tr = $tr.prev('.parent')
            }

            var data = table.row($tr).data();
            console.log(data)

            $('#fnama').val(data[1])
            $('#fcountry').val(data[2])
          
            $('#editForm').attr('action', '/owner/master-currency/' + id)
            $('#editCurrency').modal('show');

        })




    })

</script>



@endsection
