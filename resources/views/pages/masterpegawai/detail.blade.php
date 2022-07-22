@extends('layouts.app')

@section('content')

<main>
    <div class="card mb-3 mt-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Detail Data Pegawai </h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Nama: {{ $item->name }}</p>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input class="form-control" type="text" value="{{ $item->name }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Panggilan</label>
                        <input class="form-control" type="text" value="{{ $item->nama_panggilan }}" readonly />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <input class="form-control" type="text" value="{{ $item->jenis_kelamin }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telephone</label>
                        <input class="form-control" type="text" value="{{ $item->phone_number }}" readonly />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <input class="form-control" type="text" value="{{ $item->role }}" readonly />
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" type="text" value="{{ $item->alamat }}" readonly>{{ $item->alamat }}</textarea>
                    </div>
                </div>
                <hr class="mt-4">
                <p class="mb-0 pt-1 mt-2 mb-3">Account Pegawai</p>
                <div class="row mb-5">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="text" value="{{ $item->email }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label><br>
                    <a href="{{ route('password.reset', $item->id) }}" class="btn btn-primary me-1 mb-1 btn-sm" type="button">Ubah Password?</a>
                </div>
                </div>

                <a  href="{{ route('master-pegawai.index') }}" class="btn btn-secondary " type="button">Kembali</a>
        </div>
    </div>
</main>



@endsection
