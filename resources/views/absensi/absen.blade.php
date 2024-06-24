@php
    $layout = Auth::user()->role != 'Owner' ? 'layouts.appAbsen' : 'layouts.app';
@endphp
@extends($layout)


@section('content')
@if($errors->any())
<script>
    $('#modal-tambah').modal('show');
</script>
@endif
<main>
    <div class="mt-4">
        <h1 class="mb-0 text-primary">{{ Carbon\Carbon::parse($today)->format('d F Y') }}</h1>
        <h1 id="realTimeClock">Waktu: </h1>
        @if($jadwalToday->isNotEmpty())
        <hr>
        @if($countTodayStatusX == 0)

        <div class="text-center">
            <b class="text-primary">Absen Hari Ini Complete! ✅ </b>
        </div>
        @else
        <div class="text-center">
            @if($jadwalMasukFilled)
                <a href="{{ route('dashboard') }}" class="btn btn-info col-12 mb-4">Masuk Aplikasi Kasir</a>
            @endif

            <p class="text-danger" id="textJangkauan">Anda Berada diluar jangkauan radius kantor!</p>
            <form action="{{ route('jadwal-user.store') }}" method="POST" id="jadwalForm" enctype="multipart/form-data">
                @csrf
                <button id="absenButton" type="submit" class="btn btn-danger col-12" style="height: 50px!important"
                    disabled>Anda diluar radius kantor</button>
            </form>

        </div>
        @endif
        <hr>
        <div class="card mb-3 mt-5">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0" data-anchor="data-anchor">Jadwal Hari Ini {{ $jadwalTodayCount }} Shift</h5>
                </div>
            </div>
            <div class="card-body">
                <div id="tableExample"
                    data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs--1 mb-0" id="datatableToday">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th class="sort text-center" data-sort="no">No.</th>
                                    <th class="sort text-center" data-sort="nama">Tanggal</th>
                                    <th class="sort text-center" data-sort="in">Shift</th>
                                    <th class="sort text-center" data-sort="out">In</th>
                                    <th class="sort text-center" data-sort="out">Out</th>
                                    <th class="sort text-center" data-sort="out">Status In</th>
                                    <th class="sort text-center" data-sort="out">Status Out</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($jadwalToday as $item)
                                <tr role="row" class="odd">
                                    <th scope="row" class="no">{{ $loop->iteration}}.</th>
                                    <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}
                                    </td>
                                    <td class="in">{{ $item->Shift->shift_name }} , Jam {{ $item->Shift->shift_in }} -
                                        {{ $item->Shift->shift_out }}</td>
                                    <td class="text-center">
                                        @if($item->jam_masuk == "" || !$item->jam_masuk)
                                        -
                                        @else
                                        {{ $item->jam_masuk }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->jam_keluar == "" || !$item->jam_keluar)
                                        -
                                        @else
                                        {{ $item->jam_keluar }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_in == 'Terlambat')
                                        <span class="badge rounded-pill badge-soft-danger">Terlambat</span>
                                        @elseif ($item->status_absen_in == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-warning">Belum Absen</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_out == 'Pulang Cepat')
                                        <span class="badge rounded-pill badge-soft-danger">Pulang Cepat</span>
                                        @elseif ($item->status_absen_out == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-warning">Belum Absen</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        @else
        <div class="text-center">
            <h4 class=" mb-0">Anda tidak memiliki jadwal untuk hari ini</h4>
            <i class="text-muted">Cek kembali Jadwal bersama Owner, Pastikan tidak ada yang terlewat</i>
        </div>
        <hr>
        @endif
        <div class="mt-5">
            <div>
                <h4 class="mb-0">Jadwal Kerja Bulan Ini</h4>
                <i>Cek Jadwal Kerja Anda pada Bulan {{ $currentMonth }}</i>
            </div>
        </div>
        <div class="card mb-3 mt-3">
            <div class="card-body">
                <div id="tableExample"
                    data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                            <thead class="bg-200 text-900">
                                <tr>
                                    <th class="sort text-center" data-sort="no">No.</th>
                                    <th class="sort text-center" data-sort="nama">Tanggal</th>
                                    <th class="sort text-center" data-sort="in">Shift</th>
                                    <th class="sort text-center" data-sort="out">In</th>
                                    <th class="sort text-center" data-sort="out">Out</th>
                                    <th class="sort text-center" data-sort="out">Status In</th>
                                    <th class="sort text-center" data-sort="out">Status Out</th>
                                    {{-- <th class="sort text-center" data-sort="out">Status</th> --}}
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @php
                                $timezone = new \DateTimeZone('Asia/Makassar');
                                $today = Carbon\Carbon::today($timezone);
                                $yesterday = $today->copy()->subDay();
                                $now = Carbon\Carbon::now($timezone);
                                @endphp
                                @foreach ($jadwal as $item)
                                <tr role="row" class="odd">
                                    <th scope="row" class="no">{{ $loop->iteration }}.</th>
                                    <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}
                                    </td>
                                    <td class="in">{{ $item->Shift->shift_name }} , Jam {{ $item->Shift->shift_in }} -
                                        {{ $item->Shift->shift_out }}</td>
                                    <td class="text-center">
                                        @if(empty($item->jam_masuk))
                                        -
                                        @else
                                        {{ $item->jam_masuk }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(empty($item->jam_keluar))
                                        -
                                        @else
                                        {{ $item->jam_keluar }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_in == 'Terlambat')
                                        <span class="badge rounded-pill badge-soft-danger">Terlambat</span>
                                        @elseif($item->status_absen_in == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_absen_out == 'Pulang Cepat')
                                        <span class="badge rounded-pill badge-soft-danger">Pulang Cepat</span>
                                        @elseif($item->status_absen_out == 'Absen')
                                        <span class="badge rounded-pill badge-soft-primary">Absen</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    {{-- <td class="text-center">
                                        @if($item->status == 'X')
                                        <span class="badge rounded-pill badge-soft-warning">Incomplete</span>
                                        @else
                                        <span class="badge rounded-pill badge-soft-success">Complete</span>
                                        @endif
                                    </td> --}}
                                    <td class="text-center">
                                        @if(Carbon\Carbon::parse($item->tanggal)->isSameDay($yesterday) ||
                                        Carbon\Carbon::parse($item->tanggal)->isSameDay($today) ||
                                        Carbon\Carbon::parse($item->tanggal) < $now) @if($item->status == 'X')
                                            <span class="badge rounded-pill badge-soft-warning">Incomplete</span>
                                            @else
                                            <span class="badge rounded-pill badge-soft-success">Complete</span>
                                            @endif
                                            @else
                                            <button class="btn btn-sm btn-secondary ms-2"
                                                onclick="modalTukar('{{ $item->tanggal }}', '{{ $item->jadwal_id }}')"
                                                style="font-size: 12px!important" value="{{ $item->jadwal_id }}"
                                                type="button" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Tindakan Tukar Jadwal">Tukar Jadwal
                                            </button>
                                            @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modal-tukar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('jadwal-user.update', 1) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="jadwalId" id="jadwalId">
                <div class="modal-body p-0">
                    <div class="p-4 pb-3">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class=" mb-3">
                            <label class="form-label" for="date">Tanggal</label>
                            <input class="form-control" name="date" id="dateNow" type="text" readonly></input>
                        </div>
                        <div class=" mb-3">
                            <label class="form-label" for="keterangan">Keterangan Tukar</label>
                            <textarea class="form-control" name="keterangan" type="text" placeholder="Input keterangan"
                                rows="2" value="{{ old('keterangan') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="btnModal" type="submit">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function modalTukar(tanggal, id){
        $('#jadwalId').val(id)
        $('#dateNow').val(tanggal)
        $('#modal-tukar').modal('show');
    }

    function updateClock() {
        // Set timezone to Asia/Makassar
        const now = new Date().toLocaleString('en-US', { timeZone: 'Asia/Makassar' });
        const nowDate = new Date(now);
        const hours = String(nowDate.getHours()).padStart(2, '0');
        const minutes = String(nowDate.getMinutes()).padStart(2, '0');
        const seconds = String(nowDate.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        document.getElementById('realTimeClock').innerText = `Waktu: ${timeString}`;
    }

    // Update the clock every second
    setInterval(updateClock, 1000);

    // Initialize the clock when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateClock();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }

        function showPosition(position) {
            const userLat = position.coords.latitude;
            const userLong = position.coords.longitude;
            const fixedLat = {{ $fixedLatitude }};
            const fixedLong = {{ $fixedLongitude }};
            const distance = calculateDistance(userLat, userLong, fixedLat, fixedLong);

            if (distance < 20) {
                var absenButton = document.getElementById('absenButton');
                var jangkauanText = document.getElementById('textJangkauan');
                jangkauanText.innerText = 'Anda Telah Berada di Radius Kantor, Silahkan Absen!'
                jangkauanText.classList.remove('text-danger');
                jangkauanText.classList.add('text-primary');

                absenButton.disabled = false;
                absenButton.innerText = "Absen Sekarang, Klik Disini";
                absenButton.classList.remove('btn-danger');
                absenButton.classList.add('btn-primary');
            }else{
                const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
                });
                Toast.fire({
                icon: "warning",
                title: "Anda Berada Diluar Jangkauan Absensi Kantor"
                });

                absenButton.disabled = true;
            }
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the earth in km
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a = 
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c; // Distance in km
            return distance;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }
    });

    $(document).ready(function() {
        var table = $('#datatable').DataTable();
        var tableToday = $('#datatableToday').DataTable();
    });
</script>
@endsection