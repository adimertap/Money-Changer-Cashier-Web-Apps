@extends('layouts.app')

@section('content')
<main>
    <div class="row g-3 mb-3 mt-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center g-0">
                        <div class="col-6 d-lg-block flex-between-center">
                            <h5 class="text-primary mb-1">Welcome, {{ Auth::user()->nama_panggilan }}!</h5>
                            <p>Tambah Data Currency Disini</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#error-modal">Tambah Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row flex-between-center">
                        <div class="col d-md-flex d-lg-block flex-between-center">
                            <h6 class="mb-md-0 mb-lg-2">Total Currency</h6><span
                                class="badge rounded-pill badge-soft-success">Currency</span>
                        </div>
                        <div class="col-auto">
                            <h4 class="fs-3 fw-normal text-700"><span>{{ $lembar }}</span> Lembar, <span>{{ $coins }} Coins</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link active" href="{{ route('master-currency') }}">Master Currency</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('currency-detail.index') }}">Currency on Website Exchange</a></li>
      </ul>
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
                                <th class="sort text-center" data-sort="flag">Flag</th>
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
                                <td class="kurs text-center">{{ number_format($item->nilai_kurs, 2, ',', '.') }}</td>
                                <td class="flag text-center">
                                    @if($item->img_flag == '' || $item->img_flag == null )
                                        <p>Gambar Kosong</p>
                                    @else
                                        <img src="{{ $item->img_flag }}" alt="flag" height="30" />
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn p-0 ms-2 editCurrencyBtn" value="{{ $item->id_currency }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit"><span class="text-700 fas fa-edit"></span>
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


<div class="modal fade" id="error-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <input class="form-control @error('country') is-invalid @enderror"
                                name="country" type="text" placeholder="Input Country Currency"
                                value="{{ old('country') }}" required />
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
                                <select name="jenis_kurs" id="jenis_kurs" class="form-select" value="{{ old('jenis_kurs') }}"
                                    class="form-control @error('jenis_kurs') is-invalid @enderror">
                                    <option value="Lembar">Lembar</option>
                                    <option value="Coins">Coins</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-1">
                                <label class="form-label" for="nilai_kurs">Nilai Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <input class="form-control @error('nilai_kurs') is-invalid @enderror"
                                    name="nilai_kurs" type="number" placeholder="Nilai Kurs" step='0.01' id="nilai_kurs"
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
                        <p class="text-word-break fs--1"> <b>Ket:</b> Jika terdapat angka dibelakang koma gunakan separator <b class="text-primary">koma (,)</b> </p>
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="img_flag">Gambar Bendera</label><span class="mr-4"
                                style="color: red">*</span>
                            <input class="form-control" id="img_flag" type="file" name="img_flag"
                                value="{{ old('img_flag') }}" accept="image/*" multiple="multiple" required>
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
                    <p class="text-word-break fs--1">Lengkapi Form Currency berikut ini</p>
                        <input type="hidden" name="edit_currency_id" id="edit_currency_id">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="nama_currency">Nama Currency</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control nama_currency" id="fnama"
                                name="nama_currency" type="text" placeholder="Input Nama Currency"
                                value="{{ old('nama_currency') }}" required />
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="country">Country</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control country" id="fcountry"
                                name="country" type="text" placeholder="Input Country Currency"
                                value="{{ old('country') }}" required />
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label" for="jenis_kurs">Jenis Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <select name="jenis_kurs" id="jenis_kurs" class="form-select" value="{{ old('jenis_kurs') }}"
                                    class="form-control @error('jenis_kurs') is-invalid @enderror">
                                    <option id="jkurs" value=""></option>
                                    <option value="Lembar">Lembar</option>
                                    <option value="Coins">Coins</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-1">
                                <label class="form-label" for="nilai_kurs">Nilai Kurs</label><span class="mr-4 mb-3"
                                    style="color: red">*</span>
                                <input class="form-control kurs" id="fkurs"
                                    name="nilai_kurs" type="number" placeholder="Nilai Kurs" step='0.01'
                                    value="{{ old('nilai_kurs') }}" required />
                            </div>
                        </div>
                       
                        <p class="text-primary fs--1">(IDR):
                            <span id="detaileditkurs" class="detaileditkurs">
                                
                            </span>
                        </p>
                        <p class="text-word-break fs--1"> <b>Ket:</b> Jika terdapat angka dibelakang koma gunakan separator <b class="text-primary">koma (,)</b> </p>
                        <div class="row mb-3">
                            <div class="col-2 bg-soft-info mt-3">
                                <img id="img-flag" src="" alt="flag" height="50" />
                            </div>
                            <div class="col-10">
                                <label class="form-label" for="img_flag">Gambar Bendera</label><span class="mr-4"
                                style="color: red">*</span>
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
        $('.deleteCurrencyBtn').click(function (e) {
            e.preventDefault();

            var id = $(this).val();
            $('#id_currency').val(id)
            $('#deleteCurrency').modal('show');
        })

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

        $('.kurs').each(function() {
            $(this).on('input', function() {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(harga)

                $('#detaileditkurs').html(harga_fix)
                console.log(harga)
            })
        });

        var table = $('#datatable').DataTable();

        table.on('click', '.editCurrencyBtn', function () {
            var id = $(this).val();
            $('#edit_currency_id').val(id)

            $tr = $(this).closest('tr');
            if ($($tr).hasClass('clid')) {
                $tr = $tr.prev('.parent')
            }

            var data = table.row($tr).data();
            var nilai_kurs = data[4].replace('.', '').replace(',', '.').trim()
            var jenis = $(data[3]).html()
            var tes = $(data[5]).attr('src');

            if(tes != undefined){
                $('#img-flag').attr("src",tes);
            }else{
                $('#img-flag').attr("src",null);
            }
           

            $('#fnama').val(data[1])
            $('#fcountry').val(data[2])
            $('#jkurs').val(jenis)
            $('#jkurs').text(jenis)
            $('#fkurs').val(nilai_kurs)
            var kurs_edit = new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(nilai_kurs)

            $('#detaileditkurs').html(kurs_edit)
          
            $('#editForm').attr('action', '/owner/master-currency/' + id)
            $('#editCurrency').modal('show');

        })
    });

</script>



@endsection
