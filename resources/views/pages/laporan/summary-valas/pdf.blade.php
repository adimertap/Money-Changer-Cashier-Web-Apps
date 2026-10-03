<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Summary Valas</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h1, h2, h3 { margin: 0; text-align: center; }
        h1 { font-size: 18px; color: #8d2c2c; }
        h2 { font-size: 11px; margin-top: 4px; }
        h3 { font-size: 10px; margin: 4px 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #333; padding: 5px 4px; text-align: right; }
        th { background: #343a40; color: #fff; text-align: center; }
        th:nth-child(1), th:nth-child(2), td:nth-child(1), td:nth-child(2) { text-align: left; }
    </style>
</head>
<body>
    <h1>Summary Valas</h1>
    <h2>{{ $report['period_label'] }}</h2>
    <h3>{{ $report['branch_label'] }}</h3>
    <table>
        <thead>
            <tr>
                <th>FOREX CODE</th>
                <th>NAMA CURRENCY</th>
                <th>BEGINNING BALANCE</th>
                <th>MUTATION BUY</th>
                <th>MUTATION SELL</th>
                <th>ENDING BALANCE</th>
                <th>MUTATION (IDR) BUY</th>
                <th>MUTATION (IDR) SELL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
            <tr>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ number_format($row['beginning_balance'], 3, '.', ',') }}</td>
                <td>{{ number_format($row['buy'], 3, '.', ',') }}</td>
                <td>{{ number_format($row['sell'], 3, '.', ',') }}</td>
                <td>{{ number_format($row['ending_balance'], 3, '.', ',') }}</td>
                <td>Rp. {{ number_format($row['buy_idr'], 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($row['sell_idr'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
