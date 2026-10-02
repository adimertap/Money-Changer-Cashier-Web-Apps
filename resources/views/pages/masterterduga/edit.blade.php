@extends('layouts.app')
@section('content')
<main><div class="card mb-3 mt-3"><div class="card-header"><h5 class="mb-0">Edit Data Terduga</h5><p class="mb-0 fs--1">Header Tahun: {{ $header->tahun ?: '-' }}</p></div><div class="card-body">
<form method="POST" action="{{ route('master-terduga.detail.update', [$header->terduga_header_id, $terduga->terduga_id]) }}">@csrf @method('PUT') @include('pages.masterterduga.form', ['item' => $terduga])
<button class="btn btn-primary" type="submit">Simpan Perubahan</button> <a class="btn btn-secondary" href="{{ route('master-terduga.show', $header->terduga_header_id) }}">Batal</a>
</form></div></div></main>
@endsection
