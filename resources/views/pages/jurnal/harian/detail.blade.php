@extends('layouts.app')

@section('content')

<main>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h5 class="mb-2">Transaksi Kode: <span class="text-primary">#{{ $transaksi->kode_transaksi }}</span>
                        <div class="col-auto mt-3"><button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0"
                                type="button">
                                <span class="fas fa-arrow-down me-1"> </span>Download
                                (.pdf)</button>
                                <button class="btn btn-falcon-default btn-sm me-1 mb-2 mb-sm-0" type="button">
                                <span class="fas fa-print me-1"> </span>Print Invoice</button>
                            </div>
                </div>
                <div class="col-auto d-none d-sm-block">
                    <h6 class="text-uppercase text-600">Transaksi Detail<span class="fas fa-user ms-2"></span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="card-body border-top">
            <div class="d-flex">
                <span class="fas fa-user text-success me-2" data-fa-transform="down-5"></span>
                <div class="flex-1">
                    <p class="mb-0">Pegawai: {{ $transaksi->Pegawai->name }}</p>
                    <p class="fs--1 mb-0 text-600">{{ date_format($transaksi->created_at,"d M Y H:i:s") }}</p>
                </div>
            </div>
        </div>
    </div>
    @php
      $formatAngka = function ($val, $maxDec = 4) {
          $floatVal = (float) $val;
          if (floor($floatVal) == $floatVal) {
              return number_format($floatVal, 0, ',', '.');
          }
          $str = (string) $floatVal;
          $decimalPart = substr(strrchr($str, '.'), 1) ?: '';
          $numDec = max(2, min(strlen($decimalPart), $maxDec));
          return number_format($floatVal, $numDec, ',', '.');
      };
    @endphp
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
                    <td class="align-middle text-center">{{ optional($item->Currency)->nama_currency }}</td>
                    <td class="align-middle text-center">Rp. {{ $formatAngka($item->jumlah_currency) }}</td>
                    <td class="align-middle text-center">{{ $formatAngka($item->jumlah_tukar) }}</td>
                    <td class="align-middle text-end">Rp. {{ $formatAngka($item->total_tukar) }}</td>
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
                  <td class="fw-semi-bold">Rp. {{ $formatAngka($transaksi->total) }}</td>
                </tr>
              </tbody></table>
            </div>
          </div>
        </div>
      </div>
</main>

<script>
    $(document).ready(function () {
        var table = $('#example').DataTable();
    })
</script>

@endsection
