@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
            style="background-image:url(/../falcon/assets/img/icons/spot-illustrations/corner-4.png);"></div>
        <div class="card-body position-relative">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Jurnal Debit Kredit</h5>
                    <p class="mt-2">Export Jurnal</p>
                    <hr>
                    <button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0" type="button" data-bs-toggle="modal"
                        data-bs-target="#modalfilter"><span class="fas fa-arrow-down me-1"> </span>Download Laporan Jurnal
                        (.excel)/(.pdf)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","pegawai","tanggal_transaksi","kode_transaksi","total"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center fs--1" data-sort="no">No.</th>
                                <th class="sort text-center fs--1" data-sort="tanggal">Tanggal</th>
                                <th class="sort text-center fs--1" data-sort="jenis">Jenis</th>
                                <th class="sort text-center fs--1" data-sort="tanggal_transaksi">Currency</th>
                                <th class="sort text-center fs--1" data-sort="kode_transaksi">Jumlah Tukar</th>
                                <th class="sort text-center fs--1" data-sort="total">Kurs</th>
                                <th class="sort text-center fs--1" data-sort="status">Debit</th>
                                <th class="sort text-center fs--1" data-sort="status">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($jurnal as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no fs--1">{{ $loop->iteration}}.</th>
                                <td class="text-start tanggal fs--1">{{ date('d-M-Y H:i:s', strtotime($item->updated_at)) }}</td>
                                @if ($item->jenis_jurnal == 'Debit')
                                    <td class="text-center text-center fs--1 jenis">Jual</td>
                                    <td class="text-center text-center fs--1">{{ $item->Currency->nama_currency }}</td>
                                    <td class="text-center text-center fs--1">{{ $item->jumlah_tukar }}</td>
                                    <td class="text-center text-center fs--1">Rp. {{ number_format($item->kurs, 0, ',', '.') }}</td>
                                    <td class="text-center text-center fs--1">Rp. {{ number_format($item->total_tukar, 0, ',', '.') }}</td>
                                    <td>-</td>
                                @else
                                    <td class="text-center text-center fs--1 jenis">Modal</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="text-center text-center fs--1">Rp. {{ number_format($item->jumlah_modal, 0, ',', '.') }}
                                @endif
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
<div class="modal fade" id="modalfilter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('jurnal-debit-kredit.create') }}" id="form2">
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Filter Data untuk Export</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1 mb-3">Filter Data Berdasarkan Inputan</p>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault1" type="radio" value="excel" name="radio_input" checked/>
                                    <label class="form-check-label" for="flexRadioDefault1">Export Excel</label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" id="flexRadioDefault2" type="radio" value="pdf" name="radio_input"  />
                                    <label class="form-check-label" for="flexRadioDefault2">Export PDF</label>
                                </div>
                            </div>
                        </div>
                        <div class="row input-daterange">
                            <div class="col-md-6">
                                <label class="small">Start Date</label>
                                <input class="form-control datetimepicker" id="from_date_export" type="date"
                                    name="from_date_export" placeholder="From Date"
                                    data-options='{"disableMobile":true}' />
                            </div>
                            <div class="col-md-6">
                                <label class="small">End Date</label>
                                <input class="form-control datetimepicker" id="to_date_export" type="date"
                                    name="to_date_export" placeholder="To Date" data-options='{"disableMobile":true}' />
                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <label for="filter_jenis">Filter by Debit or Kredit</label>
                                <select class="form-select js-choice" id="filter_jenis" name="filter_jenis"
                                    data-options='{"removeItemButton":true,"placeholder":true}'>
                                    <option value="">Pilih Jenis Jurnal</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Filter dan Export Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        var table = $('#example').DataTable();
    })
</script>



@endsection
