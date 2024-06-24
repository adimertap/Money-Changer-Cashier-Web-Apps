@extends('layouts.app')

@section('content')
@if($errors->any())
<script>
    $('#modal-tambah').modal('show');
</script>
@endif
<main>
    <div class="mt-4">
        <h4 class="mb-3">Laporan Absensi {{ $displayText }}</h4>
        <form action="{{ route('jadwal-laporan.index') }}" method="GET">
            <div class="card mb-2 mt-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <select type="text" id="statusFilter" name="statusFilter" class="form-select"
                                    value="{{ old('statusFilter') }}">
                                    <option value="">Filter By Status</option>
                                    <option value="Terlambat">Terlambat</option>
                                    <option value="Pulang Lebih Cepat">Pulang Lebih Cepat</option>
                                    <option value="InComplete">InComplete</option>
                                </select>
                                <label>Status</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <select type="text" id="monthFilter" name="monthFilter" class="form-select"
                                    value="{{ old('monthFilter') }}">
                                    <option value="">Filter By Bulan</option>
                                    <option value="01" {{ old('monthFilter')=='01' ? 'selected' : '' }}>Januari</option>
                                    <option value="02" {{ old('monthFilter')=='02' ? 'selected' : '' }}>Februari</option>
                                    <option value="03" {{ old('monthFilter')=='03' ? 'selected' : '' }}>Maret</option>
                                    <option value="04" {{ old('monthFilter')=='04' ? 'selected' : '' }}>April</option>
                                    <option value="05" {{ old('monthFilter')=='05' ? 'selected' : '' }}>Mei</option>
                                    <option value="06" {{ old('monthFilter')=='06' ? 'selected' : '' }}>Juni</option>
                                    <option value="07" {{ old('monthFilter')=='07' ? 'selected' : '' }}>Juli</option>
                                    <option value="08" {{ old('monthFilter')=='08' ? 'selected' : '' }}>Agustus</option>
                                    <option value="09" {{ old('monthFilter')=='09' ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ old('monthFilter')=='10' ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ old('monthFilter')=='11' ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ old('monthFilter')=='12' ? 'selected' : '' }}>Desember</option>
                                </select>
                                <label>Bulan</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <select type="text" id="yearFilter" name="yearFilter" class="form-select"
                                    value="{{ old('monthFilter') }}">
                                    <option value="">Filter By Tahun</option>
                                    <option value="2020" {{ old('yearFilter')=='2020' ? 'selected' : '' }}>2020</option>
                                    <option value="2021" {{ old('yearFilter')=='2021' ? 'selected' : '' }}>2021</option>
                                    <option value="2022" {{ old('yearFilter')=='2022' ? 'selected' : '' }}>2022</option>
                                    <option value="2023" {{ old('yearFilter')=='2023' ? 'selected' : '' }}>2023</option>
                                    <option value="2024" {{ old('yearFilter')=='2024' ? 'selected' : '' }}>2024</option>
                                    <option value="2025" {{ old('yearFilter')=='2025' ? 'selected' : '' }}>2025</option>
                                    <option value="2026" {{ old('yearFilter')=='2026' ? 'selected' : '' }}>2026</option>
                                    <option value="2027" {{ old('yearFilter')=='2027' ? 'selected' : '' }}>2027</option>
                                    <option value="2028" {{ old('yearFilter')=='2028' ? 'selected' : '' }}>2028</option>
                                    <option value="2029" {{ old('yearFilter')=='2029' ? 'selected' : '' }}>2029</option>
                                    <option value="2030" {{ old('yearFilter')=='2030' ? 'selected' : '' }}>2030</option>
                                </select>
                                <label>Tahun</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start mt-4">
                        <button type="submit" class="btn btn-sm btn-primary me-2">Apply Filter</button>
                        <a href="{{ route('jadwal-laporan.index') }}" type="button" class="btn btn-sm btn-danger">Reset</a>
                    </div>
                </div>
            </div>
        </form>
       

        <div class="card mb-3 mt-3">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0" data-anchor="data-anchor">Laporan Absensi 
                        @if ($selectedMonth)
                        - {{ $selectedMonth }}
                        @endif
                        @if ($selectedYear)
                            {{ $selectedYear }}
                        @endif
                        @if ($selectedStatus)
                        - Status:  {{ $selectedStatus }}
                        @endif
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div id="tableExample"
                    data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs--1 mb-0" id="datatableToday">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th class="sort text-center" data-sort="no">No.</th>
                                    <th class="sort text-center" data-sort="nama">Tanggal</th>
                                    <th class="sort text-center" data-sort="in">Shift</th>
                                    <th class="sort text-center" data-sort="out">In</th>
                                    <th class="sort text-center" data-sort="out">Out</th>
                                    <th class="sort text-center" data-sort="out">Status In</th>
                                    <th class="sort text-center" data-sort="out">Status Out</th>
                                    <th class="sort text-center" data-sort="out">Status</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($jadwal as $item)
                                <tr role="row" class="odd">
                                <th scope="row" class="no fs--1">{{ $loop->iteration + ($jadwal->currentPage() - 1) * $jadwal->perPage() }}.</th>
                                    <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}
                                    </td>
                                    <td class="in">{{ $item->Shift->shift_name }} , Jam {{ $item->Shift->shift_in }} -
                                        {{ $item->Shift->shift_out }}</td>
                                    <td class="text-center">
                                        @if(empty($item->jam_masuk))
                                        -
                                        @else
                                        {{ $item->jam_masuk }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(empty($item->jam_keluar))
                                        -
                                        @else
                                        {{ $item->jam_keluar }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_in == 'Terlambat')
                                        <span class="badge rounded-pill badge-soft-danger">Terlambat</span>
                                        @elseif($item->status_absen_in == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_out == 'Pulang Cepat')
                                        <span class="badge rounded-pill badge-soft-danger">Pulang Cepat</span>
                                        @elseif($item->status_absen_out == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 'X')
                                        <span class="badge rounded-pill badge-soft-warning">Incomplete</span>
                                        @elseif ($item->status == 'T')
                                        <span class="badge rounded-pill badge-soft-info">Proses Tukar</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-success">Complete</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $jadwal->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable();
        var tableToday = $('#datatableToday').DataTable();
    });
</script>
@endsection