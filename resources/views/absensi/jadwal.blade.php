@extends('layouts.app')

@section('content')

<style>
    #calendar {
        height: 100%;
    }

    .fc-event-title {
        font-size: 14px !important;
        /* Custom font size for event titles */
        font-weight: bold;
        /* Bold text */
    }
</style>
<main>
    <div class="d-flex justify-content-between mt-3">
        <div class="mb-4">
            <h4 class="mb-0">Selamat Datang, {{ Auth::user()->name }}</h4>
            <i class="text-muted">Atur Jadwal Pegawai Anda pada halaman ini</i>
        </div>
        <button class="btn btn-sm btn-primary h-100 mt-3" onclick="aturJadwal()">Atur Jadwal</button>
    </div>
   


    <div class="card overflow-hidden p-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#tab-home" role="tab" aria-controls="tab-home" aria-selected="true">Jadwal Kerja</a></li>
            <li class="nav-item"><a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#tab-profile" role="tab" aria-controls="tab-profile" aria-selected="false">Request Tukar Jadwal</a></li>
            {{-- <li class="nav-item"><a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#tab-contact" role="tab" aria-controls="tab-contact" aria-selected="false">Contact</a></li> --}}
          </ul>
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-home" role="tabpanel" aria-labelledby="home-tab">  <div class="card-body p-0 pt-3" style="height: 1000px">
                <div id="calendar"></div>
            </div></div>
            <div class="tab-pane fade" id="tab-profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card-body">
                    <i class="mb-3">Data Pegawai yang ingin tukar jadwal</i>
                    <div class="mt-3" id="tableTukar" data-list='{"valueNames":["no","nama_currency","country"],"page":20,"pagination":true}'>
                        <div class="table-responsive scrollbar">
                            <table class="table table-bordered table-striped fs--1 mb-0" id="datatable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th class="sort text-center" data-sort="no">No.</th>
                                        <th class="sort text-center" data-sort="nama">Tanggal</th>
                                        <th class="sort text-center" data-sort="in">Shift</th>
                                        <th class="sort text-center" data-sort="out">User</th>
                                        <th class="sort text-center" data-sort="out">Keterangan</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach ($tukar as $item)
                                    <tr role="row" class="odd">
                                        <th scope="row" class="no">{{ $loop->iteration }}.</th>
                                        <td class="text-center">{{ Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                                        <td class="text-center">{{ $item->Shift->shift_name }}</td>
                                        <td class="text-center">{{ $item->User->name }}</td>
                                        <td >{{ $item->keterangan }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary"  style="font-size: 12px!important" onclick="updateTukar('{{ $item->tanggal }}','{{ $item->jadwal_id }}','{{ $item->User->id }}','{{ $item->shift_id }}')" type="button">Update</button>
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
      
    </div>
</main>

<div class="modal fade" id="modal-tambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('jadwal.store') }}" method="POST" id="jadwalForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="jadwalId">
                <div class="modal-body p-0">
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs--1">Lengkapi Form berikut ini</p>
                        <div class="row mt-4 mb-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="start_date">Start Date</label><span class="mr-4 mb-3" style="color: red">*</span>
                                <input class="form-control @error('start_date') is-invalid @enderror" name="start_date" type="date" placeholder="Input Start Date" value="{{ old('start_date') }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="end_date">End Date</label>
                                <input class="form-control @error('end_date') is-invalid @enderror" name="end_date" type="date" placeholder="Input End Date" value="{{ old('end_date') }}" />
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="pegawai">Pegawai</label><span class="mr-4 mb-3" style="color: red">*</span>
                            <select type="text" id="pegawai" name="pegawai" class="form-select" value="{{ old('pegawai') }}">
                                <option value="">Pilih Pegawai</option>
                                @foreach ($user as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="shift_name">Shift Kerja</label>
                            <span class="mr-4 mb-3" style="color: red">*</span>
                            @foreach ($shift as $item)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shift" value="{{ $item->shift_id }}" id="shift_{{ $item->shift_id }}">
                                <label class="form-check-label" for="shift_{{ $item->shift_id }}">
                                    {{ $item->shift_name }}, In: {{ $item->shift_in }}, Out: {{ $item->shift_out }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        {{-- <div class="row mt-1 mb-2">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="keterangan">Keterangan</label>
                                <textarea class="form-control" name="keterangan" type="text" placeholder="Input keterangan" rows="2" value="{{ old('keterangan') }}"></textarea>
                            </div>
                        </div> --}}
                        <div class="mb-4">
                            <span class="mb-4" style="color: red">*</span> <span class="fs--1">Wajib diisi</span>
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

<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-index-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('jadwal.update', 1) }}" method="POST" id="jadwalEditForm" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" id="jadwalIdEdit" name="jadwalIdEdit">
                <div class="modal-body p-0">
                    <div class="p-4 pb-0">
                        <p class="text-word-break fs-1">Edit Jadwal</p>
                        <div class="row mt-4 mb-2">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="start_date">Date</label>
                                <input class="form-control" name="start_date" id="startDateEdit" type="date" placeholder="Input Start Date" value="{{ old('start_date') }}" readonly/>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="pegawai">Pegawai</label><span class="mr-4 mb-3" style="color: red">*</span>
                            <select type="text" id="pegawaiEdit" name="pegawai" class="form-select" value="{{ old('pegawai') }}">
                                <option value="">Pilih Pegawai</option>
                                @foreach ($user as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="shift_name">Shift Kerja</label>
                            <span class="mr-4 mb-3" style="color: red">*</span>
                            @foreach ($shift as $item)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shiftEdit" value="{{ $item->shift_id }}" id="edit_shift_{{ $item->shift_id }}">
                                <label class="form-check-label" for="shift_{{ $item->shift_id }}">
                                    {{ $item->shift_name }}, In: {{ $item->shift_in }}, Out: {{ $item->shift_out }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        {{-- <div class="row mt-1 mb-2">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="keteranganEdit">Keterangan</label>
                                <textarea class="form-control" id="keteranganEdit" name="keterangan" type="text" placeholder="Input keterangan" rows="2" value="{{ old('keterangan') }}" ></textarea>
                            </div>
                        </div> --}}
                        <div class="mb-4">
                            <span class="mb-4" style="color: red">*</span> <span class="fs--1">Wajib diisi</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn btn-danger" id="btnDelete" onclick="deleteJadwal()" type="button">Delete</button>
                    <div>
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" id="btnEdit" type="submit">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function aturJadwal() {
        $('#modal-tambah').modal('show');
    }

    function updateTukar(tanggal, jadwalId, user, shift) {
        var datePart = tanggal.split(' ')[0];
        $('#jadwalIdEdit').val(jadwalId);
        $('#startDateEdit').prop('readonly',false);
        $('#startDateEdit').val(datePart);
        $('#pegawaiEdit').val(user).trigger('change');
        $('input[name="shiftEdit"][value="' + shift + '"]').prop('checked', true);
        $('#modal-edit').modal('show');
    }

    function deleteJadwal() {
        Swal.fire({
            title: 'Are you sure?'
            , text: "You won't be able to revert this!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#3085d6'
            , cancelButtonColor: '#d33'
            , confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.preventDefault()
                let id = $('#jadwalIdEdit').val();
                if (id === "" || id === 0) {
                    console.error('Error id null');
                    return;
                }

                $.ajax({
                    url: '/jadwal/' + id
                    , method: 'DELETE'
                    , data: {
                        _token: '{{ csrf_token() }}'
                    , }
                    , success: function(response) {
                        Swal.fire(
                            'Deleted!'
                            , 'The schedule has been deleted.'
                            , 'success'
                        ).then(() => {
                            $('#modal-edit').modal('hide');
                            window.location.reload();
                        });
                    }
                    , error: function(xhr, status, error) {
                        console.error('Failed to delete event:', status, error);
                        Swal.fire(
                            'Failed!'
                            , 'There was an error deleting the schedule.'
                            , 'error'
                        );
                    }
                });
            }
        })
    }

    $(document).ready(function() {
        const tes = $('#datatable').DataTable();

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5'
            , initialView: 'dayGridMonth'
            , height: 900
            , displayEventTime: false
            , dateClick: function(info) {
                $('#modal-tambah').modal('show');
                $('input[name="start_date"]').val(info.dateStr);
            }
            , eventClick: function(info) {
                var eventId = info.event.id; // assuming you have an ID for each event
                $.ajax({
                    url: '/api/get-event/' + eventId
                    , method: 'GET'
                    , success: function(data) {
                        console.log(data)
                        var formattedStartDate = new Date(data.start_date).toISOString().split('T')[0];
                        // Populate modal with fetched data
                        $('#jadwalIdEdit').val(data.id)
                        $('#startDateEdit').val(formattedStartDate);
                        $('#pegawaiEdit').val(data.pegawai).trigger('change');
                        // $('#keteranganEdit').html(data.keterangan)
                        $('input[name="shiftEdit"][value="' + data.shift + '"]').prop('checked', true);
                        $('#modal-edit').modal('show');
                    }
                    , error: function(xhr, status, error) {
                        console.error('Failed to fetch event details:', status, error);
                    }
                });
            }
            , events: function(fetchInfo, successCallback, failureCallback) {
                try {
                    $.ajax({
                        url: '/api/jadwal-kerja'
                        , method: 'GET'
                        , success: function(data) {
                            var events = [];
                            data.forEach(function(jadwal) {
                                var eventColor;
                                events.push({
                                    id: jadwal.jadwal_id, // Assuming you have an ID for each event
                                    title: `${jadwal.user.name} - ${jadwal.shift.shift_name}`
                                    , start: jadwal.tanggal
                                    , end: jadwal.tanggal_akhir, // Assuming your date fields are single day events
                                    extendedProps: {
                                        shift_in: jadwal.shift.shift_in
                                        , shift_out: jadwal.shift.shift_out
                                    }
                                });
                            });
                            successCallback(events);
                        }
                        , error: function(xhr, status, error) {
                            console.error('Failed to fetch jadwal kerja:', status, error);
                            failureCallback(error);
                        }
                    });
                } catch (error) {
                    console.error('Error during AJAX request:', error);
                    failureCallback(error);
                }
            }
        });
        calendar.render();
    });

</script>

@endsection
