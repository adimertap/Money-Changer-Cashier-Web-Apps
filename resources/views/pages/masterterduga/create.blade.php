@extends('layouts.app')
@section('content')
<main><div class="card mb-3 mt-3"><div class="card-header"><h5 class="mb-0">Tambah Data Terduga</h5><p class="mb-0 fs--1">Header Tahun: {{ $header->tahun ?: '-' }}</p></div><div class="card-body">
<form method="POST" action="{{ route('master-terduga.detail.store', $header->terduga_header_id) }}">@csrf @include('pages.masterterduga.form')
<button class="btn btn-primary" type="submit">Simpan</button> <a class="btn btn-secondary" href="{{ route('master-terduga.show', $header->terduga_header_id) }}">Batal</a>
</form></div></div></main>
@endsection
