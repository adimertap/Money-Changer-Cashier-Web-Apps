@extends('layouts.app')

@section('content')
<main>
    <div class="row g-3 mb-3 mt-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-8 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Tambah Data!</h5>
                            <p>Tambah Data Currency Disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-tambah">Tambah Data</button>
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
                            <h5 class="text-primary mb-1">Edit Data!</h5>
                            <p>Edit Data Keseluruhan Currency Disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-edit-kurs">Edit Data Kurs</button>
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
                            <h6 class="mb-md-0 mb-lg-2">Total Currency</h6><span
                                class="badge rounded-pill badge-soft-success">Currency</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-2 fw-normal text-700"><span>{{ $lembar }}</span> Lembar, <span>{{ $coins }}
                                    Coins</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Data Currency</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Currency</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="tableExample"
                data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                <div class="table-responsive scrollbar">
                    <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="sort text-center" data-sort="no">No.</th>
                                <th class="sort text-center" data-sort="nama_currency">Nama Currency</th>
                                <th class="sort text-center" data-sort="country">Country</th>
                                <th class="sort text-center" data-sort="jenis">Jenis</th>
                                <th class="sort text-center" data-sort="kurs">Kurs (Rp)</th>
                                <th class="sort text-center" data-sort="keterangan">Keterangan</th>
                                <th class="sort text-center" data-sort="urutan">Key</th>
                                <th class="sort text-center" data-sort="flag">Flag</th>
                                <th class="sort text-center" data-sort="kurs">Jumlah / Last Sell</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($currency as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="nama_currency">{{ $item->nama_currency }}</td>
                                <td class="country">{{ $item->country }}</td>
                                <td class="jenis text-center">
                                    @if($item->jenis_kurs == 'Lembar')
                                    <span class="badge badge-soft-primary">{{ $item->jenis_kurs }}</span>
                                    @else
                                    <span class="badge badge-soft-warning">{{ $item->jenis_kurs }}</span>
                                    @endif

                                </td>
                                <td class="kurs text-center">
                                    {{ number_format($item->nilai_kurs, 2, ',', '.') }}</td>
                                <td class="keterangan">
                                    @if(!$item->keterangan)
                                    <p>-</p>
                                    @else
                                    {{ $item->keterangan }}
                                    @endif
                                </td>
                                <td class="urutan">
                                    {{ $item->urutan }}
                                </td>
                                <td class="flag text-center">
                                    @if($item->img_flag == '' || $item->img_flag == null )
                                    <p>Gambar Kosong</p>
                                    @else
                                    <img src="{{ $item->img_flag }}" alt="flag" height="30" />
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$item->jumlah_valas) 
                                    0 / {{ number_format($item->last_nilai_jual, 2, ',', '.') }}
                                    @else
                                    {{ $item->jumlah_valas }} / {{ number_format($item->last_nilai_jual, 2, ',', '.') }}
                                    @endif
                                    </td>
                                <td class="text-center">
                                    <button class="btn p-0 ms-2 editCurrencyBtn" value="{{ $item->id_currency }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Uplaod Gambar"><span class="text-700 fas fa-edit"></span>
                                    </button>
                                    <button class="btn p-0 ms-2 deleteCurrencyBtn" value="{{ $item->id_currency }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Delete"><span class="text-700 fas fa-trash-alt"></span>
                                    </button>

                                </td>
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

