@extends('layouts.app')

@section('content')

<main>
    <div class="card mb-3 mt-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Edit Data Pegawai</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Data {{ $item->name }}</p>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body">
            <form class="needs-validation" novalidate="" action="{{ route('master-pegawai.update', $item->id) }}"
                method="POST">
                @method('PUT')
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input class="form-control" id=" name" name="name" type="text"
                            placeholder="Input Nama Lengkap Pegawai" value="{{ $item->name }}" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nama_panggilan">Nama Panggilan</label>
                        <input class="form-control" id=" nama_panggilan" name="nama_panggilan" type="text"
                            placeholder="Input Nama Panggilan Pegawai" value="{{ $item->nama_panggilan }}" required />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select"
                            value="{{ old('jenis_kelamin') }}" class="form-control">
                            <option value="{{ $item->jenis_kelamin }}">{{ $item->jenis_kelamin }}</option>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone_number">No. Telephone</label>
                        <input class="form-control" id=" phone_number" name="phone_number" type="text"
                            placeholder="Input Nomor Telephone" value="{{ $item->phone_number }}" required />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label class="form-label" for="role">Role Pegawai</label>
                        <select name="role" id="role" class="form-select" value="{{ old('role') }}"
                            class="form-control">
                            <option value="{{ $item->role }}">{{ $item->role }}</option>
                            <option value="Owner">Owner</option>
                            <option value="Pegawai">Pegawai</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control " id=" alamat" name="alamat" type="text"
                            placeholder="Input Alamat Lengkap Pegawai"
                            value="{{ $item->alamat }}">{{ $item->alamat }}</textarea>
                    </div>
                </div>
                <hr class="mt-4">
                <p class="mb-0 pt-1 mt-2 mb-0">Account Pegawai</p>
                <div class="row mb-5 mt-3">
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email Pegawai</label>
                        <input class="form-control" id=" email" name="email" type="email"
                            placeholder="Input Email Pegawai" value="{{ $item->email }}" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label><br>
                        <a href="{{ route('password.reset', $item->id) }}" class="btn btn-primary me-1 mb-1 btn-sm"
                            type="button">Ubah Password?</a>
                    </div>
                </div>
                
                <a href="{{ route('master-pegawai.index') }}" class="btn btn-secondary" type="button">Kembali</a>
                <button class="btn btn-primary" type="submit">Edit Data</button>
              
            </form>
        </div>
    </div>
</main>



@endsection
