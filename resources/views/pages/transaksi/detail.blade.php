@extends('layouts.app')

@section('content')

<main>
  <div class="card mb-3">
    <div class="card-header bg-light">
      <div class="row align-items-center">
        <div class="col">
          <div class="d-flex align-items-center">
            <h5 class="mb-0 me-3">Transaksi Kode: <span class="text-primary">#{{ $transaksi->kode_transaksi }}</span></h5>
            @if(strcasecmp($transaksi->jenis_transaksi, 'Beli') === 0)
              <span class="badge bg-success">Transaksi Beli</span>
            @else
              <span class="badge bg-info">Transaksi Jual</span>
            @endif
          </div>
        </div>
        <div class="col-auto">
          <a href="{{ url('/cetak/' . $transaksi->id_transaksi) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
            <i class="fas fa-print me-1"></i> Cetak Receipt
          </a>
          <a href="javascript:history.back()" class="btn btn-falcon-default btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
          </a>
        </div>
      </div>
    </div>
    <div class="card-body border-top">
      <div class="row g-2">
        <div class="col-md-4">
          <div class="d-flex align-items-center">
            <span class="fas fa-user-tie text-primary me-2"></span>
            <div>
              <span class="fs--2 text-600 d-block">Pegawai Kasir</span>
              <span class="fw-semi-bold">{{ optional($transaksi->Pegawai)->name ?: '-' }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex align-items-center">
            <span class="fas fa-building text-info me-2"></span>
            <div>
              <span class="fs--2 text-600 d-block">Cabang</span>
              <span class="fw-semi-bold">{{ optional($transaksi->Cabang)->cabang_name ?: '-' }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-flex align-items-center">
            <span class="fas fa-calendar-alt text-success me-2"></span>
            <div>
              <span class="fs--2 text-600 d-block">Waktu Transaksi</span>
              <span class="fw-semi-bold">{{ date_format($transaksi->created_at, "d M Y H:i:s") }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Informasi Customer --}}
  <div class="card mb-3">
    <div class="card-header bg-light py-2">
      <h6 class="mb-0 text-700"><i class="fas fa-id-card text-primary me-2"></i>Informasi Customer</h6>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label text-600 fs--1 mb-1">Nama Customer</label>
          <input class="form-control form-control-sm bg-light" name="nama_customer" type="text"
            value="{{ $transaksi->nama_customer ?: '-' }}" readonly />
        </div>
        <div class="col-md-4">
          <label class="form-label text-600 fs--1 mb-1">Nomor Passport / ID</label>
          <input class="form-control form-control-sm bg-light" name="nomor_passport" type="text"
            value="{{ $transaksi->nomor_passport ?: '-' }}" readonly />
        </div>
        <div class="col-md-4">
          <label class="form-label text-600 fs--1 mb-1">Asal Negara</label>
          <input class="form-control form-control-sm bg-light" name="asal_negara" type="text"
            value="{{ $transaksi->negara_asal ?: '-' }}" readonly />
        </div>
      </div>
    </div>
  </div>

  {{-- Kelengkapan Dokumen Pendukung jika transaksi memiliki dokumen atau total >= 180 Juta --}}
  @if(!empty($transaksi->supporting_document_type) || !empty($transaksi->supporting_document_number) || !empty($transaksi->supporting_document_file) || !empty($transaksi->supporting_document_note) || (float)$transaksi->total >= 180000000)
  <div class="card mb-3 border border-warning">
    <div class="card-header bg-soft-warning border-bottom border-warning d-flex justify-content-between align-items-center py-2">
      <div class="d-flex align-items-center">
        <span class="fas fa-file-invoice text-warning fs-1 me-2"></span>
        <div>
          <h6 class="mb-0 text-900 fw-bold">Kelengkapan Dokumen Pendukung (Regulasi BI &gt; Rp 180 Juta)</h6>
          <span class="fs--2 text-700">Dokumen pendukung (Underlying Document) untuk transaksi bernilai setara USD 10.000 atau lebih</span>
        </div>
      </div>
      @if(!empty($transaksi->supporting_document_file))
        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Dokumen Terlampir</span>
      @else
        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Belum Diunggah</span>
      @endif
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label text-600 fs--1 mb-1">Jenis Dokumen</label>
          <div class="fw-bold text-dark">{{ $transaksi->supporting_document_type ?: '-' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label text-600 fs--1 mb-1">Nomor Dokumen</label>
          <div class="fw-bold text-dark">{{ $transaksi->supporting_document_number ?: '-' }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label text-600 fs--1 mb-1">Tanggal Dokumen</label>
          <div class="fw-bold text-dark">
            {{ $transaksi->supporting_document_date ? \Carbon\Carbon::parse($transaksi->supporting_document_date)->translatedFormat('d F Y') : '-' }}
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label text-600 fs--1 mb-1">Berkas Dokumen Pendukung</label>
          <div>
            @if(!empty($transaksi->supporting_document_file))
              @php
                $isPdf = preg_match('/\.pdf$/i', $transaksi->supporting_document_file);
                $isImg = preg_match('/\.(jpg|jpeg|png|webp)$/i', $transaksi->supporting_document_file);
              @endphp
              <a href="{{ route('transaksi.dokumen', $transaksi->id_transaksi) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                <i class="fas {{ $isPdf ? 'fa-file-pdf text-danger' : ($isImg ? 'fa-file-image text-info' : 'fa-download') }} me-2"></i>
                Lihat / Unduh Dokumen
              </a>
            @else
              <span class="text-muted fst-italic fs--1">Tidak ada file terlampir</span>
            @endif
          </div>
        </div>
        <div class="col-12 mt-2">
          <label class="form-label text-600 fs--1 mb-1">Keterangan / Keperluan Transaksi</label>
          <div class="p-2 bg-light rounded border text-700 fs--1">
            {{ $transaksi->supporting_document_note ?: '-' }}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

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
  <div class="card mt-3 mb-3">
    <div class="card-header bg-light py-2">
      <h6 class="mb-0 text-700"><i class="fas fa-coins text-warning me-2"></i>Rincian Valas &amp; Nilai Tukar</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive fs--1">
        <table class="table table-striped border-bottom">
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
              <th scope="row" class="align-middle">{{ $loop->iteration }}.</th>
              <td class="align-middle text-center">{{ optional($item->Currency)->nama_currency }}, {{ optional($item->Currency)->jenis_kurs }}</td>
              <td class="align-middle text-center">Rp. {{ $formatAngka($item->jumlah_currency) }}</td>
              <td class="align-middle text-center">{{ $formatAngka($item->jumlah_tukar) }}</td>
              <td class="align-middle text-end">Rp. {{ $formatAngka($item->total_tukar) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-3">Tidak ada data rincian transaksi</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="row g-0 justify-content-end">
        <div class="col-auto">
          <table class="table table-sm table-borderless fs--1 text-end">
            <tbody>
              <tr class="border-bottom">
                <th class="text-900 fs-0">Total Transaksi:</th>
                <td class="fw-bold fs-0 text-primary">Rp. {{ $formatAngka($transaksi->total) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

@endsection