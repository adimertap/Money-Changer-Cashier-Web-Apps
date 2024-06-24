@extends('layouts.app')

@section('content')
<main>
    <div class="card mb-3">
        <div class="bg-holder d-none d-lg-block bg-card"
            style="background-image:url(/../falcon/assets/img/icons/spot-illustrations/corner-4.png);"></div>
        <div class="card-body position-relative">
            <div class="row">
                <div class="col-lg-6">
                    <h5>Jurnal Bulanan</h5>
                    <p class="mt-2">Data Transaksi dikelompokan berdasarkan bulan</p>
                    <hr>
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
                                <th class="sort text-center fs--1" data-sort="year">Tahun Jurnal</th>
                                <th class="sort text-center fs--1" data-sort="month">Bulan Jurnal</th>
                                <th class="sort text-center fs--1" data-sort="jumlah_transaksi">Transaksi Customer</th>
                                <th class="sort text-center fs--1" data-sort="jumlah_transaksi">Transaksi Jual</th>
                                <th class="sort text-center fs--1" data-sort="grand_total">Total Customer</th>
                                <th class="sort text-center fs--1" data-sort="grand_total">Total Jual</th>

                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($transaksi as $item)
                            <tr role="row" class="odd">
                                <th scope="row" class="no fs--1">{{ $loop->iteration}}.</th>
                                <td class="text-center year fs--1">{{ $item->year }}</td>
                                <td class="text-center month fs--1">{{ date("F", mktime(0, 0, 0, $item->month, 10)) }}</td>
                                <td class="text-center jumlah_transaksi fs--1">{{ $item->jumlah_transaksi }}</td>
                                <td class="text-center jumlah_transaksi fs--1">{{ $item->jual_transaksi }}</td>

                                <td class="grand_total fs--1">Rp. {{ number_format($item->grand_total, 0, ',', '.') }}</td>
                                <td class="grand_total fs--1">Rp. {{ number_format($item->jual_total, 0, ',', '.') }}</td>

                                <td class="text-center fs--1">
                                    <a href="{{ route('jurnal-bulanan.show', $item->month) }}" class="btn p-0 ms-2"
                                        type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Detail"><span class="text-700 fas fa-eye"></span>
                                    </a>
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


<script>
    $(document).ready(function () {
        var table = $('#example').DataTable();
    })

    function filter_tanggal(event) {
        event.preventDefault()
        var form1 = $('#form1')
        var tanggal_mulai = form1.find('input[name="from_date"]').val()
        var tanggal_selesai = form1.find('input[name="to_date"]').val()
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
            icon: 'info',
            title: 'Mohon Tunggu, Sedang diproses ...'
        })
        window.location.href = '/owner/jurnal-harian?from=' + tanggal_mulai + '&to=' + tanggal_selesai
    }

</script>



@endsection
