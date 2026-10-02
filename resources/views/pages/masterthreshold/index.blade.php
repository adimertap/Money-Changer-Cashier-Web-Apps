@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3 mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Master Preset Batas Atas Transaksi</h5>
                <p class="mb-0 fs--1">Pengaturan batas akumulasi passport rolling 30 hari</p>
            </div>
            <button class="btn btn-sm btn-primary" type="button" onclick="tambahThreshold()">Tambah</button>
        </div>
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-bordered table-striped fs--1 mb-0" id="thresholdTable">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th>No.</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Currency</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($thresholds as $item)
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ $item->start_date ? date('d-m-Y H:i', strtotime($item->start_date)) : '-' }}</td>
                            <td>{{ $item->end_date ? date('d-m-Y H:i', strtotime($item->end_date)) : '-' }}</td>
                            <td>{{ optional($currencies->firstWhere('id_currency', $item->currency_id))->nama_currency ?: '-' }}</td>
                            <td>{{ number_format($item->nominal, 2, ',', '.') }}</td>
                            <td class="text-nowrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input thresholdStatus" type="radio" name="threshold-status-{{ $item->threshold_id }}" value="1" data-id="{{ $item->threshold_id }}" {{ $item->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input thresholdStatus" type="radio" name="threshold-status-{{ $item->threshold_id }}" value="0" data-id="{{ $item->threshold_id }}" {{ $item->is_active ? '' : 'checked' }}>
                                    <label class="form-check-label">Nonaktif</label>
                                </div>
                            </td>
                            <td class="text-center text-nowrap">
                                <button class="btn p-0 editThreshold" type="button" value="{{ $item->threshold_id }}" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </button>
                                <button class="btn p-0 ms-2" type="button" onclick="hapusThreshold({{ $item->threshold_id }})" title="Delete">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                                <form id="delete-threshold-{{ $item->threshold_id }}" action="{{ route('master-threshold.destroy', $item->threshold_id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center">Belum ada preset batas atas transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="thresholdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="thresholdForm" method="POST" action="{{ route('master-threshold.store') }}">
                @csrf
                <input type="hidden" name="_method" id="thresholdMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="thresholdTitle">Tambah Preset Batas Atas</h5>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdBulan">Bulan <span class="text-danger">*</span></label>
                            <select class="form-select" name="bulan" id="thresholdBulan" required>
                                <option value="">Pilih Bulan</option>
                                @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                                <option value="{{ $bulan }}">{{ $bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdTahun">Tahun <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" name="tahun" id="thresholdTahun" min="1900" max="2100" maxlength="4" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdStart">Start Date <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="start_date" id="thresholdStart" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdEnd">End Date <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="end_date" id="thresholdEnd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdCurrency">Currency <span class="text-danger">*</span></label>
                            <select class="form-select" name="currency_id" id="thresholdCurrency" required>
                                <option value="">Pilih Currency</option>
                                @foreach ($currencies as $currency)
                                <option value="{{ $currency->id_currency }}">{{ $currency->nama_currency }}{{ $currency->country ? ' - ' . $currency->country : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="thresholdNominal">Nominal Limit Rolling 30 Hari <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" name="nominal" id="thresholdNominal" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="thresholdActive" value="1" checked required>
                                <label class="form-check-label" for="thresholdActive">Aktif</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_active" id="thresholdInactive" value="0">
                                <label class="form-check-label" for="thresholdInactive">Nonaktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="thresholdSubmit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const thresholdStore = @json(route('master-threshold.store'));
const thresholdShow = @json(route('master-threshold.show', ':id'));
const thresholdUpdate = @json(route('master-threshold.update', ':id'));
const thresholdStatus = @json(route('master-threshold.status', ':id'));
const thresholdYear = '{{ date('Y') }}';
const thresholdCsrf = '{{ csrf_token() }}';

$(function () {
    $('#thresholdTable').DataTable();

    $('.thresholdStatus').on('change', function () {
        const radio = $(this);
        $.ajax({
            url: thresholdStatus.replace(':id', radio.data('id')),
            method: 'POST',
            data: { _token: thresholdCsrf, _method: 'PATCH', is_active: radio.val() },
            success: function () {
                Swal.fire({ icon: 'success', title: 'Status diperbarui', timer: 1200, showConfirmButton: false });
            },
            error: function () {
                $('input[name="' + radio.attr('name') + '"][value="' + (radio.val() == 1 ? 0 : 1) + '"]').prop('checked', true);
                Swal.fire('Gagal', 'Status gagal diperbarui', 'error');
            }
        });
    });

    $('.editThreshold').on('click', function () {
        const id = $(this).val();
        $.get(thresholdShow.replace(':id', id), function (item) {
            $('#thresholdForm').attr('action', thresholdUpdate.replace(':id', id));
            $('#thresholdMethod').val('PUT');
            $('#thresholdTitle').text('Edit Preset Batas Atas');
            $('#thresholdSubmit').text('Edit Data');
            $('#thresholdBulan').val(item.bulan);
            $('#thresholdTahun').val(item.tahun);
            $('#thresholdStart').val(toDateInput(item.start_date));
            $('#thresholdEnd').val(toDateInput(item.end_date));
            $('#thresholdCurrency').val(item.currency_id);
            $('#thresholdNominal').val(item.nominal);
            $('input[name="is_active"][value="' + (item.is_active ? 1 : 0) + '"]').prop('checked', true);
            $('#thresholdModal').modal('show');
        });
    });
});

function toDateInput(value) {
    if (!value) return '';
    return value.substring(0, 10);
}

function tambahThreshold() {
    $('#thresholdForm')[0].reset();
    $('#thresholdForm').attr('action', thresholdStore);
    $('#thresholdMethod').val('POST');
    $('#thresholdTitle').text('Tambah Preset Batas Atas');
    $('#thresholdSubmit').text('Simpan');
    $('#thresholdTahun').val(thresholdYear);
    $('#thresholdCurrency').val('');
    $('#thresholdActive').prop('checked', true);
    $('#thresholdInactive').prop('checked', false);
    $('#thresholdModal').modal('show');
}

function hapusThreshold(id) {
    Swal.fire({
        title: 'Hapus preset ini?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (result.isConfirmed) {
            $('#delete-threshold-' + id).submit();
        }
    });
}
</script>
@endsection
