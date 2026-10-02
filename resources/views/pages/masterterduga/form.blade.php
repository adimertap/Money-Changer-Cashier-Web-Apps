@php($item = $item ?? null)
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input id="terdugaName" class="form-control @error('name') is-invalid @enderror" name="name" required value="{{ old('name', optional($item)->name) }}">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label">Alias</label><input id="terdugaAlias" class="form-control" name="alias" value="{{ old('alias', optional($item)->alias) }}"></div>
    <div class="col-md-6"><label class="form-label">Tipe Terduga</label><input id="terdugaType" class="form-control" name="terduga_type" value="{{ old('terduga_type', optional($item)->terduga_type) }}"></div>
    <div class="col-md-6"><label class="form-label">Kode Densus</label><input id="terdugaKodeDensus" class="form-control" name="kode_densus" value="{{ old('kode_densus', optional($item)->kode_densus) }}"></div>
    <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input id="terdugaTempatLahir" class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir', optional($item)->tempat_lahir) }}"></div>
    <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input id="terdugaTanggalLahir" class="form-control" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($item)->tanggal_lahir) }}"></div>
    <div class="col-md-6">
        <label class="form-label">Warga Negara</label>
        <select class="form-select" name="wn" id="terdugaCountry" data-country-select>
            <option value="">Pilih Country</option>
            @foreach ($countries as $code => $countryName)
            <option value="{{ $countryName }}" {{ old('wn', optional($item)->wn) === $countryName ? 'selected' : '' }}>{{ $countryName }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea id="terdugaDescription" class="form-control" name="description">{{ old('description', optional($item)->description) }}</textarea></div>
    <div class="col-12"><label class="form-label">Alamat</label><textarea id="terdugaAlamat" class="form-control" name="alamat">{{ old('alamat', optional($item)->alamat) }}</textarea></div>
    <div class="col-md-4"><label class="form-label">Status</label><select id="terdugaIsClear" class="form-select" name="is_clear"><option value="1" {{ old('is_clear', optional($item)->is_clear) == 1 ? 'selected' : '' }}>Clear</option><option value="0" {{ old('is_clear', optional($item)->is_clear) == 0 ? 'selected' : '' }}>Belum Clear</option></select></div>
</div><hr>