<div class="modal fade" id="modal-edit-kurs" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 1350px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <a href="{{ route('master-currency') }}" class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    aria-label="Close"></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div id="tableExample"
                        data-list='{"valueNames":["no","enama_currency","enama_country","ejenis","ekurs",
                            "eketerangan","eurutan","eflag"],"page":20,"pagination":true}'>
                        <div class="table-responsive scrollbar">
                            <table class="table table-bordered table-striped fs--1 mb-0" id="datatable2">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th class="sort text-center" data-sort="no">No.</th>
                                        <th class="sort text-center" data-sort="enama_currency">Currency</th>
                                        <th class="sort text-center" data-sort="enama_country">Country</th>
                                        <th class="sort text-center" data-sort="ejenis">Jenis</th>
                                        <th class="sort text-center" data-sort="ekurs">Kurs(Rp)</th>
                                        <th class="sort text-center" data-sort="eketerangan">Keterangan.</th>
                                        <th class="sort text-center" data-sort="eurutan">Urutan</th>
                                        <th class="sort text-center" data-sort="eflag">Flag</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @forelse ($currency as $item)
                                    <tr role="row" class="odd">
                                        <input type="hidden" name="_token" id="token_edit" value="{{ csrf_token() }}">
                                        <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                        <td class="enama_currency bg-soft-primary">
                                            <input class="form-control form-control-sm nama_edit" name="nama_edit"
                                            type="text" placeholder="Nama Kurs" id="{{ $item->id_currency }}"
                                            value="{{ $item->nama_currency }}" />
                                        </td>
                                        <td class="enama_country">
                                            <input class="form-control form-control-sm country_edit" name="country_edit"
                                            type="text" placeholder="Country" id="{{ $item->id_currency }}"
                                            value="{{ $item->country }}" />
                                        </td>
                                        <td class="ejenis text-center">
                                            <select class="form-select form-select-sm jenis_edit" name="jenis_edit"
                                                type="text" placeholder="Jenis" id="{{ $item->id_currency }}" value="{{ $item->urutan }}">
                                                <option value="{{ $item->jenis_kurs }}">{{ $item->jenis_kurs ?? 'Pilih Jenis' }}</option>
                                                <option value="Lembar">Lembar</option>
                                                <option value="Coins">Coins</option>
                                            </select>

                                            {{-- @if($item->jenis_kurs == 'Lembar')
                                            <span class="badge badge-soft-primary">{{ $item->jenis_kurs }}</span>
                                            @else
                                            <span class="badge badge-soft-warning">{{ $item->jenis_kurs }}</span>
                                            @endif --}}
                                        </td>
                                        <td class="ekurs text-center bg-soft-primary">
                                            <input class="form-control form-control-sm kurs_edit" name="kurs_edit"
                                                type="text" placeholder="Nilai Kurs" step="0.1"
                                                id="{{ $item->id_currency }}"
                                                value="{{ number_format($item->nilai_kurs, 2, ',','.') }}" />
                                        </td>
                                        <td class="eketerangan">
                                            @if($item->keterangan == '')
                                            <input class="form-control form-control-sm keterangan_edit" name="ket_edit"
                                                type="text" placeholder="Keterangan" id="{{ $item->id_currency }}"
                                                value="{{ " -" }}" />
                                            @else
                                            <input class="form-control form-control-sm keterangan_edit" name="ket_edit"
                                                type="text" placeholder="Keterangan" id="{{ $item->id_currency }}"
                                                value="{{ $item->keterangan }}" defaultValue={{ "-" }} />
                                            @endif
                                        </td>
                                        <td class="eurutan">
                                            @if($item->jenis_kurs != 'Coins')
                                            <select class="form-select form-select-sm urutan_edit" name="ket_urutan"
                                                type="number" placeholder="Urutan" id="{{ $item->id_currency }}"
                                                step="1" value="{{ $item->urutan }}">
                                                <option value="{{ $item->urutan }}">{{ $item->urutan ?? 'Pilih Urutan'
                                                    }}</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29">29</option>
                                                <option value="30">30</option>
                                            </select>
                                            @else
                                            -
                                            @endif

                                        </td>
                                        <td class="eflag text-center">
                                            @if($item->img_flag == '' || $item->img_flag == null )
                                                <p>Gambar Kosong</p>
                                            @else
                                                <img src="{{ $item->img_flag }}" alt="flag" height="30" />
                                            @endif
                                        </td>
                                    </tr>

                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-10">
                            <p class="text-word-break fs--1"> <b>Keterangan:</b> <br>
                                - Jika terdapat angka dibelakang koma gunakan separator <b class="text-primary">koma (,)</b>
                                <br>
                                - Pastikan urutan tidak ada angka duplicat atau sama</p>
                        </div>
                        <div class="col-auto mt-4">
                            <a href="{{ route('master-currency') }}" class="btn btn-secondary" type="button">Kembali</a>
                        </div>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('master-currency-store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Tambah Data Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form Currency berikut ini</p>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="nama_currency">Nama Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control @error('nama_currency') is-invalid @enderror"
                                name="nama_currency" type="text" placeholder="Input Nama Currency"
                                value="{{ old('nama_currency') }}" required />
                            @error('nama_currency')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="country">Country</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control @error('country') is-invalid @enderror" name="country"
                                type="text" placeholder="Input Country Currency" value="{{ old('country') }}"
                                required />
                            @error('country')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label" for="jenis_kurs">Jenis Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <select name="jenis_kurs" id="jenis_kurs" class="form-select"
                                    value="{{ old('jenis_kurs') }}"
                                    class="form-control @error('jenis_kurs') is-invalid @enderror">
                                    <option value="Lembar">Lembar</option>
                                    <option value="Coins">Coins</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-1">
                                <label class="form-label" for="nilai_kurs">Nilai Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <input class="form-control @error('nilai_kurs') is-invalid @enderror" name="nilai_kurs"
                                    type="number" placeholder="Nilai Kurs" step='0.01' id="nilai_kurs"
                                    value="{{ old('nilai_kurs') }}" required />
                                @error('nilai_kurs')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>


                        <p class="text-primary fs--1">(IDR):
                            <span id="detailjumlahcurrency" class="detailjumlahcurrency">

                            </span>
                        </p>
                        <p class="text-word-break fs--1"> <b>Ket:</b> Jika terdapat angka dibelakang koma gunakan
                            separator <b class="text-primary">koma (,)</b> </p>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" type="text" placeholder="Input Keterangan"
                                value="{{ old('country') }}"></textarea>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label" for="urutan">Urutan Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <select class="form-select" name="urutan" type="number"
                                    placeholder="Urutan" id="{{ $item->id_currency }}" step="1"
                                    value="{{ old('urutan') }}" required>
                                    <option value="">Urutan</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                </select>
                                {{-- <input class="form-control @error('urutan') is-invalid @enderror" name="urutan"
                                    type="number" placeholder="Urutan" step='1' id="urutan" value="{{ old('urutan') }}"
                                    required /> --}}
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="img_flag">Gambar Bendera</label><span class="mr-4"
                                    style="color: red">*</span>
                                <input class="form-control" id="img_flag" type="file" name="img_flag"
                                    value="{{ old('img_flag') }}" accept="image/*" multiple="multiple" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <span class="mb-4" style="color: red">*</span> <span class="fs--1">Wajib diisi</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Tambah Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteCurrency" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                    class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal"
                    aria-label="Close"></button></div>
            <form action="{{ url('owner/delete-currency') }}" method="POST">
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Currency</h4>
                    </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="currency_delete_id" id="id_currency">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Currency ini?
                                        </p>
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

<div class="modal fade" id="editCurrency" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/owner/update-currency') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Edit Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <input type="hidden" name="edit_currency_id" id="edit_currency_id">
                        <p class="text-word-break fs--1">Lengkapi Form Currency berikut ini</p>
                        <div class="row mb-3">
                            <div class="col-2 bg-soft-info mt-3">
                                <img id="img-flag" src="" alt="flag" height="50" />
                            </div>
                            <div class="col-10">
                                <label class="form-label" for="img_flag">Gambar Bendera</label>
                                <input class="form-control" id="img_flag" type="file" name="img_flag"
                                    value="{{ old('img_flag') }}" accept="image/*" multiple="multiple">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Edit Data </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('.jenis_edit').each(function(){
            $(this).on('change', function(){
                var id = $(this).attr('id')
                var jenis = $(this).val()
                
                var dataReq = {
                    id: id,
                    jenis: jenis,
                    "_token": $('#token_edit').val()
                }

                $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    cache: false,
                    data: dataReq,
                    
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Jenis Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Jenis Kurs',
                        })
                    }
                }); 
            })
        });

        $('.country_edit').each(function(){
            $(this).on('change', function(){
                var id = $(this).attr('id')
                var country = $(this).val()
                
                var dataReq = {
                    id: id,
                    country: country,
                    "_token": $('#token_edit').val()
                }

                $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    cache: false,
                    data: dataReq,
                    
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Country Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Country Kurs',
                        })
                    }
                }); 
            })
        });

        $('.nama_edit').each(function(){
            $(this).on('change', function(){
                var id = $(this).attr('id')
                var nama = $(this).val()
                
                var dataReq = {
                    id: id,
                    nama: nama,
                    "_token": $('#token_edit').val()
                }

                $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    cache: false,
                    data: dataReq,
                    
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Nama Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Nama Kurs',
                        })
                    }
                }); 
            })
        });

        $('.urutan_edit').each(function(){
            $(this).on('change', function(){
                var id = $(this).attr('id')
                var urutan = $(this).val()
                var dataReq = {
                    id: id,
                    urutan: urutan,
                    "_token": $('#token_edit').val()
                }

                
                console.log(dataReq)

                $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    cache: false,
                    data: dataReq,
                    
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Urutan Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Urutan Kurs',
                        })
                    }
                }); 
            })
        });

        $('.keterangan_edit').each(function(){
            $(this).on('change', function(){
                var id = $(this).attr('id')
                var keterangan = $(this).val()
                
                if(keterangan == null || keterangan == ''){
                    var tes = $(this).val("-")
                    var yak = "-";
                        var dataReq = {
                        id: id,
                        keterangan: yak,
                        "_token": $('#token_edit').val()
                    }
                }else{
                    var dataReq = {
                        id: id,
                        keterangan: keterangan,
                        "_token": $('#token_edit').val()
                    }
                }

                
                console.log(dataReq)

                $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    cache: false,
                    data: dataReq,
                    
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Keterangan Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Mengupdate Kurs',
                        })
                    }
                }); 
            })
        });


        $('.kurs_edit').each(function() {
            $(this).on('change', function() {
                var id = $(this).attr('id')
                var kurs = $(this).val()  
                var kurs_template = kurs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                $(this).val(kurs_template)

                var kurs_store =kurs_template.replace('.', '').replace(',','.').trim()
                    $.ajax({
                    method: 'post',
                    url: '/owner/update-nilai-kurs',
                    data: {
                        "id": id,
                        "nilai_kurs": kurs_store,
                        "_token": $('#token_edit').val()
                    },
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Mengupdate Kurs'
                        })
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error! Tidak Berhasil Mengupdate Kurs',
                        })
                    }
                }); 
            })
        });


        $('.deleteCurrencyBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_currency').val(id)
            $('#deleteCurrency').modal('show');
        })

        $('#datatable2').DataTable();

        $('#nilai_kurs').on('input', function() {
            var value = $(this).val()
            console.log(value)
            
            // var hasil_calc = calculate.toFixed(2)
            
            var hasil_calc = new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);

            $('#detailjumlahcurrency').html(hasil_calc)
        });

        // $('.kurs').each(function() {
        //     $(this).on('input', function() {
        //         var harga = $(this).val()
        //         var harga_fix = new Intl.NumberFormat('id', {
        //             style: 'currency',
        //             currency: 'IDR',
        //             minimumFractionDigits: 2,
        //             maximumFractionDigits: 2
        //         }).format(harga)

        //         $('#detaileditkurs').html(harga_fix)
        //         console.log(harga)
        //     })
        // });

        var table = $('#datatable').DataTable();

        table.on('click', '.editCurrencyBtn', function () {
            var id = $(this).val();
            $('#edit_currency_id').val(id)

            $tr = $(this).closest('tr');
            if ($($tr).hasClass('clid')) {
                $tr = $tr.prev('.parent')
            }

            var data = table.row($tr).data();
            var tes = $(data[7]).attr('src');

            if(tes != undefined){
                $('#img-flag').attr("src",tes);
            }else{
                $('#img-flag').attr("src",null);
            }
          
            $('#editForm').attr('action', '/owner/master-currency/' + id)
            $('#editCurrency').modal('show');

        })
    });

</script>



@endsection