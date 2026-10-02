<table>
    <thead>
        <tr>
            <th colspan="8">Master Terduga Tahun {{ $header->tahun ?: '-' }}</th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Nama</th>
            <th>Alias</th>
            <th>Tipe</th>
            <th>Kode Densus</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>WN</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($header->terduga as $item)
        <tr>
            <td>{{ $loop->iteration }}.</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->alias ?: '-' }}</td>
            <td>{{ $item->terduga_type ?: '-' }}</td>
            <td>{{ $item->kode_densus ?: '-' }}</td>
            <td>{{ $item->tempat_lahir ?: '-' }}</td>
            <td>{{ $item->tanggal_lahir ?: '-' }}</td>
            <td>{{ $item->wn ?: '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
