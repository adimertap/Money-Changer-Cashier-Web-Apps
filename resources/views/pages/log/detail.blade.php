@extends('layouts.app')

@section('content')

<main>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h5 class="mb-2">Transaksi Kode: <span class="text-primary">#{{ $log->kode_transaksi }}</span>
                </div>
                <div class="col-auto d-none d-sm-block">
                    <h6 class="text-uppercase text-600">Transaksi Detail<span class="fas fa-user ms-2"></span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="card-body border-top">
          <div class="row">
            <div class="col">
              <div class="d-flex">
                <span class="fas fa-user text-success me-2" data-fa-transform="down-5"></span>
                <div class="flex-1">
                    <p class="mb-0">Pegawai: {{ $log->Pegawai->name }}</p>
                    <p class="fs--1 mb-0 text-600">Tanggal Transaksi {{ $log->tanggal_transaksi }}</p>
                    <p class="fs--1 mb-0 text-600">Diedit pada {{ date_format($log->created_at,"d M Y H:i:s") }}</p>

                </div>
            </div>
            </div>
            <div class="col-auto">
                @if($log->jenis_log == 'Delete')
                  <p class="badge rounded-pill badge-soft-danger mb-0">Jenis Log: {{ $log->jenis_log }}</p>
                @else
                  <p class="badge rounded-pill badge-soft-info mb-0">Jenis Log: {{ $log->jenis_log }}</p>
                @endif
                <p class="fs--1 text-600">Keterangan: {{ $log->keterangan }}</p>
            </div>
          </div>
           
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
          <div class="table-responsive fs--1">
            <table class="table table-striped border-bottom" id="example">
              <thead class="bg-200 text-900">
                <tr>
                  <th class="border-0">No.</th>
                  <th class="border-0 text-center">Currency</th>
                  <th class="border-0 text-center">Harga Currency</th>
                  <th class="border-0 text-center">Jumlah Tukar</th>
                  <th class="border-0 text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($detail as $item)
                <tr role="row" class="odd align-middle">
                    <th scope="row" class="align-middle">{{ $loop->iteration}}.</th>
                    <td class="align-middle text-center">{{ $item->Currency->nama_currency }}</td>
                    <td class="align-middle text-center">Rp. {{ number_format($item->jumlah_currency, 0, ',', '.') }}</td>
                    <td class="align-middle text-center">{{ $item->jumlah_tukar }}</td>
                    <td class="align-middle text-end">Rp. {{ number_format($item->total_tukar, 0, ',', '.') }}</td>
                </tr>
                @empty

                @endforelse
              </tbody>
            </table>
          </div>
          <div class="row g-0 justify-content-end">
            <div class="col-auto">
              <table class="table table-sm table-borderless fs--1 text-end">
                <tbody>
                <tr class="border-bottom">
                  <th class="text-900">Total:</th>
                  <td class="fw-semi-bold">Rp. {{ number_format($log->total, 0, ',', '.') }}</td>
                </tr>
              </tbody></table>
            </div>
          </div>
        </div>
      </div>
</main>

@endsection
