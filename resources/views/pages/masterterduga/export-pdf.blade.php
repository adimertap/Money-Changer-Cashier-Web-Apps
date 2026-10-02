<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Terduga</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h3 { text-align: center; margin-bottom: 14px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 5px; vertical-align: top; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Master Terduga Tahun {{ $header->tahun ?: '-' }}</h3>
    <table>
        <thead>
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
</body>
</html>
