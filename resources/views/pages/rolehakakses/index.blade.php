@extends('layouts.app')

@section('content')
<main>
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-1">Role &amp; Hak Akses</h5>
            <p class="mb-0 fs--1">Atur menu sidebar untuk setiap role non-Owner.</p>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="alert alert-info">Owner: Semua Menu (selalu memiliki akses)</div>

            <form method="POST" action="{{ route('role-hak-akses.store') }}" class="card mb-4">
                @csrf
                <div class="card-body">
                    <label class="form-label" for="role-name">Tambah Role</label>
                    <div class="input-group">
                        <input class="form-control @error('name') is-invalid @enderror" id="role-name" name="name"
                            type="text" maxlength="100" placeholder="Contoh: Supervisor" value="{{ old('name') }}" required>
                        <button class="btn btn-success" type="submit">Tambah Role</button>
                    </div>
                    @error('name')
                        <div class="text-danger fs--1 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </form>

            @foreach ($editableRoles as $role)
                @php($selected = collect($roleMenus->get($role->name, [])))
                <form method="POST" action="{{ route('role-hak-akses.update', $role->name) }}" class="card mb-4">
                    @csrf
                    @method('PUT')
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $role->name }}</h6>
                        <button class="btn btn-sm btn-primary" type="submit">Simpan {{ $role->name }}</button>
                    </div>
                    <div class="card-body">
                        @foreach ($menus as $group => $groupMenus)
                            <div class="mb-3">
                                <h6 class="text-700">{{ $group ?: 'Lainnya' }}</h6>
                                <div class="row g-2">
                                    @foreach ($groupMenus as $menu)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="menu-{{ \Illuminate\Support\Str::slug($role->name) }}-{{ \Illuminate\Support\Str::slug($menu->menu_key) }}"
                                                    name="menu_keys[]" value="{{ $menu->menu_key }}"
                                                    {{ $selected->contains($menu->menu_key) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="menu-{{ \Illuminate\Support\Str::slug($role->name) }}-{{ \Illuminate\Support\Str::slug($menu->menu_key) }}">
                                                    {{ $menu->menu_label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            @endforeach
        </div>
    </div>
</main>
@endsection
