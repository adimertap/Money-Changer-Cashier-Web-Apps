@extends('layouts.app')

@section('content')
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

    $totalNominalSemua = $transaksiHistory->sum('total');
@endphp

<main class="py-2">
    {{-- Compact Top Bar: Customer Identity & Stats --}}
    <div class="card mb-2 border">
        <div class="card-body py-2 px-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold text-900 fs-0">
                        <i class="fas fa-user-circle text-primary me-1"></i>{{ $customer->name }}
                        @if(!empty($customer->alias))
                            <span class="fs--1 text-600 fw-normal">({{ $customer->alias }})</span>
                        @endif
                    </h6>
                    <span class="badge bg-secondary fs--2">{{ $customer->country ?: 'No Country' }}</span>
                    <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-danger' }} fs--2">
                        {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    @if($customer->is_terduga)
                        <span class="badge bg-danger fs--2"><i class="fas fa-exclamation-triangle me-1"></i>Terduga</span>
                    @endif
                </div>
                <div class="d-flex align-items-center flex-wrap gap-3 fs--1">
                    <div>
                        <span class="text-600">Total Trx:</span>
                        <strong class="text-primary">{{ number_format($transaksiHistory->count(), 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-600">Akumulasi:</span>
                        <strong class="text-success">Rp {{ $formatAngka($totalNominalSemua) }}</strong>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('master-customer.index') }}" class="btn btn-falcon-default btn-sm py-1 px-2">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button class="btn btn-outline-primary btn-sm py-1 px-2" type="button" onclick="editCustomerModal()">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Compact Data Identitas & CDD Nasabah --}}
    <div class="card mb-2 border">
        <div class="card-header bg-light py-1 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold fs--1 text-800">
                <i class="fas fa-id-card text-primary me-2"></i>Data Identitas &amp; Dokumen CDD (KYC)
            </span>
            @if(!empty($customer->supporting_document_file))
                <a href="{{ route('master-customer.dokumen', $customer->customer_id) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 shadow-none">
                    <i class="fas fa-paperclip me-1"></i>Unduh Berkas Profil
                </a>
            @else
                <span class="text-muted fs--2"><i class="fas fa-shield-alt text-success me-1"></i>Regulasi BI</span>
            @endif
        </div>
        <div class="card-body py-2 px-3">
            <div class="row g-2 fs--1">
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Passport / ID:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->passport ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">NIK:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->nik ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Cabang Terdaftar:</span>
                    <span class="fw-semi-bold text-dark">{{ optional($customer->cabang)->cabang_name ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">NPWP (TIN):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->npwp ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Pekerjaan (Job):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->job ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Jabatan (Position):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->position ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Perusahaan (Company):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->company ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Bentuk Usaha:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->company_form ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Bidang Usaha:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->business_sector ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Penghasilan (Income):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->income ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Sumber Dana:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->source_of_funds ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-600 d-block fs--2">Tujuan Transaksi:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->transaction_purpose ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-6">
                    <span class="text-600 d-block fs--2">Domisili:</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->domicile ?: '-' }}</span>
                </div>
                <div class="col-6 col-md-6">
                    <span class="text-600 d-block fs--2">Hubungan (jika diwakilkan):</span>
                    <span class="fw-semi-bold text-dark">{{ $customer->relationship ?: '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Compact Riwayat Transaksi Nasabah --}}
    <div class="card mb-2 border">
        <div class="card-header bg-light py-1 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold fs--1 text-800">
                <i class="fas fa-history text-primary me-2"></i>Riwayat Transaksi Nasabah
            </span>
            <span class="badge bg-primary fs--2">{{ $transaksiHistory->count() }} Transaksi</span>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered fs--1 mb-0 align-middle" id="historyTable">
                    <thead class="bg-200 text-800 text-center">
                        <tr>
                            <th style="width: 35px;">No</th>
                            <th style="width: 85px;">Tanggal</th>
                            <th>Kode Trx</th>
                            <th style="width: 60px;">Jenis</th>
                            <th>Cabang &amp; Kasir</th>
                            <th>Rincian Valas</th>
                            <th class="text-end" style="width: 120px;">Total</th>
                            <th class="text-center" style="width: 95px;">Dokumen CDD</th>
                            <th class="text-center" style="width: 75px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiHistory as $trx)
                        @php
                            $trxHasCdd = !empty($trx->npwp) || !empty($trx->position) || !empty($trx->domicile) ||
                                         !empty($trx->business_sector) || !empty($trx->income) || !empty($trx->transaction_purpose) ||
                                         !empty($trx->job) || !empty($trx->relationship) || !empty($trx->company) ||
                                         !empty($trx->source_of_funds) || !empty($trx->company_form) || !empty($trx->supporting_document_file);
                            $trxFile = $trx->supporting_document_file;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ url('/owner/jurnal-harian/' . $trx->id_transaksi) }}" class="fw-bold text-primary">
                                    #{{ $trx->kode_transaksi }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if(strcasecmp($trx->jenis_transaksi, 'Beli') === 0)
                                    <span class="badge bg-success fs--2">Beli</span>
                                @else
                                    <span class="badge bg-info fs--2">Jual</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semi-bold text-dark">{{ optional($trx->Cabang)->cabang_name ?: '-' }}</span>
                                <span class="text-muted fs--2">({{ optional($trx->Pegawai)->name ?: '-' }})</span>
                            </td>
                            <td>
                                @if($trx->detailTransaksi && $trx->detailTransaksi->count() > 0)
                                    @foreach($trx->detailTransaksi as $d)
                                        <span class="badge bg-100 text-dark border fs--2 me-1">
                                            {{ optional($d->Currency)->nama_currency ?: 'Valas' }} 
                                            {{ $formatAngka($d->jumlah_tukar) }} @ {{ $formatAngka($d->jumlah_currency) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted fs--2">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-nowrap">
                                Rp {{ $formatAngka($trx->total) }}
                                @if((float)$trx->total >= 180000000)
                                    <span class="badge bg-warning text-dark fs--2 ms-1" title="Regulasi BI &gt; 180 Juta">BI</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if($trxHasCdd)
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#modalCddTrx{{ $trx->id_transaksi }}">
                                        <i class="fas fa-file-alt me-1"></i>CDD
                                    </button>
                                    @if(!empty($trxFile))
                                        <a href="{{ route('transaksi.dokumen', $trx->id_transaksi) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-1 ms-1" title="Unduh Berkas">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted fs--2">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ url('/owner/jurnal-harian/' . $trx->id_transaksi) }}" class="btn btn-xs btn-falcon-default py-0 px-1" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('/cetak/' . $trx->id_transaksi) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-1 ms-1" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-3 text-muted">Belum ada riwayat transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

{{-- COMPACT MODALS DOKUMEN CDD TIAP TRANSAKSI --}}
@foreach($transaksiHistory as $trx)
@php
    $cddTrxVal = function ($field) use ($trx, $customer) {
        $val = $trx->{$field} ?: $customer->{$field};
        return !empty(trim((string)$val)) ? $val : null;
    };
    $trxSupportingFile = $trx->supporting_document_file ?: $customer->supporting_document_file;
@endphp
<div class="modal fade" id="modalCddTrx{{ $trx->id_transaksi }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 bg-light">
                <h6 class="modal-title mb-0 fw-bold fs--1 text-900">
                    <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Dokumen CDD #{{ $trx->kode_transaksi }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 fs--1">
                <div class="row g-2">
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">NPWP (TIN):</span>
                        <strong class="text-dark">{{ $cddTrxVal('npwp') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Position:</span>
                        <strong class="text-dark">{{ $cddTrxVal('position') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Job:</span>
                        <strong class="text-dark">{{ $cddTrxVal('job') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Income:</span>
                        <strong class="text-dark">{{ $cddTrxVal('income') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Company:</span>
                        <strong class="text-dark">{{ $cddTrxVal('company') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Company Form:</span>
                        <strong class="text-dark">{{ $cddTrxVal('company_form') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Business Sector:</span>
                        <strong class="text-dark">{{ $cddTrxVal('business_sector') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Source of Funds:</span>
                        <strong class="text-dark">{{ $cddTrxVal('source_of_funds') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Purpose:</span>
                        <strong class="text-dark">{{ $cddTrxVal('transaction_purpose') ?: '-' }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-600 fs--2 d-block">Relationship:</span>
                        <strong class="text-dark">{{ $cddTrxVal('relationship') ?: '-' }}</strong>
                    </div>
                    <div class="col-12">
                        <span class="text-600 fs--2 d-block">Domicile:</span>
                        <strong class="text-dark">{{ $cddTrxVal('domicile') ?: '-' }}</strong>
                    </div>
                </div>

                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-600 fs--2">Berkas Lampiran:</span>
                        @if(!empty($trxSupportingFile))
                            <a href="{{ route('transaksi.dokumen', $trx->id_transaksi) }}" target="_blank" class="btn btn-xs btn-primary py-0 px-2 shadow-none">
                                <i class="fas fa-external-link-alt me-1"></i>Buka / Unduh Berkas
                            </a>
                        @else
                            <span class="text-muted fs--2 fst-italic">Tidak ada lampiran fisik</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer py-1 px-3 bg-light">
                <a href="{{ url('/owner/jurnal-harian/' . $trx->id_transaksi) }}" class="btn btn-xs btn-outline-primary py-1">
                    Detail Transaksi
                </a>
                <button type="button" class="btn btn-xs btn-secondary py-1" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- MODAL EDIT CUSTOMER --}}
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="customerForm" method="POST" action="{{ route('master-customer.update', $customer->customer_id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header py-2 px-3 bg-light">
                    <h6 class="modal-title mb-0 fw-bold">Edit Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2 fs--1">
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1">Name <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" name="name" id="customerName" required value="{{ $customer->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1">Country <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="country" id="customerCountry" required>
                                <option value="">Pilih Country</option>
                                @foreach ($countries as $code => $countryName)
                                <option value="{{ $countryName }}" {{ $customer->country === $countryName ? 'selected' : '' }}>{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1">Passport</label>
                            <input class="form-control form-control-sm" name="passport" id="customerPassport" value="{{ $customer->passport }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1">NIK</label>
                            <input class="form-control form-control-sm" name="nik" id="customerNik" value="{{ $customer->nik }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1 d-block">Status</label>
                            <label class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="is_active" value="1" {{ $customer->is_active ? 'checked' : '' }}> Aktif
                            </label>
                            <label class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="is_active" value="0" {{ !$customer->is_active ? 'checked' : '' }}> Nonaktif
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs--2 mb-1">Cabang Terdaftar</label>
                            <select class="form-select form-select-sm" name="cabang_terdaftar" id="customerCabang">
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $c)
                                <option value="{{ $c->cabang_id }}" {{ $customer->cabang_terdaftar == $c->cabang_id ? 'selected' : '' }}>{{ $c->cabang_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-1 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm py-1" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary btn-sm py-1" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let countryChoices;
    $(function () {
        if ($('#historyTable').length) {
            $('#historyTable').DataTable({
                pageLength: 10,
                order: [[1, 'desc']],
                language: {
                    emptyTable: "Belum ada riwayat transaksi",
                    search: "Cari:",
                    lengthMenu: "Tampil _MENU_",
                    info: "_START_ - _END_ dari _TOTAL_",
                    paginate: { next: "›", previous: "‹" }
                }
            });
        }

        countryChoices = new Choices('#customerCountry', {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: ''
        });
    });

    function editCustomerModal() {
        $('#customerModal').modal('show');
    }
</script>
@endsection
