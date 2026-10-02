@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><h5 class="mb-1">Data Header Terduga</h5><p class="mb-0 fs--1">Master data daftar terduga berdasarkan tahun</p></div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-secondary" type="button" onclick="uploadTerduga()">Upload Excel</button>
                <button class="btn btn-sm btn-primary" type="button" onclick="tambahHeader()">Tambah</button>
            </div>
        </div>
        <div class="card-body"><div class="table-responsive scrollbar">
            <table class="table table-bordered table-striped fs--1 mb-0" id="terdugaHeaderTable">
                <thead class="bg-200 text-900"><tr><th>No.</th><th>Tahun</th><th>Status</th><th>Jumlah</th><th>File</th><th>Actions</th></tr></thead>
                <tbody>@forelse ($headers as $item)<tr>
                    <td>{{ $loop->iteration }}.</td><td>{{ $item->tahun ?: '-' }}</td>
                    <td>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td><td>{{ $item->terduga_count }}</td><td>{{ $item->file_name ?: '-' }}</td>
                    <td class="text-center text-nowrap">
                        <a class="btn p-0" href="{{ route('master-terduga.show', $item->terduga_header_id) }}" title="Detail"><span class="fas fa-eye"></span></a>
                        <button class="btn p-0 ms-2 editHeader" value="{{ $item->terduga_header_id }}" title="Edit"><span class="fas fa-edit"></span></button>
                        <button class="btn p-0 ms-2" onclick="hapusHeader({{ $item->terduga_header_id }})" title="Delete"><span class="fas fa-trash-alt"></span></button>
                        <form id="delete-header-{{ $item->terduga_header_id }}" action="{{ route('master-terduga.destroy', $item->terduga_header_id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                    </td>
                </tr>@empty<tr><td colspan="6" class="text-center">Belum ada header terduga.</td></tr>@endforelse</tbody>
            </table>
        </div></div>
    </div>
</main>

<div class="modal fade" id="headerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<form id="headerForm" method="POST" action="{{ route('master-terduga.store') }}">@csrf
<input type="hidden" name="_method" id="headerMethod" value="POST"><div class="modal-header"><h5 class="modal-title" id="headerTitle">Tambah Header Terduga</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><div class="row g-3"><div class="col-md-6"><label class="form-label">Tahun <span class="text-danger">*</span></label><input class="form-control" name="tahun" id="headerTahun" type="number" min="1900" max="2100" required></div><div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="is_active" id="headerActive"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div></div></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit" id="headerSubmit">Simpan</button></div></form>
</div></div></div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<form method="POST" action="{{ route('master-terduga.upload') }}" enctype="multipart/form-data">@csrf
<div class="modal-header"><h5 class="modal-title">Upload Data Terduga</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><div class="mb-3"><label class="form-label">Tahun <span class="text-danger">*</span></label><input class="form-control" name="tahun" type="number" min="1900" max="2100" value="{{ date('Y') }}" required></div><div><label class="form-label">File Excel <span class="text-danger">*</span></label><input class="form-control" name="file" type="file" accept=".xls,.xlsx,.csv" required><small class="text-muted">Format .xls, .xlsx, atau .csv, maksimal 10 MB.</small></div></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Upload</button></div></form>
</div></div></div>
<script>
const headerStore = @json(route('master-terduga.store'));
const headerEdit = @json(route('master-terduga.edit', ':id'));
const headerUpdate = @json(route('master-terduga.update', ':id'));
$(function () {
    $('#terdugaHeaderTable').DataTable();
    $('.editHeader').on('click', function () {
        const id = $(this).val();
        $.get(headerEdit.replace(':id', id), function (item) {
            $('#headerForm').attr('action', headerUpdate.replace(':id', id)); $('#headerMethod').val('PUT');
            $('#headerTitle').text('Edit Header Terduga'); $('#headerSubmit').text('Edit Data');
            $('#headerTahun').val(item.tahun); $('#headerActive').val(item.is_active ? 1 : 0);
            $('#headerModal').modal('show');
        });
    });
});
function tambahHeader() { $('#headerForm')[0].reset(); $('#headerTahun').val('{{ date('Y') }}'); $('#headerForm').attr('action', headerStore); $('#headerMethod').val('POST'); $('#headerTitle').text('Tambah Header Terduga'); $('#headerSubmit').text('Simpan'); $('#headerModal').modal('show'); }
function uploadTerduga() { $('#uploadModal').modal('show'); }
function hapusHeader(id) { Swal.fire({title:'Hapus header dan semua detail?', text:'Seluruh data terduga di dalamnya ikut terhapus.', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, hapus'}).then(r => { if (r.isConfirmed) $('#delete-header-' + id).submit(); }); }
</script>
@endsection
