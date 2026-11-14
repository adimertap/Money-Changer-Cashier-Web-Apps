@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3 mt-3">
        <div class="card-header">
            <div class="row flex-between-center g-2">
                <div class="col-auto">
                    <h5 class="mb-0">Perbandingan Kurs Antar Cabang</h5>
                    <p class="mb-0 pt-1 fs--1 text-600">Edit nilai kurs per cabang langsung pada tabel di bawah ini</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('master-currency') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-bordered table-striped fs--1 mb-0" id="datatable-perbandingan">
                    <thead class="bg-200 text-900">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Nama Currency</th>
                            <th class="text-center">Jenis</th>
                            @foreach ($cabangs as $cabang)
                            <th class="text-center">Kurs {{ $cabang->cabang_name }}</th>
                            @endforeach
                            <th class="text-center">Keterangan</th>
                            <th class="text-center">Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr>
                            <th scope="row" class="text-center">{{ $loop->iteration }}.</th>
                            <td class="fw-semibold">{{ $row['nama_currency'] }}</td>
                            <td class="text-center">
                                @if ($row['jenis_kurs'] == 'Lembar')
                                <span class="badge badge-soft-primary">{{ $row['jenis_kurs'] }}</span>
                                @else
                                <span class="badge badge-soft-warning">{{ $row['jenis_kurs'] }}</span>
                                @endif
                            </td>
                            @foreach ($cabangs as $cabang)
                            @php
                                $currencyItem = $row['by_cabang']->get($cabang->cabang_id);
                            @endphp
                            <td class="text-center" style="min-width: 140px;">
                                @if ($currencyItem)
                                <input class="form-control form-control-sm text-end kurs_edit"
                                    type="text"
                                    placeholder="Nilai Kurs"
                                    data-id="{{ $currencyItem->id_currency }}"
                                    value="{{ number_format($currencyItem->nilai_kurs, 2, ',', '.') }}" />
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endforeach
                            <td>{{ $row['keterangan'] ?: '-' }}</td>
                            <td class="text-center">{{ $row['urutan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function () {
        $('#datatable-perbandingan').DataTable({
            pageLength: 25
        });

        $(document).on('change', '.kurs_edit', function () {
            var $input = $(this);
            var id = $input.data('id');
            var raw = ($input.val() || '').toString().trim();

            var normalized = raw.replace(/\./g, '').replace(',', '.').replace(/[^\d.]/g, '');
            var numericVal = parseFloat(normalized);
            if (isNaN(numericVal)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Format nilai kurs tidak valid'
                });
                return;
            }

            $input.val(numericVal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $.ajax({
                method: 'post',
                url: '/owner/update-nilai-kurs',
                data: {
                    id: id,
                    nilai_kurs: numericVal,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Berhasil Mengupdate Kurs ' + (response.nama_currency || '') + ' pada cabang ' + (response.cabang_name || '-')
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error! Tidak Berhasil Mengupdate Kurs'
                    });
                }
            });
        });
    });
</script>
@endsection

