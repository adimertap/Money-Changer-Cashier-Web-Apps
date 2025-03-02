@extends('layouts.app')

@section('content')

<main>
    <div class="row g-3 mb-3 mt-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-12 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Download Report!</h5>
                            <p>Rekapan Transaksi Hari Ini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0" type="button" data-bs-toggle="modal" data-bs-target="#modalfilter"><span class="fas fa-arrow-down me-1"> </span>Download Laporan Hari Ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-12 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Valas Report!</h5>
                            <p>Lihat Cepat Valas Report disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-primary btn-sm me-1 mb-2 mb-sm-0" type="button" id="btnreport" data-bs-toggle="modal" data-bs-target="#modalreport">Valas Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center">
                        <div class="col d-md-flex d-lg-block flex-between-center">
                            <h6 class="mb-md-0 mb-lg-2">Total Transaksi Hari Ini</h6>
                            <span class="badge rounded-pill badge-soft-success">Rp. {{ number_format($total_transaksi)}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link active" href="{{ route('transaksi.index') }}">Transaksi Beli Hari Ini</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transaksi-jual.index') }}">Transaksi Jual Hari Ini</a></li>
    </ul>
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Rekapan Data Transaksi Customer Anda Hari Ini</h5>
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
                                <th class="sort text-center fs--1" data-sort="no">No.</th>
                                <th class="sort text-center fs--1" data-sort="pegawai">Pegawai</th>
                                <th class="sort text-center fs--1" data-sort="tanggal_transaksi">Tanggal & Waktu</th>
                                <th class="sort text-center fs--1" data-sort="kode_transaksi">Kode Transaksi</th>
                                <th class="sort text-center fs--1" data-sort="nama_customer">Cust</th>
                                <th class="sort text-center fs--1" data-sort="nomor_passport">Passport</th>
                                <th class="sort text-center fs--1" data-sort="total">Total Transaksi</th>
                                <th class="sort text-center fs--1" data-sort="print">Print</th>
                                <th class="sort text-center fs--1" data-sort="status">Status</th>
                                <th class="text-center" style="width: 60px">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($transaksi as $index => $item)
                            <tr role="row" class="odd">
                                <td class="text-center">{{ $transaksi->firstItem() + $index }}</td>
                                <td class="text-start pegawai fs--1">{{ $item->Pegawai->name }}</td>
                                <td class="text-center tanggal_transaksi fs--1">{{ date('d-M-Y',
                                    strtotime($item->tanggal_transaksi)) }}, {{ date('H:i:s',
                                    strtotime($item->created_at)) }}</td>
                                <td class="text-center kode_transaksi fs--1">{{ $item->kode_transaksi }}</td>
                                <td class="text-center nama_customer fs--1">{{ $item->nama_customer }}</td>
                                <td class="text-center nomor_passport fs--1">{{ $item->nomor_passport }}</td>
                                <td class="text-center total text-center fs--1">Rp. {{ number_format($item->total, 0,
                                    ',', '.') }}</td>
                                <td class="text-center status text-center fs--1">
                                    <span class="badge rounded-pill badge-soft-success">Lunas</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('cetak', $item->id_transaksi) }}" target="_blank" class="btn p-0" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><span class="text-700 fas fa-print"></span>
                                    </a>
                                </td>
                                <td class="text-center fs--1">
                                    <a href="{{ route('transaksi.show', $item->id_transaksi) }}" class="btn p-0" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a>
                                    <a href="{{ route('transaksi.edit', $item->id_transaksi) }}" class="btn p-0" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><span class="text-700 fas fa-edit"></span>
                                    </a>
                                    <button class="btn p-0 deleteModalBtn" value="{{ $item->id_transaksi }}" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><span class="text-700 fas fa-trash-alt"></span>
                                    </button>
                                </td>
                            </tr>

                            @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $transaksi->appends(['per_page' => request('per_page')])->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalreport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light rounded-top-lg py-3 ps-4 pe-6">
                    <h4 class="mb-1" id="staticBackdropLabel">Valas Report per {{ $today }}</h4>
                    <p class="fs--2 mb-0">Pilih pegawai pada radio button untuk melihat data report perpegawai</p>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url('/transaksi') }}" method="GET">
                            <label for="filterData">Filter Berdasarkan Pegawai</label>
                            <div class="row mb-3">
                                <div class="col-4">
                                    <select class="form-select form-select-sm" id="filterData" size="1" name="filterData">
                                        <option value="">Report Keseluruhan</option>
                                        @foreach ($pegawai as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-sm btn-primary" type="submit">Filter</button>
                                </div>
                            </div>
                        </form>


                        <div class="card p-3 mb-5">
                            <div id="tableExample" data-list='{"valueNames":["no","nama_currency","jumlah","nilai","grand"],"page":20,"pagination":true}'>
                                <div class="table-responsive scrollbar">
                                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatableReport">
                                        <thead class="bg-200 text-900">
                                            <tr>
                                                <th class="sort text-center" data-sort="no">No.</th>
                                                <th class="sort text-center" data-sort="nama_currency">Currency</th>
                                                <th class="sort text-center" data-sort="jumlah">Jumlah</th>
                                                <th class="sort text-center" data-sort="nilai">Kurs(Rp)</th>
                                                <th class="sort text-center" data-sort="grand">Total</th>
                                            </tr>
                                        </thead>
                                        @php
                                        $total_debit = 0;
                                        foreach ($report as $key => $item) {
                                        $total_debit = $total_debit + ($item->nilai_kurs*$item->jumlah_tukar);
                                        }

                                        @endphp
                                        <tbody class="list" id="tess">
                                            @forelse ($report as $item)
                                            <tr role="row" class="odd">
                                                <th scope="row" class="no" style="font-size: 14px">{{ $loop->iteration}}.</th>
                                                <td class="text-start nama_currency" style="font-size: 15px">{{ $item->nama_kurs }}</td>
                                                <td class="text-start jumlah" style="font-size: 15px">{{ $item->jumlah_tukar }}</td>
                                                <td class="text-center nilai text-center bg-soft-primary" style="font-size: 15px">Rp. {{
                                                number_format($item->nilai_kurs, 2, ',', '.') }}</td>
                                                <td class="text-center grand text-center" style="font-size: 15px">Rp. {{
                                                number_format($item->nilai_kurs*$item->jumlah_tukar, 0, ',', '.') }}
                                                </td>
                                            </tr>

                                            @empty

                                            @endforelse



                                        </tbody>
                                        <tr>
                                            <th class="text-center bg-soft-primary" colspan="4" style="font-size: 17px">Total Tercatat / Grand Total</th>
                                            <th class="text-center bg-soft-primary fw-bold" colspan="1" style="font-size: 18px">{{ number_format($total_debit,2,',','.') }}
                                            </th>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>


                        <div class="card p-3">
                            <h4 class="mb-2">Detail Valas</h4>
                            <div id="tableExample" data-list='{"valueNames":["no","nama_currency","jumlah","nilai","grand"],"page":20,"pagination":true}'>
                                <div class="table-responsive scrollbar">
                                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatableReport2">
                                        <thead class="bg-200 text-900">
                                            <tr>
                                                <th class="sort text-center" data-sort="no">No.</th>
                                                <th class="sort text-center" data-sort="nama_currency">Currency</th>
                                                <th class="sort text-center" data-sort="jumlah">Jumlah</th>
                                                <th class="sort text-center" data-sort="tengah">Kurs Tengah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="tess">
                                            @forelse ($valas as $item)
                                            <tr role="row" class="odd">
                                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                                <td class="text-start nama_currency">{{ $item->nama_kurs }} / {{ $item->jenis }}</td>
                                                @if($item->jenis == 'Lembar')
                                                <td class="text-center jumlah">{{ $item->jumlah }} Lembar</td>
                                                @else
                                                <td class="text-center jumlah">{{ $item->jumlah }} Coins</td>
                                                @endif
                                                <td class="text-center tengah">Rp.{{ number_format($item->total/$item->jumlah, 2,',','.' ) }}</td>
                                            </tr>

                                            @empty

                                            @endforelse



                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form action="{{ url('/delete-transaksi') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Transaksi</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="transaksi_id" id="id_transaksi">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Transaksi ini?
                                            Dengan Menghapus Data Transaksi Modal akan Bertambah Sesuai dengan Total
                                            Transaksi</p>
                                        <div class="mb-3">
                                            <label class="col-form-label" for="keterangan">Keterangan
                                                Penghapusan</label><span class="mr-4 mb-3" style="color: red">*</span>
                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                            <span class="mb-1" style="color: red">*</span> <span class="fs--1">Wajib
                                                diisi</span>
                                        </div>
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

<div class="modal fade" id="modalfilter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('export-dokumen-harian') }}" id="form2">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Filter Data untuk Export</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1 mb-3">Filter Data Berdasarkan Inputan</p>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault1" type="radio" value="excel" name="radio_input" checked />
                                    <label class="form-check-label" for="flexRadioDefault1">Export Excel</label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault2" type="radio" value="pdf" name="radio_input" />
                                    <label class="form-check-label" for="flexRadioDefault2">Export PDF</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <label for="currency">Filter by Currency</label>
                                <select class="form-select js-choice" id="id_currency" name="id_currency" data-options='{"removeItemButton":true,"placeholder":true, "shouldSort":false}'>
                                    <option value="">Pilih Currency</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}, {{
                                        $item->jenis_kurs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <label for="currency">Filter by Pegawai</label>
                                <select class="form-select js-choice" id="pegawai" name="id_pegawai" data-options='{"removeItemButton":true,"placeholder":true}'>
                                    <option value="">Pilih Pegawai</option>
                                    @foreach ($pegawai as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Export Data </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty(Session::get('modal-tes')) && Session::get('modal-tes') == 5)
<script>
    $(function() {
        $('#btnreport').trigger("click");
    });

</script>
@endif

<script>
    //
    $(document).ready(function() {
        $('.deleteModalBtn').click(function(e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_transaksi').val(id)
            $('#deleteModal').modal('show');
        })

        var table = $('#example').DataTable({
            paging: false,
            // scrollY: 400
        });
        $('#datatableReport').DataTable();
        $('#datatableReport2').DataTable();

        var url = (window.location).href;
        var name = url.substring(url.lastIndexOf('=') + 1);


        $('#perPageSelect').on('change', function () {
            var perPage = $(this).val();
            window.location.href = '?per_page=' + perPage;
        });



    })

</script>



@endsection
