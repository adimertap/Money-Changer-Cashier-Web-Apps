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
                            <p>Currency Exchange Website</p>
                        </div>
                        <div class="col-auto h-100">
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#error-modal">Atur Currency</button>
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
                            <h6 class="mb-md-0 mb-lg-2">Keterangan</h6>
                            {{-- <span class="badge rounded-pill badge-soft-success mb-1">Currency Detail</span> --}}
                        </div>
                        <div class="col-auto">
                            <p class="fs--1 fw-normal text-700">Jika terdapat data opsional untuk exchange seperti promo dapat ditambahkan disini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('master-currency') }}">Master Currency</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('currency-detail.index') }}">Currency on Website Exchange</a></li>
      </ul>
    <div class="card mb-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Data Detail Currency</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Currency Website</p>
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
                                <th class="sort text-center" data-sort="kurs">Nilai Kurs Baru</th>
                                <th class="sort text-center" data-sort="keterangan">Keterangan</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($kurs_detail as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td class="nama_currency">{{ $item->Kurs->nama_currency }}</td>
                                <td class="country">{{ $item->Kurs->country }}</td>
                                <td class="jenis text-center">
                                    @if($item->Kurs->jenis_kurs == 'Lembar')
                                        <span class="badge badge-soft-primary">{{ $item->Kurs->jenis_kurs }}</span>
                                    @else
                                        <span class="badge badge-soft-warning">{{ $item->Kurs->jenis_kurs }}</span>
                                    @endif
                                    
                                </td>
                                <td class="kurs text-center">{{ number_format($item->nilai_baru, 2, ',', '.') }}</td>
                                <td class="jenis text-center">{{ $item->keterangan }}</td>
                                <td class="text-center">
                                    <button class="btn p-0 ms-2 editCurrencyBtn" value="{{ $item->id_detail }}"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Edit"><span class="text-700 fas fa-edit"></span>
                                    </button>
                                    <button class="btn p-0 ms-2 deleteCurrencyBtn" value="{{ $item->id_detail }}"
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
            <form action="{{ route('currency-detail.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Tambah Detail Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">
                            Data Currency ini akan tampil sebagai data baru pada website
                        </p>
                        <div class="col-12 mb-1">
                            <label for="currency">Pilih Kurs</label><span class="mr-4 mb-3" style="color: red">*</span>
                            <select class="form-select js-choice currency-select" id="currency" size="1" name="id_currency" data-options='{"removeItemButton":true,"placeholder":true}'>
                                <option value="">Pilih Kurs Terlebih Dahulu</option>
                                @foreach ($kurs as $item)
                                    <option value="{{ $item->id_currency }}">{{ $item->nama_currency }}, {{ $item->jenis_kurs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="nilai_baru">Nilai Baru Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <input class="form-control @error('nilai_baru') is-invalid @enderror"
                                name="nilai_baru" type="number" placeholder="Nilai Kurs" step='0.01' id="nilai_baru"
                                value="{{ old('nilai_baru') }}" required />
                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="keterangan">Keterangan Kurs</label><span class="mr-4 mb-3"
                                style="color: red">*</span>
                            <textarea class="form-control"
                                name="keterangan" type="text" placeholder="Input Keterangan"
                                value="{{ old('keterangan') }}" required></textarea>
                        </div>
                        <p class="text-primary fs--1 m-0">(IDR):
                            <span id="detailjumlahcurrency" class="detailjumlahcurrency">

                            </span>
                        </p>
                        <p class="text-word-break fs--1"> <b>Ket:</b> Jika terdapat angka dibelakang koma gunakan separator <b class="text-primary">koma (,)</b> </p>
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
            <form id="deleteForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body p-0">
                    <div class="bg-danger rounded-top-lg py-3 ps-4 pe-6">
                        <h4 class="mb-1 text-white">Hapus Data Detail Currency</h4>
                       </div>
                    <div class="p-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex-1">
                                        <input type="hidden" name="id_detail_kurs" id="id_detail_kurs">
                                        <h5 class="mb-2 fs-0">Confirmation</h5>
                                        <p class="text-word-break fs--1">Apakah Anda Yakin Menghapus Data Detail Currency ini?
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
            <form id="editForm" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-body p-0">
                    <div class="rounded-top-lg py-3 ps-4 pe-6 bg-light">
                        <h4 class="mb-1">Edit Detail Currency</h4>
                    </div>
                    <div class="p-4 pb-0">
                    <p class="text-word-break fs--1">Lengkapi Form Currency berikut ini</p>
                    <input type="hidden" name="detail_id" id="detail_id">
                    <div class="col-12 mb-1">
                        <label for="currency">Kurs</label><span class="mr-4 mb-3" style="color: red">*</span>
                        <input class="form-control"
                            name="currency" type="text" placeholder="Kur" id="kurs_nama"
                            value="{{ old('nilai_baru') }}" readonly />
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label" for="nilai_baru">Nilai Baru Kurs</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <input class="form-control nilai_baru_edit"
                            name="nilai_baru" type="number" placeholder="Nilai Kurs" step='0.01' id="nilai_baru_edit"
                            value="{{ old('nilai_baru') }}" required />
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label" for="keterangan">Keterangan Kurs</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <textarea class="form-control"
                            name="keterangan" type="text" placeholder="Input Keterangan" id="keterangan"
                            value="{{ old('keterangan') }}" required></textarea>
                    </div>
                    <p class="text-primary fs--1 m-0">(IDR):
                        <span id="detailcurrencyedit" class="detailcurrencyedit">

                        </span>
                    </p>
                    <p class="text-word-break fs--1"> <b>Ket:</b> Jika terdapat angka dibelakang koma gunakan separator <b class="text-primary">koma (,)</b> </p>
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
            $('#id_detail_kurs').val(id)
            $('#deleteForm').attr('action', '/currency-detail/' + id)
            $('#deleteCurrency').modal('show');
            
        })

        $('#nilai_baru').on('input', function() {
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

        $('.nilai_baru_edit').each(function() {
            $(this).on('input', function() {
                var harga = $(this).val()
                var harga_fix = new Intl.NumberFormat('id', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(harga)

                $('#detailcurrencyedit').html(harga_fix)
            })
        });

        var table = $('#datatable').DataTable();

        table.on('click', '.editCurrencyBtn', function () {
            var id = $(this).val();
            $('#detail_id').val(id)
            $tr = $(this).closest('tr');
            if ($($tr).hasClass('clid')) {
                $tr = $tr.prev('.parent')
            }
            var data = table.row($tr).data();
            var nilai_kurs = data[4].replace('.', '').replace(',', '.').trim()
           
            $('#kurs_nama').val(data[1])
            $('#keterangan').val(data[5])
            $('#nilai_baru_edit').val(nilai_kurs)
            var kurs_edit = new Intl.NumberFormat('id', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(nilai_kurs)

            $('#detailcurrencyedit').html(kurs_edit)
          
            $('#editForm').attr('action', '/currency-detail/' + id)
            $('#editCurrency').modal('show');

        })
    });

</script>



@endsection
