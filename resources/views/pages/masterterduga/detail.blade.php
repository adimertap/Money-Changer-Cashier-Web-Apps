@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3 mt-3"><div class="card-header d-flex justify-content-between align-items-center"><div><h5 class="mb-1">Detail Header Terduga</h5><p class="mb-0 fs--1">Tahun {{ $header->tahun ?: '-' }}</p></div><div class="d-flex gap-2"><a class="btn btn-sm btn-success" href="{{ route('master-terduga.export.excel', $header->terduga_header_id) . '?' . http_build_query(request()->only(['search', 'terduga_type'])) }}"><span class="fas fa-file-excel me-1"></span>Excel</a><a class="btn btn-sm btn-danger" href="{{ route('master-terduga.export.pdf', $header->terduga_header_id) . '?' . http_build_query(request()->only(['search', 'terduga_type'])) }}"><span class="fas fa-file-pdf me-1"></span>PDF</a><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#terdugaModal" onclick="tambahTerduga()">Tambah Terduga</button></div></div>
    <div class="card-body"><div class="row g-3 mb-3"><div class="col-md-4"><strong>Tahun</strong><br>{{ $header->tahun ?: '-' }}</div><div class="col-md-4"><strong>Status</strong><br>{{ $header->is_active ? 'Aktif' : 'Nonaktif' }}</div><div class="col-md-4"><strong>Jumlah</strong><br>{{ $header->jumlah ?? $header->terduga->count() }}</div></div>
        <form method="GET" action="{{ route('master-terduga.show', $header->terduga_header_id) }}" class="row g-2 mb-3">
            <div class="col-md-4"><label class="form-label mb-1">Search</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama, alias, tipe, kode densus, WN"></div>
            <div class="col-md-4"><label class="form-label mb-1">Tipe</label><select class="form-select" name="terduga_type"><option value="">Semua Tipe</option>@foreach ($types as $type)<option value="{{ $type }}" {{ request('terduga_type') === $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach</select></div>
            <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary me-2" type="submit">Filter</button><a class="btn btn-secondary" href="{{ route('master-terduga.show', $header->terduga_header_id) }}">Reset</a></div>
        </form>
        <div class="table-responsive scrollbar"><table class="table table-bordered table-striped fs--1 mb-0" id="terdugaDetailTable"><thead class="bg-200 text-900"><tr><th>No.</th><th>Nama</th><th>Alias</th><th>Tipe</th><th>Kode Densus</th><th>Tempat/Tanggal Lahir</th><th>WN</th><th>Actions</th></tr></thead><tbody>@forelse ($header->terduga as $item)<tr><td>{{ $loop->iteration }}.</td><td>{{ $item->name }}</td><td>{{ $item->alias ?: '-' }}</td><td>{{ $item->terduga_type ?: '-' }}</td><td>{{ $item->kode_densus ?: '-' }}</td><td>{{ $item->tempat_lahir ?: '-' }} / {{ $item->tanggal_lahir ?: '-' }}</td><td>{{ $item->wn ?: '-' }}</td><td class="text-center text-nowrap"><button class="btn p-0 detailTerduga" type="button" data-id="{{ $item->terduga_id }}" title="Detail"><span class="fas fa-eye"></span></button><button class="btn p-0 ms-2 editTerduga" type="button" data-id="{{ $item->terduga_id }}" title="Edit"><span class="fas fa-edit"></span></button><button class="btn p-0 ms-2" onclick="hapusDetail({{ $item->terduga_id }})" title="Delete"><span class="fas fa-trash-alt"></span></button><form id="delete-detail-{{ $item->terduga_id }}" action="{{ route('master-terduga.detail.destroy', [$header->terduga_header_id, $item->terduga_id]) }}" method="POST" class="d-none">@csrf @method('DELETE')</form></td></tr>@empty<tr><td colspan="8" class="text-center">Belum ada detail terduga.</td></tr>@endforelse</tbody></table></div>
    </div></div>
    <a href="{{ route('master-terduga.index') }}" class="btn btn-secondary">Kembali</a>
</main>

<div class="modal fade" id="terdugaDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Detail Terduga</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><div class="row g-3">
            <div class="col-md-6"><strong>Nama</strong><p id="detailName" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Alias</strong><p id="detailAlias" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Tipe Terduga</strong><p id="detailType" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Kode Densus</strong><p id="detailDensus" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Warga Negara</strong><p id="detailCountry" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Tempat Lahir</strong><p id="detailBirthPlace" class="text-800 mb-0"></p></div>
            <div class="col-md-6"><strong>Tanggal Lahir</strong><p id="detailBirthDate" class="text-800 mb-0"></p></div>
            <div class="col-12"><strong>Alamat</strong><p id="detailAddress" class="text-800 mb-0"></p></div>
            <div class="col-12"><strong>Deskripsi</strong><p id="detailDescription" class="text-800 mb-0"></p></div>
        </div></div>
        <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button></div>
    </div></div>
</div>

<div class="modal fade" id="terdugaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form id="terdugaForm" method="POST" action="{{ route('master-terduga.detail.store', $header->terduga_header_id) }}">
            @csrf
            <input type="hidden" name="_method" id="terdugaMethod" value="POST">
            <div class="modal-header"><h5 class="modal-title" id="terdugaTitle">Tambah Terduga</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                @include('pages.masterterduga.form')
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit" id="terdugaSubmit">Simpan</button></div>
        </form>
    </div></div>
</div>
<script>
const terdugaStore = @json(route('master-terduga.detail.store', $header->terduga_header_id));
const terdugaUpdate = @json(route('master-terduga.detail.update', [$header->terduga_header_id, ':id']));
const terdugaShow = @json(route('master-terduga.show', $header->terduga_header_id));
$(function () {
    $('#terdugaDetailTable').DataTable();
    if (window.Choices) {
        window.terdugaCountry = new Choices('#terdugaCountry', { searchEnabled: true, shouldSort: false, itemSelectText: '' });
    }
    $('.detailTerduga').on('click', function () {
        const item = @json($header->terduga->keyBy('terduga_id'))[$(this).data('id')];
        $('#detailName').text(item.name || '-'); $('#detailAlias').text(item.alias || '-');
        $('#detailType').text(item.terduga_type || '-'); $('#detailDensus').text(item.kode_densus || '-'); $('#detailCountry').text(item.wn || '-');
        $('#detailBirthPlace').text(item.tempat_lahir || '-'); $('#detailBirthDate').text(item.tanggal_lahir || '-');
        $('#detailAddress').text(item.alamat || '-'); $('#detailDescription').text(item.description || '-');
        const detailModal = document.getElementById('terdugaDetailModal');
        if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(detailModal).show();
        else $('#terdugaDetailModal').modal('show');
    });
    $('.editTerduga').on('click', function () {
        const id = $(this).data('id');
        $.get('{{ url('owner/master-terduga') }}/{{ $header->terduga_header_id }}/detail/' + id + '/edit', function (item) {
            $('#terdugaForm').attr('action', terdugaUpdate.replace(':id', id)); $('#terdugaMethod').val('PUT');
            $('#terdugaTitle').text('Edit Terduga'); $('#terdugaSubmit').text('Edit Data');
            $('#terdugaName').val(item.name); $('#terdugaAlias').val(item.alias); $('#terdugaType').val(item.terduga_type); $('#terdugaKodeDensus').val(item.kode_densus);
            $('#terdugaTempatLahir').val(item.tempat_lahir); $('#terdugaTanggalLahir').val(item.tanggal_lahir);
            $('#terdugaDescription').val(item.description); $('#terdugaAlamat').val(item.alamat);
            $('#terdugaIsClear').val(item.is_clear ? 1 : 0);
            if (window.terdugaCountry) terdugaCountry.setChoiceByValue(item.wn || '');
            const modal = document.getElementById('terdugaModal');
            if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(modal).show();
            else $('#terdugaModal').modal('show');
        });
    });
    @if ($errors->any()) tambahTerduga(false); @endif
});
function tambahTerduga(reset = true) {
    if (reset) {
        $('#terdugaForm')[0].reset();
        if (window.terdugaCountry) terdugaCountry.removeActiveItems();
    }
    $('#terdugaForm').attr('action', terdugaStore); $('#terdugaMethod').val('POST');
    $('#terdugaTitle').text('Tambah Terduga'); $('#terdugaSubmit').text('Simpan');
    const modal = document.getElementById('terdugaModal');
    if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(modal).show();
    else $('#terdugaModal').modal('show');
}
function hapusDetail(id) { Swal.fire({title:'Hapus data terduga?', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, hapus'}).then(r => { if (r.isConfirmed) $('#delete-detail-' + id).submit(); }); }
</script>
@endsection
