@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5 class="mb-0">Data Cabang</h5>
                <button onclick="tambahFunction()" class="btn btn-sm btn-primary">Tambah</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Nama Cabang</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">Latitude</th>
                            <th class="text-center">Longitude</th>
                            <th class="text-center">Radius (m)</th>
                            <th class="text-center">Radius Check</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabang as $item)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}.</th>
                            <td>{{ $item->cabang_name }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->latitude ?? $item->lat ?? '-' }}</td>
                            <td>{{ $item->longitude ?? $item->lng ?? '-' }}</td>
                            <td class="text-center"><span class="badge badge-soft-info">{{ ($item->radius ?? 50) }} m</span></td>
                            <td class="text-center">
                                @if($item->absen_radius_active ?? true)
                                    <span class="badge badge-soft-success">Aktif</span>
                                @else
                                    <span class="badge badge-soft-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input statusRadio" type="radio" id="aktif-{{ $item->cabang_id }}"
                                        name="status-{{ $item->cabang_id }}" value="1" data-id="{{ $item->cabang_id }}"
                                        {{ $item->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="aktif-{{ $item->cabang_id }}">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input statusRadio" type="radio" id="nonaktif-{{ $item->cabang_id }}"
                                        name="status-{{ $item->cabang_id }}" value="0" data-id="{{ $item->cabang_id }}"
                                        {{ $item->is_active ? '' : 'checked' }}>
                                    <label class="form-check-label" for="nonaktif-{{ $item->cabang_id }}">Nonaktif</label>
                                </div>
                            </td>
                            <td class="text-center text-nowrap">
                                <button class="btn p-0 ms-2 editBtn" value="{{ $item->cabang_id }}" type="button" title="Edit Cabang">
                                    <span class="text-700 fas fa-edit"></span>
                                </button>
                                <button class="btn p-0 ms-2" onclick="deleteFunction({{ $item->cabang_id }})" type="button" title="Delete">
                                    <span class="text-700 fas fa-trash-alt"></span>
                                </button>
                                <form id="delete-form-{{ $item->cabang_id }}" action="{{ route('master-cabang.destroy', $item->cabang_id) }}"
                                    method="post" style="display: none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modal-cabang" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 750px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-cabang.store') }}" method="POST" id="cabangForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="edit_id" id="editId" value="{{ old('edit_id') }}">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1" id="modalTitle">Tambah Data Cabang</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <div class="mb-3">
                            <label class="form-label" for="cabang_name">Nama Cabang</label><span style="color: red">*</span>
                            <input class="form-control @error('cabang_name') is-invalid @enderror" id="cabang_name" name="cabang_name"
                                type="text" maxlength="100" placeholder="Input Nama Cabang" value="{{ old('cabang_name') }}" required />
                            @error('cabang_name')
                            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="alamat">Alamat</label>
                            <input class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                type="text" maxlength="100" placeholder="Input Alamat" value="{{ old('alamat') }}" />
                            @error('alamat')
                            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="latitude">Latitude</label>
                                <input class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude"
                                    type="number" step="any" placeholder="-8.701647" value="{{ old('latitude', old('lat')) }}" />
                                @error('latitude')
                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="longitude">Longitude</label>
                                <input class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude"
                                    type="number" step="any" placeholder="115.166375" value="{{ old('longitude', old('lng')) }}" />
                                @error('longitude')
                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label" for="radius">Radius Absen (Meter)</label>
                                <input class="form-control @error('radius') is-invalid @enderror" id="radius" name="radius"
                                    type="number" step="1" min="1" placeholder="50" value="{{ old('radius', 50) }}" />
                                <span class="fs--2 text-muted">Jarak radius dalam meter (default: 50)</span>
                                @error('radius')
                                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Pengecekan Radius Absensi (Jadwal User) <span style="color: red">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="absen_radius_active" id="radiusCheckActive" value="1" checked required>
                                <label class="form-check-label" for="radiusCheckActive">Aktif (Wajib dalam radius)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="absen_radius_active" id="radiusCheckInactive" value="0">
                                <label class="form-check-label" for="radiusCheckInactive">Nonaktif (Bebas lokasi)</label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <span style="color: red">*</span> <span class="fs--1">Wajib diisi</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnModal" type="submit">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const storeUrl = "{{ route('master-cabang.store') }}";
    const itemUrl = "{{ route('master-cabang.show', ':id') }}";
    const statusUrl = "{{ route('master-cabang.status', ':id') }}";
    const csrf = "{{ csrf_token() }}";

    $(document).ready(function () {
        var table = $('#datatable').DataTable();

        @if($errors->any())
        if ($('#editId').val()) {
            setEditMode($('#editId').val());
        }
        $('#modal-cabang').modal('show');
        @endif

        table.on('click', '.editBtn', function () {
            var id = $(this).val();
            $.get(itemUrl.replace(':id', id), function (response) {
                setEditMode(id);
                $('#cabangForm .is-invalid').removeClass('is-invalid');
                $('#cabang_name').val(response.cabang_name);
                $('#alamat').val(response.alamat);
                $('#latitude').val(response.latitude ?? response.lat ?? '');
                $('#longitude').val(response.longitude ?? response.lng ?? '');
                $('#radius').val(response.radius ?? 50);
                if (response.absen_radius_active == 0 || response.absen_radius_active === false) {
                    $('#radiusCheckInactive').prop('checked', true);
                } else {
                    $('#radiusCheckActive').prop('checked', true);
                }
                $('#modal-cabang').modal('show');
            }).fail(function () {
                Swal.fire('Warning!', 'Data Tidak Ditemukan!', 'warning');
            });
        });

        table.on('change', '.statusRadio', function () {
            var radio = $(this);
            $.ajax({
                method: 'POST',
                url: statusUrl.replace(':id', radio.data('id')),
                data: { _token: csrf, _method: 'PATCH', is_active: radio.val() },
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Status diperbarui', timer: 1200, showConfirmButton: false });
                },
                error: function () {
                    // kembalikan pilihan radio sebelumnya jika gagal
                    $('input[name="' + radio.attr('name') + '"][value="' + (radio.val() == 1 ? 0 : 1) + '"]').prop('checked', true);
                    Swal.fire('Gagal', 'Status gagal diperbarui', 'error');
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
                document.getElementById(`delete-form-${itemId}`).submit();
            }
        });
    }

    function setEditMode(id) {
        $('#modalTitle').text('Edit Data Cabang');
        $('#btnModal').text('Edit Data');
        $('#cabangForm').attr('action', itemUrl.replace(':id', id));
        $('#formMethod').val('PUT');
        $('#editId').val(id);
    }

    function tambahFunction() {
        $('#modalTitle').text('Tambah Data Cabang');
        $('#btnModal').text('Tambah Data');
        $('#cabangForm').attr('action', storeUrl);
        $('#formMethod').val('POST');
        $('#cabangForm')[0].reset();
        $('#cabangForm input[type=text], #cabangForm input[type=number]').val('');
        $('#radius').val('50');
        $('#radiusCheckActive').prop('checked', true);
        $('#editId').val('');
        $('#cabangForm .is-invalid').removeClass('is-invalid');
        $('#modal-cabang').modal('show');
    }
</script>
@endsection
