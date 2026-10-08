@extends('layouts.app')

@section('content')

<main class="mt-3">
    <div class="card bg-transparent-50 overflow-hidden mb-3">
        <div class="card-header position-relative">
            <div class="bg-holder d-none d-md-block bg-card z-index-1"
                style="background-image:url(../falcon/assets/img/illustrations/ecommerce-bg.png);background-size:170px;background-position:right bottom;z-index:-1;">
            </div>
            <div class="position-relative z-index-2">
                <div class="row">
                    <div class="col-8">
                        {{-- <h3 class="text-primary mb-1">Selamat Datang Kembali, {{ Auth::user()->nama_panggilan }}!
                        </h3> --}}
                        <h3 class="text-primary mb-1 mt-3">Transaksi Beli Valas!</h3>

                        <p>Tambah Transaksi Hari Ini {{ $today }}</p>
                        <h6 class="text-primary">Nomor Order: #{{ $kode_transaksi }}</h6>
                        <hr>

                    </div>
                    <div class="col-4">
                        <div class="d-flex py-3">
                            <div class="pe-3 mt-3">
                                @if ($modal == '')
                                <h6 class="text-600 fs--1 fw-medium">Anda Belum Menambahkan Modal Hari Ini</h6>
                                @else
                                <h6 class="text-600 fs--1 fw-medium">Modal Anda Hari Ini</h6>
                                @endif

                                <h4 class="text-primary jumlah_modal mb-2" id="jumlah_modal"
                                    data-countup="jumlah_modal"
                                    data-base-modal="{{ !empty($modal) ? (float)$modal->riwayat_modal : 0 }}"
                                    data-sisa-modal="{{ !empty($modal) ? (float)$modal->riwayat_modal : 0 }}">
                                    @if ($modal == '')

                                    @else
                                    Rp. {{ number_format($modal->riwayat_modal, 0, ',', '.') }}
                                    @endif

                                </h4>
                                <a class="fw-semi-bold fs--1 text-nowrap" href="{{ route('modal.index') }}">Tambah Modal
                                    <span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('transaksi.store') }}" id="form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gx-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body p-4">
                        <i class="mb-3">Lengkapi data Customer dibawah Ini</i>

                        @if (Auth::user()->role === 'Owner')
                        <div class="mb-3 mt-3">
                            <label class="form-label" for="transaction_cabang_id">Cabang</label>
                            <select class="form-select" id="transaction_cabang_id" name="cabang_id" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->cabang_id }}" {{ (string) $selectedCabangId === (string) $cabang->cabang_id ? 'selected' : '' }}>{{ $cabang->cabang_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="cabang_id" value="{{ $selectedCabangId }}">
                        @endif

                        {{-- Dropdown customer (langsung memuat semua customer cabang) --}}
                        <div class="mb-2 customer-picker">
                            <label class="form-label" for="customerSelect">Customer</label>
                            <div class="input-group">
                                <select class="form-select js-choice" id="customerSelect" size="1"
                                    data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false,"searchPlaceholderValue":"Cari nama, alias, passport, NIK, atau negara","noResultsText":"Customer tidak ditemukan"}'>
                                    <option value="">Pilih Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->name }}{{ $customer->alias ? ' - ' . $customer->alias : '' }}{{ $customer->passport ? ' [' . $customer->passport . ']' : '' }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary customer-add-button" type="button" id="addCustomerButton" title="Tambah customer manual">+</button>
                                <!-- <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#customerCreateModal" title="Tambah customer via popup modal"><i class="fas fa-user-plus"></i></button> -->
                            </div>
                            <small class="text-muted">Seluruh customer aktif pada cabang terpilih ditampilkan.</small>
                        </div>

                        {{-- Tombol untuk kembali ke pencarian (awalnya tersembunyi) --}}
                        <div class="mb-3" id="toggleCustomerForm" style="display: none;">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="backToSearchButton">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Pencarian Customer
                            </button>
                        </div>

                        {{-- Form manual untuk customer baru (awalnya tersembunyi) --}}
                        <div id="customerForm" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label" for="nama_customer_input">Nama Customer <span class="text-danger">*</span></label>
                                <input class="form-control form-select-sm @error('nama_customer') is-invalid @enderror"
                                    name="nama_customer" id="nama_customer_input" type="text" placeholder="Input Nama Customer"
                                    value="{{ old('nama_customer') }}" />
                                @error('nama_customer')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input status-passport-radio" type="radio" name="status_passport_radio" id="passport_ada" value="ada" checked>
                                        <label class="form-check-label fw-semi-bold cursor-pointer" for="passport_ada">
                                            Ada
                                        </label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input status-passport-radio" type="radio" name="status_passport_radio" id="passport_tidak_ada" value="tidak_ada">
                                        <label class="form-check-label fw-semi-bold cursor-pointer" for="passport_tidak_ada">
                                            Tidak Ada
                                        </label>
                                    </div>
                                </div>
                                <label class="form-label" for="nomor_passport">Nomor Passport</label>
                                <input class="form-control form-select-sm @error('nomor_passport') is-invalid @enderror"
                                    name="nomor_passport" id="nomor_passport" type="text" placeholder="Input Nomor Passport"
                                    value="{{ old('nomor_passport') }}" />
                                <div id="passport_auto_hint" class="small mt-1 text-primary fw-semi-bold" style="display: none;">
                                    <i class="fas fa-magic me-1"></i>Nomor paspor otomatis (9 digit): <span id="passport_auto_number_text" class="badge bg-primary text-white px-2 py-1">-</span>
                                </div>
                                @error('nomor_passport')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="asal_negara_select">Asal Negara</label>
                                <select class="form-select form-select-sm @error('asal_negara') is-invalid @enderror"
                                    name="asal_negara" id="asal_negara_select">
                                    <option value="">Pilih Asal Negara</option>
                                    @foreach ($countries as $code => $country)
                                        <option value="{{ $country }}" {{ old('asal_negara') === $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asal_negara')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>

                            {{-- Input hidden data dokumen untuk nasabah baru --}}
                            <input type="hidden" name="npwp" id="doc_npwp" value="{{ old('npwp') }}">
                            <input type="hidden" name="position" id="doc_position" value="{{ old('position') }}">
                            <input type="hidden" name="domicile" id="doc_domicile" value="{{ old('domicile') }}">
                            <input type="hidden" name="business_sector" id="doc_business_sector" value="{{ old('business_sector') }}">
                            <input type="hidden" name="income" id="doc_income" value="{{ old('income') }}">
                            <input type="hidden" name="transaction_purpose" id="doc_transaction_purpose" value="{{ old('transaction_purpose') }}">
                            <input type="hidden" name="job" id="doc_job" value="{{ old('job') }}">
                            <input type="hidden" name="relationship" id="doc_relationship" value="{{ old('relationship') }}">
                            <input type="hidden" name="company" id="doc_company" value="{{ old('company') }}">
                            <input type="hidden" name="source_of_funds" id="doc_source_of_funds" value="{{ old('source_of_funds') }}">
                            <input type="hidden" name="company_form" id="doc_company_form" value="{{ old('company_form') }}">

                            {{-- Datalist opsi autocomplete untuk modal Document --}}
                            <datalist id="position_options">
                                <option value="CEO">
                                <option value="Direktur">
                                <option value="Manager">
                                <option value="Owner / Pemilik">
                                <option value="Staf / Karyawan">
                                <option value="Komisaris">
                            </datalist>
                            <datalist id="business_sector_options">
                                <option value="persero">
                                <option value="Perdagangan">
                                <option value="Pariwisata / Perhotelan">
                                <option value="Jasa / Konsultan">
                                <option value="Keuangan / Perbankan">
                                <option value="Konstruksi / Properti">
                                <option value="F&B / Kuliner">
                            </datalist>
                            <datalist id="income_options">
                                <option value="0-100 Juta">
                                <option value="100-500 Juta">
                                <option value="500 Juta - 1 Milyar">
                                <option value="> 1 Milyar">
                            </datalist>
                            <datalist id="transaction_purpose_options">
                                <option value="Perjalanan Dinas">
                                <option value="Liburan / Wisata">
                                <option value="Pendidikan / Sekolah">
                                <option value="Bisnis / Investasi">
                                <option value="Pengobatan / Medis">
                                <option value="Keperluan Keluarga">
                            </datalist>
                            <datalist id="job_options">
                                <option value="Lainnya">
                                <option value="Pegawai Swasta">
                                <option value="PNS / ASN">
                                <option value="Wiraswasta / Pengusaha">
                                <option value="Profesional">
                                <option value="TNI / POLRI">
                                <option value="Ibu Rumah Tangga">
                                <option value="Pelajar / Mahasiswa">
                            </datalist>
                            <datalist id="relationship_options">
                                <option value="Diri Sendiri">
                                <option value="Saudara">
                                <option value="Orang Tua / Anak">
                                <option value="Suami / Istri">
                                <option value="Atasan / Karyawan">
                                <option value="Kuasa / Rekan Kerja">
                            </datalist>
                            <datalist id="source_of_funds_options">
                                <option value="Hasil Sendiri">
                                <option value="Gaji / Penghasilan">
                                <option value="Tabungan">
                                <option value="Hasil Usaha / Bisnis">
                                <option value="Warisan / Hibah">
                                <option value="Uang Saku / Tunawisata">
                            </datalist>
                            <datalist id="company_form_options">
                                <option value="Badan Usaha Non-UMKM berbentuk PT">
                                <option value="Badan Usaha Non-UMKM berbentuk CV">
                                <option value="UMKM">
                                <option value="Perorangan">
                                <option value="BUMN / BUMD / Persero">
                                <option value="Yayasan / Lembaga">
                            </datalist>

                            <div class="mb-4">
                                <div class="d-flex gap-2 justify-content-start mb-3">
                                    <button class="btn btn-warning btn-sm" type="button" id="validateTerdugaButton">
                                        Validate Terduga
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" type="button" id="saveCustomerManualBtn">
                                        Simpan Customer
                                    </button>
                                </div>
                                <small class="text-muted d-block"><i class="fas fa-info-circle"></i>Customer baru juga akan otomatis tersimpan ke master customer saat transaksi disubmit.</small>
                                <div id="validationResult" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>

                        {{-- Widget Info Kuncian Paspor Customer --}}
                        <div id="passportAccumulationBadge" class="mt-2 mb-3" style="display: none;">
                            <div class="card border border-200 shadow-none bg-100 mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fs--1 text-700 fw-semi-bold">
                                            <i class="fas fa-passport me-1 text-primary"></i>Paspor: <span id="passportNumberDisplay" class="fw-bold text-dark">-</span>
                                        </span>
                                        <span id="passportStatusPill" class="badge rounded-pill bg-success fs--2">Aman</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                                        <span class="fs--1 text-muted">Akumulasi Bulan Ini:</span>
                                        <span class="fs--1 fw-bold">
                                            <span id="passportAccumulatedDisplay" class="text-primary">Rp 0</span> / <span id="passportLimitDisplay" class="text-muted">Rp 180.000.000</span>
                                        </span>
                                    </div>
                                    <div id="passportCurrentOrderHint" class="text-end fs--2 text-primary fw-semi-bold mb-1" style="display: none;">
                                        <i class="fas fa-cart-plus me-1"></i>Termasuk transaksi saat ini: <span id="passportCurrentOrderAmount">Rp 0</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div id="passportProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between fs--2 text-muted mt-1">
                                        <span id="passportRollingInfo">Rolling 30 Hari: Rp 0</span>
                                        <span id="passportRemainingInfo">Sisa: Rp 180.000.000</span>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- Input hidden untuk kompatibilitas dengan sistem lama --}}
                        <input type="hidden" name="customer_id" id="customer_id">
                        <input type="hidden" name="nama_customer" id="nama_customer_hidden" value="{{ old('nama_customer') }}">
                        <input type="hidden" name="customer_alias" id="customer_alias_hidden">
                        <input type="hidden" name="screening_confirmed" id="screening_confirmed" value="0">
                    </div>

                </div>

            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                        <h5 class="mb-0">Order Summary</h5> <br>
                        <a class="btn btn-falcon-default btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#modaltambah">
                            <span class="fas fa-plus me-2" data-fa-transform="shrink-2"></span>Tambah
                            Transaksi</a>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="kode_transaksi" value="{{ $kode_transaksi }}">
                        <input type="hidden" name="tanggal_transaksi" value="{{ $today_format }}">
                        <input type="hidden" name="id_modal" value="{{ $modal->id_modal }}">
                        <input type="hidden" name="id_transaksi" value="{{ $idbaru }}">
                        <div class="table-responsive scrollbar">
                            <table class="table table-hover table-striped overflow-hidden" id="dataTableKonfirmasi">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Currency</th>
                                        <th>Harga Currency</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="konfirmasi" style="font-size: 15px!important">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="card-footer d-flex justify-content-between bg-light">
                        <div class="fs-1 fw-semi-bold">Payable Total</div>
                        <div class="fs-1 fw-bold payable_total" id="payable_total">Rp. 0.0</div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Confirm Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 col-xl-12 col-xxl-7 px-md-3 mb-xxl-0 position-relative">
                            <div class="d-flex"><img class="me-3" src="../falcon/assets/img/icons/shield.png" alt=""
                                    width="60" height="60">
                                <div class="flex-1">
                                    <h5 class="mb-1">Mohon di lakukan pengecekan kembali</h5>
                                    <p class="fs--1 mb-0">Pastikan transaksi telah sesuai dan cek kembali total
                                        transaksi
                                    </p>
                                    <div class="fs-4 mt-2 fw-semi-bold">All Total: <span class="text-primary">
                                            <span class="grand_total" id="grand_total">Rp. 0.0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="vertical-line d-none d-md-block d-xl-none d-xxl-block"> </div>
                        </div>
                        <div
                            class="col-md-5 col-xl-12 col-xxl-5 ps-lg-4 ps-xl-2 ps-xxl-5 text-center text-md-start text-xl-center text-xxl-start">
                            <div class="border-dashed-bottom d-block d-md-none d-xl-block d-xxl-none my-4"></div>

                            <button class="btn btn-success mt-3 px-5 py-3" onclick="submitdata(event)"
                                id="button_submit" type="button">Confirm &amp; Pay
                            </button>
                            {{-- <p class="fs--1 mt-3 mb-0">By clicking <strong>Confirm &amp; Pay </strong>button,
                                transaction being process --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<div class="modal fade" id="customerCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="customerCreateForm">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Tambah Customer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input class="form-control" name="name" placeholder="Input nama customer" required></div>
                        <div class="col-md-6"><label class="form-label">Alias</label><input class="form-control" name="alias" placeholder="Input alias customer"></div>
                        <div class="col-md-6"><label class="form-label">Country <span class="text-danger">*</span></label><input class="form-control" name="country" placeholder="Input negara asal" required></div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input modal-status-passport-radio" type="radio" name="modal_status_passport_radio" id="modal_passport_ada" value="ada" checked>
                                    <label class="form-check-label fw-semi-bold cursor-pointer" for="modal_passport_ada">
                                        Ada
                                    </label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input modal-status-passport-radio" type="radio" name="modal_status_passport_radio" id="modal_passport_tidak_ada" value="tidak_ada">
                                    <label class="form-check-label fw-semi-bold cursor-pointer" for="modal_passport_tidak_ada">
                                        Tidak Ada
                                    </label>
                                </div>
                            </div>
                            <label class="form-label" for="modalCustomerPassportInput">Passport</label>
                            <input class="form-control" name="passport" id="modalCustomerPassportInput" placeholder="Input nomor passport">
                            <div id="modal_passport_auto_hint" class="small mt-1 text-primary fw-semi-bold" style="display: none;">
                                <i class="fas fa-magic me-1"></i>Nomor paspor otomatis (9 digit): <span id="modal_passport_auto_number_text" class="badge bg-primary text-white px-2 py-1">-</span>
                            </div>
                            <div id="customerCreatePassportInfo" class="mt-1" style="display: none;">
                                <div class="p-2 border rounded bg-light fs--2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted"><i class="fas fa-passport me-1 text-primary"></i>Akumulasi Bulan Ini:</span>
                                        <span class="fw-bold text-primary" id="customerCreatePassportTotal">Rp 0 / Rp 180.000.000</span>
                                    </div>
                                    <div class="text-muted" id="customerCreatePassportNote"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="form-label">Pekerjaan</label><input class="form-control" name="pekerjaan" placeholder="Input pekerjaan"></div>
                        <div class="col-md-6"><label class="form-label">NIK</label><input class="form-control" name="nik" placeholder="Input NIK"></div>
                        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" placeholder="Input alamat customer"></textarea></div>

                        <div class="col-12"><hr class="my-2"><h6 class="fw-bold fs--1 text-700 mb-2"><i class="fas fa-file-contract text-primary me-2"></i>Document (CDD / KYC)</h6></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">NPWP (TIN)</label><input class="form-control form-control-sm" name="npwp" placeholder="NPWP / TIN"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Position</label><input class="form-control form-control-sm" name="position" list="position_options" placeholder="CEO"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Domicile</label><input class="form-control form-control-sm" name="domicile" placeholder="Bali"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Business Sector</label><input class="form-control form-control-sm" name="business_sector" list="business_sector_options" placeholder="persero"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Income</label><input class="form-control form-control-sm" name="income" list="income_options" placeholder="0-100 Juta"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Transaction Purpose</label><input class="form-control form-control-sm" name="transaction_purpose" list="transaction_purpose_options" placeholder="Perjalanan Dinas"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Job</label><input class="form-control form-control-sm" name="job" list="job_options" placeholder="Lainnya"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Relationship (if represented)</label><input class="form-control form-control-sm" name="relationship" list="relationship_options" placeholder="Saudara"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Company</label><input class="form-control form-control-sm" name="company" placeholder="PT Usaha Bersama"></div>
                        <div class="col-md-6"><label class="form-label fs--2 mb-1">Source of funds</label><input class="form-control form-control-sm" name="source_of_funds" list="source_of_funds_options" placeholder="Hasil Sendiri"></div>
                        <div class="col-12"><label class="form-label fs--2 mb-1">Company Form</label><input class="form-control form-control-sm" name="company_form" list="company_form_options" placeholder="Badan Usaha Non-UMKM berbentuk PT"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Document (CDD / KYC) Sesuai Tampilan Gambar Referensi --}}
<div class="modal fade" id="customerDocumentDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark fs-1 mb-0" id="modalDocTitle">Document</h5>
                    <span class="badge bg-danger fs--2" id="modalDocBadgeMandatory">Wajib Diisi</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="modalDocCloseBtn"></button>
            </div>
            <div class="modal-body pt-3 pb-3">
                <div id="modalDocAlertWajib" class="alert alert-warning py-2 px-3 mb-3 fs--1">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    <strong>Wajib Diisi:</strong> Akumulasi transaksi nasabah ini melebihi batas regulasi. Seluruh isian dokumen nasabah (CDD / KYC) wajib dilengkapi sebelum transaksi dapat diproses.
                </div>

                <form id="modalDocEditForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">NPWP (TIN) <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="npwp" id="m_edit_npwp" type="text" placeholder="741168156686" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Position <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="position" id="m_edit_position" list="position_options" type="text" placeholder="CEO" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Domicile <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="domicile" id="m_edit_domicile" type="text" placeholder="Bali" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Business Sector <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="business_sector" id="m_edit_business_sector" list="business_sector_options" type="text" placeholder="persero" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Income <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="income" id="m_edit_income" list="income_options" type="text" placeholder="0-100 Juta" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Transaction Purpose <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="transaction_purpose" id="m_edit_transaction_purpose" list="transaction_purpose_options" type="text" placeholder="Perjalanan Dinas" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Job <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="job" id="m_edit_job" list="job_options" type="text" placeholder="Lainnya" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Relationship (if represented) <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="relationship" id="m_edit_relationship" list="relationship_options" type="text" placeholder="Saudara" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Company <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="company" id="m_edit_company" type="text" placeholder="PT Usaha Bersama" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs--1 mb-1">Source of funds <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="source_of_funds" id="m_edit_source_of_funds" list="source_of_funds_options" type="text" placeholder="Hasil Sendiri" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold fs--1 mb-1">Company Form <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm doc-mandatory-field" name="company_form" id="m_edit_company_form" list="company_form_options" type="text" placeholder="Badan Usaha Non-UMKM berbentuk PT" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold fs--1 mb-1">Lampiran Dokumen <span class="text-muted fw-normal fs--2">(Opsional)</span></label>
                            <input class="form-control form-control-sm" type="file" name="supporting_document_file" id="m_edit_lampiran" accept=".pdf,.jpg,.jpeg,.png,.webp" />
                            <small class="text-muted fs--2">Format file: PDF, JPG, JPEG, PNG, WEBP (maks. 10MB). Boleh dikosongkan jika tidak ada fisik dokumen.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-success btn-sm px-4 fw-bold" id="modalSaveAndProceedBtn">
                    <i class="fas fa-check-circle me-1"></i>Simpan &amp; Lanjutkan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passportDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="passportDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dokumen Pendukung Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3" id="passportThresholdNotice">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                            <div>
                                <strong class="d-block mb-1">Validasi Kuncian Paspor Rolling 30 Hari Melebihi Batas</strong>
                                <p class="mb-0 small" id="passportThresholdDesc">Akumulasi transaksi nomor paspor ini melebihi batas regulasi Bank Indonesia setara USD 10.000 (Rp 180 Juta) dalam 30 hari terakhir. Wajib melengkapi dokumen pendukung (Underlying Document) untuk melanjutkan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" name="supporting_document_type" placeholder="Contoh: Form A atau Surat Pernyataan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" name="supporting_document_number" placeholder="Input nomor dokumen" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" name="supporting_document_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">File Dokumen <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="supporting_document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF/JPG/PNG, maksimal 10 MB.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="supporting_document_note" placeholder="Input keterangan dokumen pendukung" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit" id="passportDocumentSubmit">Lanjutkan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modaltambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                <div class="position-relative z-index-1 light">
                    <h4 class="mb-0 text-white" id="authentication-modal-label">Detail Transaksi</h4>
                    <p class="fs--1 mb-0 text-white">Tambah detail transaksi untuk melengkapi Order</p>
                </div><button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2"
                    id="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-currency-store') }}" id="form1" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="border-dashed-bottom mb-2"></div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="currency">Pilih Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <select class="form-select js-choice currency-select" id="currency" size="1"
                                    name="id_currency"
                                    data-options='{"removeItemButton":true,"placeholder":true,"shouldSort":false}'>
                                    <option value="">Pilih Kurs Terlebih Dahulu</option>
                                    @foreach ($currency as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}, {{
                                        $item->jenis_kurs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="jumlah_currency">Nilai Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <div class="input-group"><span class="input-group-text">Rp. </span>
                                <input class="form-control jumlah_currency" id="jumlah_currency" name="jumlah_currency"
                                    type="number" step="any" min="0" placeholder="Input Harga Currency"
                                    value="{{ old('jumlah_currency') }}" readonly />
                            </div>
                            <p class="fs--1"> <b>Ket:</b> Nilai kurs akan otomatis terisi setelah memilih Jenis Kurs</p>

                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="jumlah_tukar">Jumlah Penukaran</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control" id="jumlah_tukar" name="jumlah_tukar" type="number" step="any" min="0.0001"
                                placeholder="Input Jumlah Penukaran" value="{{ old('jumlah_tukar') }}" required />
                        </div>
                        <p class="text-primary fs--1"> Calculate (IDR):
                            <span id="detailjumlahcurrency" class="detailjumlahcurrency">

                            </span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="button" onclick="tambahdata(event)">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<template id="template_delete_button">
    <button class="btn p-0" onclick="hapusdata(this)" type="button"><span class="text-700 fas fa-trash-alt"></span>
    </button>
</template>

<template id="template_add_button">
    <button class="btn btn-success btn-datatable" type="button" data-toggle="modal" data-target="#Modaltambah">
        <i class="fas fa-plus"></i>
    </button>
</template>

<style>
    .customer-picker {
        align-items: stretch;
    }

    .customer-picker .choices {
        flex: 1 1 auto;
        min-width: 0;
        margin-bottom: 0;
    }

    .customer-picker .choices__inner {
        min-height: 38px;
        padding: 6px 10px;
        font-size: .85rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .customer-picker .choices__list--single {
        padding: 2px 16px 2px 0;
    }

    .customer-picker .choices__list--dropdown {
        z-index: 1050;
    }

    .customer-add-button {
        align-self: stretch;
        width: 38px;
        height: 38px;
        padding: 0;
        font-size: .85rem;
        line-height: 1;
    }
</style>

<script>
    // Jika halaman dipulihkan dari back-forward cache (tombol Back), paksa reload agar
    // baris valas transaksi sebelumnya tidak ikut terbawa ke transaksi baru.
    window.addEventListener('pageshow', function (event) {
        var nav = performance.getEntriesByType ? performance.getEntriesByType('navigation')[0] : null;
        if (event.persisted || (nav && nav.type === 'back_forward')) {
            window.location.replace(window.location.href);
        }
    });

    var pendingTransactionData = null;
    var isTransactionDocumentModal = false;
    var initialModalAmount = @json($modal ? (float) $modal->riwayat_modal : 0);
    var passportBaseData = null;
    var currentActivePassport = '';

    function renderPassportRealtimeDisplay(currentGrandTotal) {
        if (!passportBaseData || !currentActivePassport) {
            return;
        }

        if (currentGrandTotal === undefined || currentGrandTotal === null) {
            currentGrandTotal = parseFloat($('#grand_total').data('raw-total')) || 0;
        }

        var baseThisMonth = parseFloat(passportBaseData.accumulated_this_month) || 0;
        var baseRolling = parseFloat(passportBaseData.accumulated) || 0;
        var limit = parseFloat(passportBaseData.limit) || 180000000;
        var periodeHari = passportBaseData.periode_hari || 30;

        var realtimeThisMonth = Math.round((baseThisMonth + currentGrandTotal) * 100) / 100;
        var realtimeRolling = Math.round((baseRolling + currentGrandTotal) * 100) / 100;
        var remaining = Math.max(0, limit - realtimeRolling);
        var isExceeded = realtimeRolling > limit;
        var pct = limit > 0 ? (realtimeRolling / limit) * 100 : 0;
        var pctDisplay = Math.min(100, Math.max(0, pct));

        $('#passportNumberDisplay').text(currentActivePassport.toUpperCase());
        $('#passportAccumulatedDisplay').text(formatCurrencyIdr(realtimeThisMonth));
        $('#passportLimitDisplay').text(formatCurrencyIdr(limit));

        if (currentGrandTotal > 0) {
            $('#passportCurrentOrderAmount').text(formatCurrencyIdr(currentGrandTotal));
            if (isExceeded) {
                $('#passportCurrentOrderHint').removeClass('text-primary text-warning').addClass('text-danger').show();
            } else if (pct >= 75) {
                $('#passportCurrentOrderHint').removeClass('text-primary text-danger').addClass('text-warning').show();
            } else {
                $('#passportCurrentOrderHint').removeClass('text-warning text-danger').addClass('text-primary').show();
            }
        } else {
            $('#passportCurrentOrderHint').hide();
        }

        $('#passportRollingInfo').text('Rolling ' + periodeHari + ' Hari: ' + formatCurrencyIdr(realtimeRolling));
        $('#passportRemainingInfo').text('Sisa: ' + formatCurrencyIdr(remaining));

        $('#passportProgressBar').css('width', pctDisplay + '%');

        if (isExceeded || pct >= 100) {
            $('#passportStatusPill').removeClass('bg-success bg-warning text-dark').addClass('bg-danger text-white').text('Batas Terlampaui');
            $('#passportProgressBar').removeClass('bg-primary bg-warning').addClass('bg-danger');
            $('#passportAccumulatedDisplay').removeClass('text-primary text-warning').addClass('text-danger');
        } else if (pct >= 75) {
            $('#passportStatusPill').removeClass('bg-success bg-danger text-white').addClass('bg-warning text-dark').text('Mendekati Batas');
            $('#passportProgressBar').removeClass('bg-primary bg-danger').addClass('bg-warning');
            $('#passportAccumulatedDisplay').removeClass('text-primary text-danger').addClass('text-warning');
        } else {
            $('#passportStatusPill').removeClass('bg-warning bg-danger text-dark').addClass('bg-success text-white').text('Aman');
            $('#passportProgressBar').removeClass('bg-warning bg-danger').addClass('bg-primary');
            $('#passportAccumulatedDisplay').removeClass('text-warning text-danger').addClass('text-primary');
        }

        $('#passportAccumulationBadge').slideDown(200);
    }

    var isSubmittingTransaction = false;

    function submitTransaction(data, documentData) {
        var payload = new FormData();
        Object.keys(data).forEach(function (key) {
            if (key !== 'detail' && key !== 'supporting_document_file') {
                payload.append(key, data[key] == null ? '' : data[key]);
            }
        });
        if (data.supporting_document_file instanceof File) {
            payload.append('supporting_document_file', data.supporting_document_file);
        }
        (data.detail || []).forEach(function (detail, index) {
            Object.keys(detail).forEach(function (key) {
                payload.append('detail[' + index + '][' + key + ']', detail[key]);
            });
        });
        if (documentData) {
            documentData.forEach(function (value, key) {
                payload.append(key, value);
            });
        }

        isSubmittingTransaction = true;
        $('#button_submit, #passportDocumentSubmit, #modalSaveAndProceedBtn').prop('disabled', true);
        $.ajax({
            method: 'post',
            url: '{{ route('transaksi.store') }}',
            data: payload,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                isSubmittingTransaction = false;
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = null;
                    }
                }

                if (!response || !response.id_transaksi) {
                    $('#button_submit, #passportDocumentSubmit, #modalSaveAndProceedBtn').prop('disabled', false);
                    $('#modalSaveAndProceedBtn').html('<i class="fas fa-check-circle me-1"></i>Simpan &amp; Lanjutkan Transaksi');
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal Disimpan',
                        text: (response && response.message) ? response.message : 'Server tidak mengembalikan respons yang valid. Silakan periksa kembali data Anda.'
                    });
                    return;
                }

                // 1. Bersihkan memory & form segera agar tidak ada sisa transaksi lama di tabel
                if (typeof resetTransactionForm === 'function') {
                    resetTransactionForm();
                }
                $('#passportDocumentModal').modal('hide');
                $('#customerDocumentDetailModal').modal('hide');
                pendingTransactionData = null;

                // 2. Buka struk cetak di tab baru
                if (response && response.id_transaksi) {
                    window.open('/cetak/' + response.id_transaksi, '_blank');
                }

                // 3. Tampilkan notifikasi sukses yang informatif
                var kodeTrx = (response && response.kode_transaksi) ? response.kode_transaksi : '';
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil Disimpan!',
                    html: `Transaksi <strong>${kodeTrx}</strong> telah berhasil disimpan.<br><a href="/cetak/${response.id_transaksi}" target="_blank" class="btn btn-outline-primary btn-sm mt-3"><i class="fas fa-print me-1"></i>Cetak Receipt</a>`,
                    confirmButtonText: 'Transaksi Baru',
                    allowOutsideClick: false
                }).then(function () {
                    // Reload bersih (GET baru, bukan dari cache) untuk mereset nomor urut, tabel order & sisa modal
                    window.location.replace(window.location.href);
                });
            },
            error: function (response) {
                isSubmittingTransaction = false;
                $('#button_submit, #passportDocumentSubmit, #modalSaveAndProceedBtn').prop('disabled', false);
                $('#modalSaveAndProceedBtn').html('<i class="fas fa-check-circle me-1"></i>Simpan &amp; Lanjutkan Transaksi');
                var errorData = response.responseJSON || {};
                var errorMsg = errorData.message || 'Terjadi kesalahan saat menyimpan transaksi.';
                if (errorData.errors) {
                    var errList = [];
                    Object.keys(errorData.errors).forEach(function(k) {
                        if (Array.isArray(errorData.errors[k])) {
                            errList.push(errorData.errors[k].join(', '));
                        } else {
                            errList.push(errorData.errors[k]);
                        }
                    });
                    if (errList.length > 0) {
                        errorMsg += '<br><br><div class="text-start small text-danger" style="max-height: 150px; overflow-y: auto;"><strong>Detail kesalahan:</strong><br>• ' + errList.join('<br>• ') + '</div>';
                    }
                }

                if (response.status === 422 && errorData.requires_screening_confirmation && pendingTransactionData) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Terduga Terdeteksi!',
                        text: 'Customer terdeteksi sebagai terduga. Ingin simpan transaksi?',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal'
                    }).then(function (answer) {
                        if (answer.isConfirmed) {
                            pendingTransactionData.screening_confirmed = '1';
                            submitTransaction(pendingTransactionData, documentData);
                        } else {
                            resetTransactionForm('Transaksi dibatalkan karena terduga terdeteksi.');
                        }
                    });
                    return;
                }
                if (response.status === 422 && pendingTransactionData && errorData.requires_cdd_document) {
                    openDocumentModalForTransaction(pendingTransactionData);
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi tidak dapat disimpan',
                    html: errorMsg
                });
                if (response.status === 422 && pendingTransactionData && errorData.requires_supporting_document) {
                    $('#passportDocumentModal').modal('show');
                }
            }
        });
    }

    function formatCurrencyIdr(val) {
        var num = parseFloat(val) || 0;
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: num % 1 !== 0 ? 2 : 0,
            maximumFractionDigits: 2
        }).format(num);
    }

    function formatNumberDec(val, maxDec) {
        var num = parseFloat(val) || 0;
        maxDec = maxDec || 4;
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: num % 1 !== 0 ? 2 : 0,
            maximumFractionDigits: maxDec
        }).format(num);
    }

    function recalculateTotals() {
        var grandTotal = 0;
        var detail = $('#konfirmasi').children();
        for (let index = 0; index < detail.length; index++) {
            var row = $(detail[index]);
            var totalSpan = row.find('.val-total');
            var valTotal = 0;
            if (totalSpan.length && totalSpan.data('total') !== undefined) {
                valTotal = parseFloat(totalSpan.data('total'));
            } else {
                var rawText = row.children().eq(4).text().replace(/[^\d,-]/g, '').replace(',', '.');
                valTotal = parseFloat(rawText) || 0;
            }
            grandTotal += valTotal;
        }
        grandTotal = Math.round(grandTotal * 100) / 100;

        $('#grand_total').data('raw-total', grandTotal).html(formatCurrencyIdr(grandTotal));

        var baseModal = parseFloat($('#jumlah_modal').data('base-modal') !== undefined ? $('#jumlah_modal').data('base-modal') : initialModalAmount);
        var sisaModal = Math.round((baseModal - grandTotal) * 100) / 100;
        $('#jumlah_modal').data('sisa-modal', sisaModal).html(formatCurrencyIdr(sisaModal));

        // Update realtime akumulasi paspor customer jika ada
        if (typeof renderPassportRealtimeDisplay === 'function') {
            renderPassportRealtimeDisplay(grandTotal);
        }

        return {
            grandTotal: grandTotal,
            sisaModal: sisaModal
        };
    }

    function submitdata(event) {
        event.preventDefault();
        var form = $('#form');
        var _token = form.find('input[name="_token"]').val();
        var kode_transaksi = form.find('input[name="kode_transaksi"]').val();
        var tanggal_transaksi = form.find('input[name="tanggal_transaksi"]').val();
        var id_transaksi = form.find('input[name="id_transaksi"]').val();
        var id_modal = form.find('input[name="id_modal"]').val();
        var dataform2 = [];
        var nama_customer = $('#nama_customer_hidden').val() || $('#nama_customer_input').val() || form.find('input[name="nama_customer"]').val() || '';
        var customer_alias = form.find('input[name="customer_alias"]').val();
        var nomor_passport = form.find('input[name="nomor_passport"]').val();
        var asal_negara = form.find('select[name="asal_negara"]').val();

        var detail = $('#konfirmasi').children();
        for (let index = 0; index < detail.length; index++) {
            var row = $(detail[index]);
            var spanCurrency = row.find('span[id]');
            var id_currency = spanCurrency.attr('id') || spanCurrency.data('currency-id');

            var valKurs = row.find('.val-kurs').data('kurs');
            if (valKurs === undefined) {
                valKurs = parseFloat(row.children().eq(2).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            var valJumlah = row.find('.val-jumlah').data('jumlah');
            if (valJumlah === undefined) {
                valJumlah = parseFloat(row.children().eq(3).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            var valTotal = row.find('.val-total').data('total');
            if (valTotal === undefined) {
                valTotal = parseFloat(row.children().eq(4).text().replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
            }

            if (id_currency) {
                dataform2.push({
                    currency_id: id_currency,
                    id_transaksi: id_transaksi,
                    jumlah_currency: valKurs,
                    jumlah_tukar: valJumlah,
                    total_tukar: valTotal
                });
            }
        }

        if (dataform2.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Transaksi Kosong! Tambah Transaksi Terlebih Dahulu',
            });
            return;
        }

        if (!nama_customer || !nama_customer.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Customer Belum Dipilih',
                text: 'Silakan pilih customer dari daftar atau isi nama customer terlebih dahulu.'
            });
            return;
        }

        var totals = recalculateTotals();
        var total = totals.grandTotal;
        var jumlah_modal = totals.sisaModal;

        var data = {
            _token: _token,
            customer_id: $('#customer_id').val(),
            cabang_id: form.find('select[name="cabang_id"], input[name="cabang_id"]').val(),
            screening_confirmed: $('#screening_confirmed').val(),
            kode_transaksi: kode_transaksi,
            tanggal_transaksi: tanggal_transaksi,
            id_modal: id_modal,
            total: total,
            jumlah_modal: jumlah_modal,
            nama_customer: nama_customer,
            customer_alias: customer_alias,
            nomor_passport: nomor_passport,
            status_passport: $('input[name="status_passport_radio"]:checked').val() || 'ada',
            asal_negara: asal_negara,
            npwp: ($('#doc_npwp').val() || '').trim(),
            domicile: ($('#doc_domicile').val() || '').trim(),
            income: ($('#doc_income').val() || '').trim(),
            job: ($('#doc_job').val() || '').trim(),
            company: ($('#doc_company').val() || '').trim(),
            company_form: ($('#doc_company_form').val() || '').trim(),
            position: ($('#doc_position').val() || '').trim(),
            business_sector: ($('#doc_business_sector').val() || '').trim(),
            transaction_purpose: ($('#doc_transaction_purpose').val() || '').trim(),
            relationship: ($('#doc_relationship').val() || '').trim(),
            source_of_funds: ($('#doc_source_of_funds').val() || '').trim(),
            detail: dataform2
        };

        pendingTransactionData = data;

        // Cek apakah transaksi nomor paspor ini melebihi batas regulasi BI
        var submitBtn = $('#button_submit');
        var originalBtnHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Memeriksa Batas...');

        $.post('{{ route('api.transaksi.passport-threshold') }}', {
            _token: _token,
            nomor_passport: nomor_passport,
            total: total,
            tanggal_transaksi: tanggal_transaksi || '{{ date('Y-m-d') }}'
        }).done(function (res) {
            submitBtn.prop('disabled', false).html(originalBtnHtml);
            if (res && res.exceeded) {
                // MELEWATI BATAS!
                // Munculkan pop-up modal Document dan paksa kasir untuk melengkapi isian dokumen
                openDocumentModalForTransaction(pendingTransactionData);
            } else {
                // TIDAK MELEWATI BATAS!
                // Langsung simpan transaksi tanpa membuka popup dokumen
                submitTransaction(pendingTransactionData, null);
            }
        }).fail(function () {
            submitBtn.prop('disabled', false).html(originalBtnHtml);
            // Fallback jika request threshold gagal, biarkan server memvalidasi
            submitTransaction(pendingTransactionData, null);
        });
    }

    function openDocumentModalForTransaction(data) {
        data = data || pendingTransactionData || {};
        isTransactionDocumentModal = true;
        var custName = data.nama_customer || $('#nama_customer_hidden').val() || $('#nama_customer_input').val() || 'Nasabah';

        $('#modalDocTitle').text('Document' + (custName ? ' - ' + custName : ''));
        $('#modalDocBadgeMandatory').show();
        $('#modalDocAlertWajib').show();

        var custId = data.customer_id || $('#customer_id').val();
        var custData = (custId && typeof customersData !== 'undefined' && customersData[custId]) ? customersData[custId] : {};

        // Ambil data yang ada di input form doc_ atau pendingTransactionData atau data profil customer
        $('#m_edit_npwp').val(data.npwp || custData.npwp || $('#doc_npwp').val() || '');
        $('#m_edit_position').val(data.position || custData.position || $('#doc_position').val() || '');
        $('#m_edit_domicile').val(data.domicile || custData.domicile || $('#doc_domicile').val() || '');
        $('#m_edit_business_sector').val(data.business_sector || custData.business_sector || $('#doc_business_sector').val() || '');
        $('#m_edit_income').val(data.income || custData.income || $('#doc_income').val() || '');
        $('#m_edit_transaction_purpose').val(data.transaction_purpose || custData.transaction_purpose || $('#doc_transaction_purpose').val() || '');
        $('#m_edit_job').val(data.job || custData.job || $('#doc_job').val() || '');
        $('#m_edit_relationship').val(data.relationship || custData.relationship || $('#doc_relationship').val() || '');
        $('#m_edit_company').val(data.company || custData.company || $('#doc_company').val() || '');
        $('#m_edit_source_of_funds').val(data.source_of_funds || custData.source_of_funds || $('#doc_source_of_funds').val() || '');
        $('#m_edit_company_form').val(data.company_form || custData.company_form || $('#doc_company_form').val() || '');

        // Bersihkan error validasi sebelumnya
        $('.doc-mandatory-field').removeClass('is-invalid');

        // Reset input lampiran opsional jika ada
        $('#m_edit_lampiran').val('');

        // Nonaktifkan tombol submit utama sementara modal terbuka
        $('#button_submit').prop('disabled', true);

        $('#customerDocumentDetailModal').modal('show');
    }

    function tambahdata(event, id_sparepart) {
        var form = $('#form1');
        var currencySelect = $('#currency');
        var id_currency = currencySelect.val();
        var currencyName = currencySelect.find('option:selected').text() || currencySelect.text();
        var jumlah_currency = parseFloat(form.find('input[name="jumlah_currency"]').val());
        var jumlah_tukar = parseFloat(form.find('input[name="jumlah_tukar"]').val());

        if (!id_currency || currencyName === "" || currencyName === "Pilih Kurs Terlebih Dahulu") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Currency Tidak Boleh Kosong!',
            });
            return;
        }

        var isDuplicate = false;
        var detailRows = $('#konfirmasi').children();
        for (let index = 0; index < detailRows.length; index++) {
            var span_asu = $(detailRows[index]).find('span[id]');
            if (span_asu.attr('id') == id_currency) {
                isDuplicate = true;
                break;
            }
        }
        if (isDuplicate) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Currency Tersebut Sudah Ada, Hapus Dahulu jika ingin menambahkan!',
            });
            return;
        }

        if (isNaN(jumlah_currency) || jumlah_currency <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga Currency Tidak Boleh Bernilai 0 atau Kosong!',
            });
            return;
        }

        if (isNaN(jumlah_tukar) || jumlah_tukar <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Jumlah Tukar Tidak Boleh Bernilai 0 atau Kosong!',
            });
            return;
        }

        var total_tukar = Math.round((jumlah_tukar * jumlah_currency) * 100) / 100;

        var baseModal = parseFloat($('#jumlah_modal').data('base-modal') !== undefined ? $('#jumlah_modal').data('base-modal') : initialModalAmount);
        var currentGrandTotal = parseFloat($('#grand_total').data('raw-total')) || 0;
        var projectedTotal = Math.round((currentGrandTotal + total_tukar) * 100) / 100;

        if (projectedTotal > baseModal) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon Maaf Modal Anda Kurang Dari Transaksi, Lakukan Penambahan!',
            });
            return;
        }

        var harga_currency_display = `<span class="val-kurs" data-kurs="${jumlah_currency}">${formatCurrencyIdr(jumlah_currency)}</span>`;
        var jumlah_tukar_display = `<span class="val-jumlah" data-jumlah="${jumlah_tukar}">${formatNumberDec(jumlah_tukar)}</span>`;
        var total_tukar_display = `<span class="val-total" data-total="${total_tukar}">${formatCurrencyIdr(total_tukar)}</span>`;

        var table = $('#dataTableKonfirmasi').DataTable();
        table.row.add([
            total_tukar_display,
            `<span id="${id_currency}" data-currency-id="${id_currency}">${currencyName}</span>`,
            harga_currency_display,
            jumlah_tukar_display,
            total_tukar_display,
            total_tukar_display
        ]).draw();

        recalculateTotals();

        // Close and reset modal
        $('#btn-close-modal').click();
        $('#form1')[0].reset();
        $('#detailjumlahcurrency').html(formatCurrencyIdr(0));

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
            title: 'Berhasil Menambahkan Data Transaksi'
        });
    }

    function hapusdata(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var table = $('#dataTableKonfirmasi').DataTable();
                var row = $(element).closest('tr');
                table.row(row).remove().draw();
                recalculateTotals();
            }
        });
    }

    $(document).ready(function () {
        let customerChoicesInstance = null;
        let screeningConfirmed = false;
        let skipNextCustomerScreening = false;
        let clearingCustomer = false;
        const customersData = @json($customers->keyBy('customer_id'));

        function initCustomerChoices() {
            const el = document.getElementById('customerSelect');
            if (!el) return;
            if (el.closest('.choices')) {
                console.log('[CustomerSelect] sudah diinisialisasi oleh theme.js');
                return;
            }
            if (typeof Choices !== 'undefined') {
                try {
                    customerChoicesInstance = new Choices(el, {
                        removeItemButton: true,
                        placeholder: true,
                        shouldSort: false,
                        searchPlaceholderValue: 'Cari nama, alias, passport, NIK, atau negara',
                        noResultsText: 'Customer tidak ditemukan',
                        itemSelectText: ''
                    });
                    console.log('[CustomerSelect] diinisialisasi manual via Choices');
                } catch (err) {
                    console.error('[CustomerSelect] Gagal inisialisasi Choices:', err);
                }
            } else {
                console.warn('[CustomerSelect] Choices belum siap');
            }
        }

        initCustomerChoices();
        $(window).on('load', initCustomerChoices);

        function clearCustomer() {
            screeningConfirmed = false;
            currentActivePassport = '';
            passportBaseData = null;
            lastManualPassport = '';
            $('#passport_ada').prop('checked', true);
            $('#nomor_passport').prop('readonly', false).removeClass('bg-200 text-primary fw-semi-bold');
            $('#passport_auto_hint').hide();
            $('#screening_confirmed').val('0');
            $('#customer_id, #nama_customer_hidden, #customer_alias_hidden, #nomor_passport, #asal_negara_select').val('');
            $('#doc_npwp, #doc_domicile, #doc_income, #doc_job, #doc_company, #doc_company_form, #doc_position, #doc_business_sector, #doc_transaction_purpose, #doc_relationship, #doc_source_of_funds').val('');
            // Clear visible fields juga
            $('#nama_customer_input').val('');
            $('#passportAccumulationBadge').slideUp(150);
            $('#passportCurrentOrderHint').hide();
            $('#customerCreatePassportInfo').slideUp(150);
            if (!clearingCustomer) {
                clearingCustomer = true;
                if (customerChoicesInstance) {
                    try {
                        customerChoicesInstance.removeActiveItems();
                    } catch (e) {
                        console.warn(e);
                    }
                } else {
                    const removeBtn = document.querySelector('.customer-picker .choices__button');
                    if (removeBtn) {
                        removeBtn.click();
                    } else {
                        $('#customerSelect').val('');
                    }
                }
                clearingCustomer = false;
            }
        }

        function fetchPassportThreshold(passportNumber, context) {
            passportNumber = (passportNumber || '').trim();
            if (!passportNumber) {
                if (!context || context === 'main') {
                    currentActivePassport = '';
                    passportBaseData = null;
                    $('#passportAccumulationBadge').slideUp(150);
                    $('#passportCurrentOrderHint').hide();
                }
                if (!context || context === 'modal') {
                    $('#customerCreatePassportInfo').slideUp(150);
                }
                return;
            }

            if (!context || context === 'main') {
                currentActivePassport = passportNumber;
            }

            $.post('{{ route('api.transaksi.passport-threshold') }}', {
                _token: '{{ csrf_token() }}',
                nomor_passport: passportNumber,
                total: 0,
                tanggal_transaksi: $('#tanggal_transaksi').val() || '{{ date('Y-m-d') }}'
            }).done(function (res) {
                // Update Main Customer Widget via realtime render
                if (!context || context === 'main') {
                    passportBaseData = res;
                    var currentGrandTotal = parseFloat($('#grand_total').data('raw-total')) || 0;
                    renderPassportRealtimeDisplay(currentGrandTotal);
                }

                // Update Modal Tambah Customer Info
                if (!context || context === 'modal') {
                    var periodeHariModal = res.periode_hari || 30;
                    $('#customerCreatePassportTotal').text(`${res.accumulated_this_month_formatted} / ${res.limit_formatted}`);
                    var pctModal = parseFloat(res.percentage_this_month) || parseFloat(res.percentage) || 0;
                    if (res.exceeded || pctModal >= 100) {
                        $('#customerCreatePassportNote').html('<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>Akumulasi paspor ini melebihi batas regulasi BI (' + res.limit_formatted + '). Transaksi memerlukan dokumen pendukung.</span>');
                    } else {
                        $('#customerCreatePassportNote').html(`<span class="text-success"><i class="fas fa-check-circle me-1"></i>Sisa kuota bulan ini: <strong>${res.remaining_formatted}</strong> (Rolling ${periodeHariModal} Hari: ${res.accumulated_formatted})</span>`);
                    }
                    $('#customerCreatePassportInfo').slideDown(200);
                }
            }).fail(function () {
                console.warn('Gagal memuat batas passport');
            });
        }

        window.resetTransactionForm = function (message) {
            var table = $('#dataTableKonfirmasi').DataTable();
            table.clear().draw();
            $('#form1')[0].reset();
            clearCustomer();
            $('#customerForm').hide();
            $('.customer-picker').show();
            $('#addCustomerButton').show();
            $('#toggleCustomerForm').hide();
            $('#validationResult').empty().hide();
            $('#grand_total').data('raw-total', 0).html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(0));
            $('#jumlah_modal').html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(initialModalAmount));
            $('#detailjumlahcurrency').html(new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR'
            }).format(0));
            $('#passportDocumentModal').modal('hide');
            $('#passportDocumentForm')[0].reset();
            pendingTransactionData = null;
            $('#button_submit, #passportDocumentSubmit').prop('disabled', false);
            if (typeof refreshNextIncrementalPassport === 'function') {
                refreshNextIncrementalPassport();
            }

            if (message) {
                Swal.fire({
                    icon: 'info',
                    title: 'Transaksi Dibatalkan',
                    text: message
                });
            }
        };

        function showScreeningPrompt(onContinue, onCancel) {
            Swal.fire({
                icon: 'warning',
                title: 'Terduga terdeteksi',
                text: 'Nama atau alias masuk Daftar Terorisme. Lanjutkan transaksi?',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal'
            }).then(function (answer) {
                if (answer.isConfirmed) return onContinue();
                clearCustomer();
                if (onCancel) onCancel();
            });
        }

        $('#transaction_cabang_id').on('change', function () {
            const cabangId = $(this).val();
            if (cabangId) window.location.href = '{{ route('transaksi.create') }}?cabang_id=' + encodeURIComponent(cabangId);
        });

        $('#customerSelect').on('change', function () {
            const selected = $(this).val();
            if (clearingCustomer) return;
            if (!selected) return clearCustomer();
            const item = customersData[selected];
            if (!item) return;

            const skipScreening = skipNextCustomerScreening;
            skipNextCustomerScreening = false;
            if (!skipScreening) {
                screeningConfirmed = false;
                $('#screening_confirmed').val('0');
            }
            $('#customer_id').val(item.customer_id);
            $('#nama_customer_hidden').val(item.name);
            $('#nama_customer_input').val(item.name);
            $('#customer_alias_hidden').val(item.alias || '');
            const custPassport = item.passport || '';
            $('#nomor_passport').val(custPassport);
            if (custPassport && /^0000\d{5}$/.test(custPassport)) {
                $('#passport_tidak_ada').prop('checked', true);
                $('#nomor_passport').prop('readonly', true).addClass('bg-200 text-primary fw-semi-bold');
                $('#passport_auto_number_text').text(custPassport);
                $('#passport_auto_hint').show();
            } else {
                $('#passport_ada').prop('checked', true);
                $('#nomor_passport').prop('readonly', false).removeClass('bg-200 text-primary fw-semi-bold');
                $('#passport_auto_hint').hide();
            }
            $('#asal_negara_select').val(item.country || '').trigger('change');
            if (!skipScreening) screenCustomer(item.name, item.alias || '');

            // Isi input Document & kartu preview info Document (sesuai gambar)
            $('#doc_npwp').val(item.npwp || '');
            $('#doc_domicile').val(item.domicile || '');
            $('#doc_income').val(item.income || '');
            $('#doc_job').val(item.job || '');
            $('#doc_company').val(item.company || '');
            $('#doc_company_form').val(item.company_form || '');
            $('#doc_position').val(item.position || '');
            $('#doc_business_sector').val(item.business_sector || '');
            $('#doc_transaction_purpose').val(item.transaction_purpose || '');
            $('#doc_relationship').val(item.relationship || '');
            $('#doc_source_of_funds').val(item.source_of_funds || '');

            if (item && item.passport) {
                fetchPassportThreshold(item.passport, 'main');
            } else {
                currentActivePassport = '';
                passportBaseData = null;
                $('#passportAccumulationBadge').slideUp(150);
                $('#passportCurrentOrderHint').hide();
            }

            // Sembunyikan form manual jika memilih dari dropdown
            $('#customerForm').hide();
            $('#toggleCustomerForm').hide();
        });

        $('#addCustomerButton').on('click', function () {
            // Tampilkan form manual dan sembunyikan dropdown
            $('#customerForm').show();
            $('#doc_npwp, #doc_domicile, #doc_income, #doc_job, #doc_company, #doc_company_form, #doc_position, #doc_business_sector, #doc_transaction_purpose, #doc_relationship, #doc_source_of_funds').val('');
            $('.customer-picker').hide();
            $(this).hide();
            $('#toggleCustomerForm').show();
            const currentPassport = $('#nomor_passport').val();
            if (currentPassport) {
                fetchPassportThreshold(currentPassport, 'main');
            }
        });

        let nextIncrementalPassport = '{{ $nextIncrementalPassport ?? '' }}';
        let lastManualPassport = '';
        let lastModalManualPassport = '';

        function refreshNextIncrementalPassport(callback) {
            $.get('{{ route('api.transaksi.next-passport') }}')
                .done(function (res) {
                    if (res && res.next_passport) {
                        nextIncrementalPassport = res.next_passport;
                    }
                    if (typeof callback === 'function') callback(nextIncrementalPassport);
                })
                .fail(function () {
                    if (typeof callback === 'function') callback(nextIncrementalPassport);
                });
        }

        $('input[name="status_passport_radio"]').on('change', function () {
            const status = $(this).val();
            const passportInput = $('#nomor_passport');
            const autoHint = $('#passport_auto_hint');
            const autoNumberText = $('#passport_auto_number_text');

            if (status === 'tidak_ada') {
                const currentVal = passportInput.val();
                if (currentVal && !/^0000\d{5}$/.test(currentVal)) {
                    lastManualPassport = currentVal;
                }
                const applyAuto = function (num) {
                    passportInput.val(num);
                    passportInput.prop('readonly', true).addClass('bg-200 text-primary fw-semi-bold');
                    autoNumberText.text(num);
                    autoHint.slideDown(150);
                    fetchPassportThreshold(num, 'main');
                };

                if (nextIncrementalPassport) {
                    applyAuto(nextIncrementalPassport);
                }
                refreshNextIncrementalPassport(function (freshNum) {
                    if ($('input[name="status_passport_radio"]:checked').val() === 'tidak_ada') {
                        applyAuto(freshNum);
                    }
                });
            } else {
                passportInput.prop('readonly', false).removeClass('bg-200 text-primary fw-semi-bold');
                autoHint.slideUp(150);
                if (lastManualPassport && !/^0000\d{5}$/.test(lastManualPassport)) {
                    passportInput.val(lastManualPassport);
                    fetchPassportThreshold(lastManualPassport, 'main');
                } else {
                    passportInput.val('');
                    fetchPassportThreshold('', 'main');
                }
                passportInput.focus();
            }
        });

        $('input[name="modal_status_passport_radio"]').on('change', function () {
            const status = $(this).val();
            const input = $('#modalCustomerPassportInput');
            const hint = $('#modal_passport_auto_hint');
            const text = $('#modal_passport_auto_number_text');

            if (status === 'tidak_ada') {
                const cur = input.val();
                if (cur && !/^0000\d{5}$/.test(cur)) {
                    lastModalManualPassport = cur;
                }
                const applyAutoModal = function (num) {
                    input.val(num);
                    input.prop('readonly', true).addClass('bg-200 text-primary fw-semi-bold');
                    text.text(num);
                    hint.slideDown(150);
                    fetchPassportThreshold(num, 'modal');
                };

                if (nextIncrementalPassport) {
                    applyAutoModal(nextIncrementalPassport);
                }
                refreshNextIncrementalPassport(function (freshNum) {
                    if ($('input[name="modal_status_passport_radio"]:checked').val() === 'tidak_ada') {
                        applyAutoModal(freshNum);
                    }
                });
            } else {
                input.prop('readonly', false).removeClass('bg-200 text-primary fw-semi-bold');
                hint.slideUp(150);
                if (lastModalManualPassport && !/^0000\d{5}$/.test(lastModalManualPassport)) {
                    input.val(lastModalManualPassport);
                    fetchPassportThreshold(lastModalManualPassport, 'modal');
                } else {
                    input.val('');
                    fetchPassportThreshold('', 'modal');
                }
                input.focus();
            }
        });

        let passportDebounceTimer = null;
        $('#nomor_passport').on('input', function () {
            clearTimeout(passportDebounceTimer);
            const val = $(this).val();
            passportDebounceTimer = setTimeout(function () {
                fetchPassportThreshold(val, 'main');
            }, 300);
        });

        let modalPassportDebounce = null;
        $('#modalCustomerPassportInput').on('input', function () {
            clearTimeout(modalPassportDebounce);
            const val = $(this).val();
            modalPassportDebounce = setTimeout(function () {
                fetchPassportThreshold(val, 'modal');
            }, 300);
        });

        $('#customerCreateModal').on('hidden.bs.modal', function () {
            $('#customerCreatePassportInfo').hide();
            $('#modal_passport_auto_hint').hide();
            $('#modal_passport_ada').prop('checked', true);
            $('#modalCustomerPassportInput').prop('readonly', false).removeClass('bg-200 text-primary fw-semi-bold');
        });

        function screenCustomer(name, alias) {
            $.post('{{ route('api.customer.screen') }}', {
                _token: '{{ csrf_token() }}',
                name: name,
                alias: alias
            }).done(function (result) {
                if (!result.matched) return;
                showScreeningPrompt(function () {
                    screeningConfirmed = true;
                    $('#screening_confirmed').val('1');
                });
            }).fail(function () {
                Swal.fire('Gagal', 'Screening customer gagal.', 'error');
                clearCustomer();
            });
        }

        $('#passportDocumentForm').on('submit', function (event) {
            event.preventDefault();
            if (!pendingTransactionData) return;
            submitTransaction(pendingTransactionData, new FormData(this));
        });

        $('#passportDocumentModal').on('hidden.bs.modal', function () {
            $('#button_submit').prop('disabled', false);
        });

        $('#customerCreateForm').on('submit', function (event) {
            event.preventDefault();
            const form = $(this);
            const data = Object.fromEntries(new FormData(this).entries());
            const cabangId = $('#transaction_cabang_id').val() || $('input[name="cabang_id"]').val();
            if (cabangId) data.cabang_terdaftar = cabangId;
            $.post('{{ route('api.customer.screen') }}', data)
                .done(function (result) {
                    const save = function () {
                        $.ajax({
                            url: '{{ route('api.customer.store') }}',
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                const item = response.customer;
                                customersData[item.customer_id] = item;
                                const label = item.name + (item.alias ? ' - ' + item.alias : '') + (item.passport ? ' [' + item.passport + ']' : '');
                                if (customerChoicesInstance) {
                                    const newCustomerChoice = {
                                        value: String(item.customer_id),
                                        label: label,
                                        customProperties: item
                                    };
                                    customerChoicesInstance.setChoices([newCustomerChoice], 'value', 'label', false);
                                    skipNextCustomerScreening = true;
                                    customerChoicesInstance.setChoiceByValue(String(item.customer_id));
                                } else {
                                    $('#customerSelect').append(new Option(label, item.customer_id, true, true));
                                }
                                $('#customerCreateModal').modal('hide');
                                skipNextCustomerScreening = true;
                                $('#customerSelect').val(String(item.customer_id)).trigger('change');
                            },
                            error: function (response) {
                                const message = response.responseJSON && response.responseJSON.message;
                                Swal.fire('Gagal', message || 'Customer tidak dapat disimpan.', 'error');
                            }
                        });
                    };
                    if (!result.matched) return save();
                    showScreeningPrompt(function () {
                        screeningConfirmed = true;
                        $('#screening_confirmed').val('1');
                        save();
                    });
                })
                .fail(function () {
                    Swal.fire('Gagal', 'Screening customer gagal.', 'error');
                });
        });

        // Simpan customer manual (inline form) ke tabel master customer
        $('#saveCustomerManualBtn').on('click', function () {
            const nama = ($('#nama_customer_input').val() || '').trim();
            const passport = ($('#nomor_passport').val() || '').trim();
            const country = $('#asal_negara_select').val() || 'INDONESIA';
            const cabangId = $('#transaction_cabang_id').val() || $('input[name="cabang_id"]').val();

            if (!nama) {
                Swal.fire('Peringatan', 'Nama customer wajib diisi.', 'warning');
                return;
            }

            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

            const data = {
                _token: '{{ csrf_token() }}',
                name: nama,
                alias: nama,
                passport: passport,
                country: country,
                cabang_terdaftar: cabangId,
                npwp: ($('#doc_npwp').val() || '').trim(),
                domicile: ($('#doc_domicile').val() || '').trim(),
                income: ($('#doc_income').val() || '').trim(),
                job: ($('#doc_job').val() || '').trim(),
                company: ($('#doc_company').val() || '').trim(),
                company_form: ($('#doc_company_form').val() || '').trim(),
                position: ($('#doc_position').val() || '').trim(),
                business_sector: ($('#doc_business_sector').val() || '').trim(),
                transaction_purpose: ($('#doc_transaction_purpose').val() || '').trim(),
                relationship: ($('#doc_relationship').val() || '').trim(),
                source_of_funds: ($('#doc_source_of_funds').val() || '').trim()
            };

            $.post('{{ route('api.customer.screen') }}', data)
                .done(function (result) {
                    const doSave = function () {
                        $.ajax({
                            url: '{{ route('api.customer.store') }}',
                            method: 'POST',
                            data: data,
                            success: function (response) {
                                const item = response.customer;
                                customersData[item.customer_id] = item;
                                const label = item.name + (item.alias ? ' - ' + item.alias : '') + (item.passport ? ' [' + item.passport + ']' : '');
                                if (customerChoicesInstance) {
                                    const newCustomerChoice = {
                                        value: String(item.customer_id),
                                        label: label,
                                        customProperties: item
                                    };
                                    customerChoicesInstance.setChoices([newCustomerChoice], 'value', 'label', false);
                                    skipNextCustomerScreening = true;
                                    customerChoicesInstance.setChoiceByValue(String(item.customer_id));
                                } else {
                                    $('#customerSelect').append(new Option(label, item.customer_id, true, true));
                                }

                                // Reset form manual dan kembali ke picker dropdown
                                $('#customerForm').hide();
                                $('.customer-picker').show();
                                $('#addCustomerButton').show();
                                $('#toggleCustomerForm').hide();

                                skipNextCustomerScreening = true;
                                $('#customerSelect').val(String(item.customer_id)).trigger('change');

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Customer baru berhasil disimpan ke Master Data Customer.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function (response) {
                                const message = response.responseJSON && response.responseJSON.message;
                                Swal.fire('Gagal', message || 'Customer tidak dapat disimpan.', 'error');
                            },
                            complete: function () {
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    };

                    if (!result.matched) return doSave();
                    showScreeningPrompt(function () {
                        screeningConfirmed = true;
                        $('#screening_confirmed').val('1');
                        doSave();
                    }, function () {
                        btn.prop('disabled', false).html(originalText);
                    });
                })
                .fail(function () {
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire('Gagal', 'Screening customer gagal.', 'error');
                });
        });

        $('.jumlah_currency').each(function () {
            $(this).on('input', function () {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(harga)

                var jumlah = $(this).parent().parent().find('.detailjumlahcurrency')
                $(jumlah).html(harga_fix);
            })
        })
        // TES
        $('select[name="id_currency"]').on('change', function () {
            var id_currency = $(this).val();

            if (id_currency) {
                $.ajax({
                    url: 'getkurs/' + id_currency,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('input[name="jumlah_currency"]').val(data[0]);
                    },
                    error: function (response) {
                        console.log(response)
                    }
                });
            } else {
                $('input[name="jumlah_currency"]').empty();
            }
        });



        $('#jumlah_tukar').on('input', function () {
            var value = parseFloat($(this).val()) || 0;
            var nilai_kurs = parseFloat($('.jumlah_currency').val()) || 0;
            var calculate = Math.round((value * nilai_kurs) * 100) / 100;
            $('#detailjumlahcurrency').html(formatCurrencyIdr(calculate));
        });

        var template = $('#template_delete_button').html()
        $('#dataTableKonfirmasi').DataTable({
            "paging": false,
            "ordering": false,
            "info": false,
            "searching": false,
            "columnDefs": [{
                    "targets": -1,
                    "data": null,
                    "defaultContent": template
                },
                {
                    "targets": 0,
                    "data": null,
                    'render': function (data, type, row, meta) {
                        return meta.row + 1
                    }
                }
            ]
        });

        // Tombol kembali ke pencarian customer - menggunakan event delegation
        $(document).on('click', '#backToSearchButton', function() {
            $('#customerForm').hide();
            $('.customer-picker').show();
            $('#addCustomerButton').show();
            $('#toggleCustomerForm').hide();
            clearCustomer();
        });

        // Sync input visible dengan input hidden
        $('#nama_customer_input').on('input', function() {
            $('#nama_customer_hidden').val($(this).val());
            // Untuk kompatibilitas, isi customer_alias dengan nilai yang sama
            $('#customer_alias_hidden').val($(this).val());
        });

        // Negara dirender dari countries.json oleh controller dan dibuat searchable.
        if (typeof Choices !== 'undefined') {
            try {
                new Choices('#asal_negara_select', {
                    searchEnabled: true,
                    shouldSort: false,
                    itemSelectText: '',
                    noResultsText: 'Negara tidak ditemukan',
                    noChoicesText: 'Ketik untuk mencari negara'
                });
            } catch (e) {
                console.warn('Init asal_negara_select Choices failed:', e);
            }
        }

        // Validasi terduga (hanya dari nama, cek ke name dan alias di tabel)
        $('#validateTerdugaButton').on('click', function() {
            const nama = $('#nama_customer_input').val().trim();

            if (!nama) {
                Swal.fire('Peringatan', 'Nama customer harus diisi.', 'warning');
                return;
            }

            const button = $(this);
            const originalText = button.html();
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Validating...');

            $.ajax({
                url: '{{ route('api.transaksi.validate-terduga') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nama: nama,
                    alias: ''  // Tidak perlu alias input terpisah
                },
                success: function(response) {
                    const resultDiv = $('#validationResult');
                    resultDiv.show();

                    if (response.terduga) {
                        resultDiv.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terduga Terdeteksi!</strong><br>
                                ${response.message}<br>
                                <small>Term yang dicari: ${response.search_terms.join(', ')}</small>
                            </div>
                        `);
                        $('#screening_confirmed').val('0');
                        showScreeningPrompt(function () {
                            screeningConfirmed = true;
                            $('#screening_confirmed').val('1');
                        }, function () {
                            resetTransactionForm('Transaksi dibatalkan karena terduga terdeteksi.');
                        });
                    } else {
                        resultDiv.html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Aman</strong><br>
                                ${response.message}<br>
                                <small>Term yang dicari: ${response.search_terms.join(', ')}</small>
                            </div>
                        `);
                        $('#screening_confirmed').val('1');
                        screeningConfirmed = true;
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat validasi.', 'error');
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        });

        // Input validation handler: bersihkan status error saat user mengetik
        $('.doc-mandatory-field').on('input change', function () {
            if ($(this).val().trim()) {
                $(this).removeClass('is-invalid');
            }
        });

        // Handler saat modal Document ditutup
        $('#customerDocumentDetailModal').on('hidden.bs.modal', function () {
            isTransactionDocumentModal = false;
            if (!isSubmittingTransaction) {
                $('#button_submit').prop('disabled', false);
            }
            $('.doc-mandatory-field').removeClass('is-invalid');
        });

        // Handler tombol Simpan & Lanjutkan Transaksi pada modal Document
        $('#modalSaveAndProceedBtn').on('click', function (e) {
            e.preventDefault();
            var saveBtn = $('#modalSaveAndProceedBtn');

            if (!pendingTransactionData) {
                Swal.fire('Error', 'Data transaksi tidak ditemukan. Silakan ulangi dengan klik tombol Confirm & Pay.', 'error');
                $('#customerDocumentDetailModal').modal('hide');
                return;
            }

            var missingLabels = [];
            var fieldsToCheck = [
                { id: 'm_edit_npwp', label: 'NPWP (TIN)' },
                { id: 'm_edit_position', label: 'Position' },
                { id: 'm_edit_domicile', label: 'Domicile' },
                { id: 'm_edit_business_sector', label: 'Business Sector' },
                { id: 'm_edit_income', label: 'Income' },
                { id: 'm_edit_transaction_purpose', label: 'Transaction Purpose' },
                { id: 'm_edit_job', label: 'Job' },
                { id: 'm_edit_relationship', label: 'Relationship (if represented)' },
                { id: 'm_edit_company', label: 'Company' },
                { id: 'm_edit_source_of_funds', label: 'Source of funds' },
                { id: 'm_edit_company_form', label: 'Company Form' }
            ];

            $('.doc-mandatory-field').removeClass('is-invalid');

            fieldsToCheck.forEach(function (f) {
                var val = ($('#' + f.id).val() || '').trim();
                if (!val) {
                    $('#' + f.id).addClass('is-invalid');
                    missingLabels.push(f.label);
                }
            });

            if (missingLabels.length > 0) {
                var firstInvalid = $('.doc-mandatory-field.is-invalid').first();
                if (firstInvalid.length) {
                    firstInvalid.focus();
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen Wajib Dilengkapi!',
                    html: `Seluruh 11 isian Dokumen nasabah (CDD / KYC) <strong>wajib diisi lengkap</strong> untuk melanjutkan transaksi.<br><br><div class="text-start small text-danger" style="max-height: 160px; overflow-y: auto;"><strong>Bidang yang belum diisi:</strong><br>• ` + missingLabels.join('<br>• ') + `</div>`,
                    confirmButtonText: 'Lengkapi Sekarang'
                });
                return;
            }

            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan &amp; Memproses...');

            // Simpan seluruh 11 field ke objek pendingTransactionData transaksi ini
            pendingTransactionData.npwp = $('#m_edit_npwp').val().trim();
            pendingTransactionData.position = $('#m_edit_position').val().trim();
            pendingTransactionData.domicile = $('#m_edit_domicile').val().trim();
            pendingTransactionData.business_sector = $('#m_edit_business_sector').val().trim();
            pendingTransactionData.income = $('#m_edit_income').val().trim();
            pendingTransactionData.transaction_purpose = $('#m_edit_transaction_purpose').val().trim();
            pendingTransactionData.job = $('#m_edit_job').val().trim();
            pendingTransactionData.relationship = $('#m_edit_relationship').val().trim();
            pendingTransactionData.company = $('#m_edit_company').val().trim();
            pendingTransactionData.source_of_funds = $('#m_edit_source_of_funds').val().trim();
            pendingTransactionData.company_form = $('#m_edit_company_form').val().trim();

            // Lampiran dokumen opsional (jika diunggah)
            var lampiranInput = document.getElementById('m_edit_lampiran');
            if (lampiranInput && lampiranInput.files && lampiranInput.files.length > 0) {
                pendingTransactionData.supporting_document_file = lampiranInput.files[0];
            } else {
                delete pendingTransactionData.supporting_document_file;
            }

            // Update form inputs di halaman
            $('#doc_npwp').val(pendingTransactionData.npwp);
            $('#doc_position').val(pendingTransactionData.position);
            $('#doc_domicile').val(pendingTransactionData.domicile);
            $('#doc_business_sector').val(pendingTransactionData.business_sector);
            $('#doc_income').val(pendingTransactionData.income);
            $('#doc_transaction_purpose').val(pendingTransactionData.transaction_purpose);
            $('#doc_job').val(pendingTransactionData.job);
            $('#doc_relationship').val(pendingTransactionData.relationship);
            $('#doc_company').val(pendingTransactionData.company);
            $('#doc_source_of_funds').val(pendingTransactionData.source_of_funds);
            $('#doc_company_form').val(pendingTransactionData.company_form);

            // Tutup modal dokumen
            $('#customerDocumentDetailModal').modal('hide');

            // Lanjutkan eksekusi penyimpanan transaksi
            submitTransaction(pendingTransactionData, null);
        });

        // Set nilai awal dari hidden fields ke visible fields jika ada data old
        @if(old('nama_customer'))
            // Jika ada data old, tampilkan form manual
            $('#customerForm').show();
            $('.customer-picker').hide();
            $('#addCustomerButton').hide();
            $('#toggleCustomerForm').show();
            $('#nama_customer_input').val('{{ old('nama_customer') }}');
        @endif

        @if(old('status_passport_radio') === 'tidak_ada')
            $('#passport_tidak_ada').prop('checked', true).trigger('change');
        @elseif(old('nomor_passport'))
            fetchPassportThreshold('{{ old('nomor_passport') }}', 'main');
        @endif
    });
</script>

@endsection