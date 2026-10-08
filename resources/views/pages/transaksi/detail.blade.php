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

  @php
    $cddCustomer = null;
    if (!empty($transaksi->nomor_passport)) {
        $cddCustomer = \App\Models\MasterCustomer::whereRaw('LOWER(TRIM(passport)) = ?', [mb_strtolower(trim($transaksi->nomor_passport))])->first();
    }
    if (!$cddCustomer && !empty($transaksi->nama_customer)) {
        $cddCustomer = \App\Models\MasterCustomer::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($transaksi->nama_customer))])->first();
    }
    $cddVal = function ($field) use ($transaksi, $cddCustomer) {
        $val = $transaksi->{$field} ?: ($cddCustomer ? $cddCustomer->{$field} : null);
        return !empty(trim((string)$val)) ? $val : null;
    };
    $supportingFile = $transaksi->supporting_document_file ?: ($cddCustomer ? $cddCustomer->supporting_document_file : null);
    $hasCddFields = !empty($cddVal('npwp')) || !empty($cddVal('position')) || !empty($cddVal('domicile')) ||
                    !empty($cddVal('business_sector')) || !empty($cddVal('income')) || !empty($cddVal('transaction_purpose')) ||
                    !empty($cddVal('job')) || !empty($cddVal('relationship')) || !empty($cddVal('company')) ||
                    !empty($cddVal('source_of_funds')) || !empty($cddVal('company_form')) || !empty($supportingFile);
    $hasLegacyDoc = !empty($transaksi->supporting_document_type) || !empty($transaksi->supporting_document_number) ||
                    !empty($transaksi->supporting_document_date) || !empty($transaksi->supporting_document_note);
  @endphp

  {{-- Card Dokumen Nasabah (CDD / KYC & Regulasi BI) - Compact --}}
  <div class="card mb-3 border {{ $hasCddFields ? 'border-primary' : 'border-200' }}">
    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-2 px-3 gap-2">
      <div class="d-flex align-items-center">
        <span class="fas fa-file-invoice-dollar text-primary me-2"></span>
        <h6 class="mb-0 text-900 fw-bold fs--1">Dokumen Nasabah (CDD / KYC &amp; Regulasi BI)</h6>
      </div>
      <div class="d-flex align-items-center gap-1">
        @if(!empty($supportingFile))
          <a href="{{ route('transaksi.dokumen', $transaksi->id_transaksi) }}" target="_blank" class="btn btn-xs btn-primary py-0 px-2 shadow-none">
            <i class="fas fa-paperclip me-1"></i>Unduh Berkas Lampiran
          </a>
        @endif
        @if((float)$transaksi->total >= 180000000)
          <span class="badge bg-warning text-dark fs--2"><i class="fas fa-shield-alt me-1"></i>&gt; Rp 180 Juta</span>
        @elseif($hasCddFields)
          <span class="badge bg-info fs--2">CDD Terdata</span>
        @else
          <span class="badge bg-secondary fs--2">Standar</span>
        @endif
      </div>
    </div>
    <div class="card-body py-2 px-3">
      <div class="row g-2 fs--1">
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">NPWP (TIN):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('npwp') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Jabatan (Position):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('position') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Pekerjaan (Job):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('job') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Penghasilan (Income):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('income') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Perusahaan (Company):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('company') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Bentuk Usaha:</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('company_form') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Bidang Usaha:</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('business_sector') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Sumber Dana:</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('source_of_funds') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Tujuan Transaksi:</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('transaction_purpose') ?: '-' }}</span>
        </div>
        <div class="col-6 col-md-3">
          <span class="text-600 d-block fs--2">Hubungan (if represented):</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('relationship') ?: '-' }}</span>
        </div>
        <div class="col-12 col-md-6">
          <span class="text-600 d-block fs--2">Domisili:</span>
          <span class="fw-semi-bold text-dark">{{ $cddVal('domicile') ?: '-' }}</span>
        </div>

        @if(!empty($supportingFile))
        <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
          <span class="text-600 fs--2"><i class="fas fa-paperclip me-1"></i>Lampiran: <strong>{{ basename($supportingFile) }}</strong></span>
          <a href="{{ route('transaksi.dokumen', $transaksi->id_transaksi) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2">
            <i class="fas fa-external-link-alt me-1"></i> Buka / Unduh Berkas
          </a>
        </div>
        @endif

        {{-- Legacy Underlying jika ada --}}
        @if($hasLegacyDoc)
        <div class="col-12 mt-2 pt-2 border-top">
          <div class="row g-2 fs--2">
            @if(!empty($transaksi->supporting_document_type))
            <div class="col-6 col-md-3"><span class="text-600 d-block">Jenis Dokumen:</span><strong class="text-dark">{{ $transaksi->supporting_document_type }}</strong></div>
            @endif
            @if(!empty($transaksi->supporting_document_number))
            <div class="col-6 col-md-3"><span class="text-600 d-block">Nomor Dokumen:</span><strong class="text-dark">{{ $transaksi->supporting_document_number }}</strong></div>
            @endif
            @if(!empty($transaksi->supporting_document_date))
            <div class="col-6 col-md-3"><span class="text-600 d-block">Tanggal:</span><strong class="text-dark">{{ \Carbon\Carbon::parse($transaksi->supporting_document_date)->translatedFormat('d M Y') }}</strong></div>
            @endif
            @if(!empty($transaksi->supporting_document_note))
            <div class="col-12"><span class="text-600 d-block">Catatan:</span><span class="text-dark">{{ $transaksi->supporting_document_note }}</span></div>
            @endif
          </div>
        </div>
        @endif
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