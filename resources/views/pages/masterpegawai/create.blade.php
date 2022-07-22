@extends('layouts.app')

@section('content')

<main>
    <div class="card mb-3 mt-3">
        <div class="card-header">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h5 class="mb-0" data-anchor="data-anchor">Tambah Data Pegawai</h5>
                    <p class="mb-0 pt-1 mt-2 mb-0">Manajemen Data Kepegawaian</p>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body">
            <form class="needs-validation" novalidate="" action="{{ route('master-pegawai.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama Lengkap</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <input class="form-control @error('name') is-invalid @enderror"" id=" name" name="name"
                            type="text" placeholder="Input Nama Lengkap Pegawai" value="{{ old('name') }}" required />
                        @error('name')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="nama_panggilan">Nama Panggilan</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <input class="form-control @error('nama_panggilan') is-invalid @enderror"" id=" nama_panggilan"
                            name="nama_panggilan" type="text" placeholder="Input Nama Panggilan Pegawai"
                            value="{{ old('nama_panggilan') }}" required />
                        @error('nama_panggilan')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select"
                            value="{{ old('jenis_kelamin') }}"
                            class="form-control @error('jenis_kelamin') is-invalid @enderror">
                            <option value="{{ old('jenis_kelamin')}}">Pilih Jenis Kelamin</option>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone_number">No. Telephone</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <input class="form-control @error('phone_number') is-invalid @enderror"" id=" phone_number"
                            name="phone_number" type="text" placeholder="Input Nomor Telephone"
                            value="{{ old('phone_number') }}" required />
                        @error('phone_number')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4">
                        <label class="form-label" for="role">Role Pegawai</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <select name="role" id="role" class="form-select" value="{{ old('role') }}"
                            class="form-control @error('role') is-invalid @enderror">
                            <option value="{{ old('role')}}">Pilih Role Pegawai</option>
                            <option value="Owner">Owner</option>
                            <option value="Pegawai">Pegawai</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror"" id=" alamat" name="alamat"
                            type="text" placeholder="Input Alamat Lengkap Pegawai"
                            value="{{ old('alamat') }}"></textarea>
                        @error('alamat')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>
                <hr class="mt-4">
                <p class="mb-0 pt-1 mt-2 mb-0">Account Pegawai</p>
                <div class="col-md-12 mb-3 mt-3">
                    <label class="form-label" for="email">Email Pegawai</label><span class="mr-4 mb-3"
                        style="color: red">*</span>
                    <input class="form-control @error('email') is-invalid @enderror"" id=" email" name="email"
                        type="email" placeholder="Input Email Pegawai" value="{{ old('email') }}" required />
                    @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label" for="password">Password Pegawai</label><span class="mr-4 mb-3"
                            style="color: red">*</span>
                        <input class="form-control @error('password') is-invalid @enderror"" id=" password"
                            name="password" type="password" placeholder="Input Password Pegawai"
                            value="{{ old('password') }}" required autocomplete="new-password" />
                        @error('email')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password-confirm">Password Confirmation</label><span
                            class="mr-4 mb-3" style="color: red">*</span>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror"" id="
                            password-confirm" name="password_confirmation" type="password"
                            placeholder="Konfirmasi Password" value="{{ old('password_confirmation') }}" required
                            autocomplete="new-password" />
                        @error('email')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                </div>

                <button class="btn btn-primary" type="submit">Tambah Data</button>
            </form>
        </div>
    </div>
</main>



@endsection
