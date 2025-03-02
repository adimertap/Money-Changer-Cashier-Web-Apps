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
            <div id="tableExample">
                <div class="table-responsive scrollbar">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead class="bg-200 text-900">
                            <tr>
                                <th class="text-center" data-sort="no">No.</th>
                                <th>Bulan</th>
                                @foreach ($years as $year)
                                    <th class="text-center">{{ $year }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="list">
                            @forelse ($data as $month)
                            <tr>
                                <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                <td>{{ $month['month_name'] }}</td>
                                @foreach ($years as $year)
                                    <td class="text-center">Rp. {{ number_format($month['totals'][$year], 0, ',', '.') }}</td>
                                @endforeach
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
        var table = $('#example').DataTable({
            responsive: true,
            paging: false,
            sort: false
            // scrollY: 400
        });
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
