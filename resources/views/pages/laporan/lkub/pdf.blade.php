<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LKUB</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; }
        h1, h2, h3, p { margin: 0; text-align: center; }
        h1 { font-size: 18px; color: #8d2c2c; }
        h2 { font-size: 11px; margin-top: 4px; }
        h3 { font-size: 10px; margin: 4px 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #333; padding: 4px 3px; text-align: right; }
        th { background: #343a40; color: #fff; text-align: center; }
        th:nth-child(2), th:nth-child(3), td:nth-child(2), td:nth-child(3) { text-align: left; }
        .section { page-break-after: always; }
        .section:last-child { page-break-after: auto; }
    </style>
</head>
<body>
@foreach ($reports as $report)
    <section class="section">
        <h1>LKUB</h1>
        <h2>Laporan Kegiatan Usaha Bulanan</h2>
        <h3>{{ $report['period'] }} - {{ $report['cabang']->cabang_name }}</h3>
        <table>
            <thead>
                <tr>
                    <th>NO</th><th>FOREX</th><th>TYPE</th><th>BG. BALANCE</th><th>BG. BALANCE (Rp.)</th>
                    <th>BUY</th><th>BUY (Rp.)</th><th>SELL</th><th>SELL (Rp.)</th><th>BALANCE</th><th>MIDDLE RATE</th><th>BALANCE (Rp.)</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td>{{ $row['no'] }}</td><td>{{ $row['forex'] }}</td><td>{{ $row['type'] }}</td>
                    <td>{{ number_format($row['bg_balance'], 2, ',', '.') }}</td><td>{{ number_format($row['bg_balance_rp'], 2, ',', '.') }}</td>
                    <td>{{ number_format($row['buy'], 2, ',', '.') }}</td><td>{{ number_format($row['buy_rp'], 2, ',', '.') }}</td>
                    <td>{{ number_format($row['sell'], 2, ',', '.') }}</td><td>{{ number_format($row['sell_rp'], 2, ',', '.') }}</td>
                    <td>{{ number_format($row['balance'], 2, ',', '.') }}</td><td>{{ number_format($row['middle_rate'], 4, ',', '.') }}</td><td>{{ number_format($row['balance_rp'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endforeach
</body>
</html>
