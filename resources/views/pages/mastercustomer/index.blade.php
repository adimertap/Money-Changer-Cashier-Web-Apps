@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Data Customer</h5>
                <p class="mb-0 fs--1">Master data customer</p>
            </div>
            <button class="btn btn-sm btn-primary" type="button" onclick="tambahCustomer()">Tambah</button>
        </div>
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-bordered table-striped fs--1 mb-0" id="customerTable">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th>No.</th><th>Nama</th><th>Country</th><th>Passport</th><th>Cabang Terdaftar</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer as $item)
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>
                                <a href="{{ route('master-customer.show', $item->getKey()) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $item->name }}
                                </a>
                                @if(!empty($item->npwp) || !empty($item->job) || !empty($item->supporting_document_file))
                                    <span class="badge bg-success fs--2 ms-1" title="Dokumen CDD Terdata"><i class="fas fa-shield-alt me-1"></i>CDD</span>
                                @endif
                            </td>
                            <td>{{ $item->country ?: '-' }}</td>
                            <td>{{ $item->passport ?: '-' }}</td>
                            <td>{{ optional($item->cabang)->cabang_name ?: '-' }}</td>
                            <td class="text-center">
                                @if ($item->getKey() !== null)
                                    <form method="POST" action="{{ route('master-customer.status', ['id' => $item->getKey()]) }}" class="d-inline-flex gap-2 align-items-center">
                                        @csrf @method('PATCH')
                                        <label class="form-check-label small">
                                            <input class="form-check-input" type="radio" name="is_active" value="1" onchange="this.form.submit()" {{ (bool) $item->is_active ? 'checked' : '' }}>
                                            Aktif
                                        </label>
                                        <label class="form-check-label small">
                                            <input class="form-check-input" type="radio" name="is_active" value="0" onchange="this.form.submit()" {{ !(bool) $item->is_active ? 'checked' : '' }}>
                                            Nonaktif
                                        </label>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($item->getKey() !== null)
                                    <a href="{{ route('master-customer.show', $item->getKey()) }}" class="btn btn-sm btn-outline-info me-1" title="Detail Customer">
                                        <span class="fas fa-eye me-1"></span>Detail
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary editCustomer" value="{{ $item->getKey() }}" title="Edit" type="button">
                                        <span class="fas fa-edit me-1"></span>Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger ms-1" type="button" onclick="hapusCustomer({{ $item->getKey() }})" title="Delete">
                                        <span class="fas fa-trash-alt me-1"></span>Delete
                                    </button>
                                    <form id="delete-customer-{{ $item->getKey() }}" action="{{ route('master-customer.destroy', ['master_customer' => $item->getKey()]) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">Belum ada data customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="customerForm" method="POST" action="{{ route('master-customer.store') }}">
                @csrf
                <input type="hidden" name="_method" id="customerMethod" value="POST">
                <input type="hidden" name="edit_id" id="customerEditId">
                <div class="modal-header"><h5 class="modal-title" id="customerTitle">Tambah Customer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" name="name" id="customerName" required value="{{ old('name') }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select @error('country') is-invalid @enderror" name="country" id="customerCountry" data-country-select required>
                                <option value="">Pilih Country</option>
                                @foreach ($countries as $code => $countryName)
                                <option value="{{ $countryName }}" {{ old('country') === $countryName ? 'selected' : '' }}>{{ $countryName }}</option>
                                @endforeach
                            </select>
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6"><label class="form-label">Passport</label><input class="form-control" name="passport" id="customerPassport" value="{{ old('passport') }}"></div>
                        <div class="col-md-6"><label class="form-label">NIK</label><input class="form-control" name="nik" id="customerNik" value="{{ old('nik') }}"></div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Status</label>
                            <label class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_active" value="1" checked> Aktif</label>
                            <label class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_active" value="0"> Nonaktif</label>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cabang Terdaftar</label>
                            <select class="form-select" name="cabang_terdaftar" id="customerCabang"><option value="">Pilih Cabang</option>@foreach ($cabang as $c)<option value="{{ $c->cabang_id }}">{{ $c->cabang_name }}</option>@endforeach</select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit" id="customerSubmit">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    let countryChoices;
    const customerStore = @json(route('master-customer.store'));
    const customerShow = @json(route('master-customer.show', ':id'));
    const customerUpdate = @json(route('master-customer.update', ':id'));
    $(function () {
        $('#customerTable').DataTable();
        countryChoices = new Choices('#customerCountry', {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: ''
        });
        $('.editCustomer').on('click', function () {
            const id = $(this).val();
            $.get(customerShow.replace(':id', id), function (item) {
                $('#customerForm').attr('action', customerUpdate.replace(':id', id));
                $('#customerMethod').val('PUT'); $('#customerEditId').val(id);
                $('#customerTitle').text('Edit Customer'); $('#customerSubmit').text('Edit Data');
                $('#customerName').val(item.name); countryChoices.setChoiceByValue(item.country || '');
                $('#customerPassport').val(item.passport);
                $('#customerNik').val(item.nik);
                $('input[name="is_active"][value="' + (item.is_active ? '1' : '0') + '"]').prop('checked', true);
                $('#customerCabang').val(item.cabang_terdaftar);
                $('#customerModal').modal('show');
            });
        });
        @if ($errors->any()) tambahCustomer(false); @endif
    });
    function tambahCustomer(reset = true) {
        if (reset) {
            $('#customerForm')[0].reset();
            if (countryChoices && typeof countryChoices.removeActiveItems === 'function') {
                countryChoices.removeActiveItems();
            }
            $('#customerCountry').val('');
        }
        $('#customerForm').attr('action', customerStore);
        $('#customerMethod').val('POST'); $('#customerEditId').val('');
        $('#customerTitle').text('Tambah Customer'); $('#customerSubmit').text('Simpan'); $('#customerModal').modal('show');
    }
    function hapusCustomer(id) {
        Swal.fire({title: 'Hapus customer?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus'}).then(result => {
            if (result.isConfirmed) $('#delete-customer-' + id).submit();
        });
    }
</script>
@endsection
