<table>
    <thead>
        <tr><th colspan="12">LKUB</th></tr>
        <tr><th colspan="12">Laporan Kegiatan Usaha Bulanan</th></tr>
        <tr><th colspan="12">{{ $periodLabel }} - {{ $report['cabang']->cabang_name }}</th></tr>
        <tr>
            <th>NO</th>
            <th>FOREX</th>
            <th>TYPE</th>
            <th>BG. BALANCE</th>
            <th>BG. BALANCE (Rp.)</th>
            <th>BUY</th>
            <th>BUY (Rp.)</th>
            <th>SELL</th>
            <th>SELL (Rp.)</th>
            <th>BALANCE</th>
            <th>MIDDLE RATE</th>
            <th>BALANCE (Rp.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report['rows'] as $row)
        <tr>
            <td>{{ $row['no'] }}</td>
            <td>{{ $row['forex'] }}</td>
            <td>{{ $row['type'] }}</td>
            <td>{{ $row['bg_balance'] }}</td>
            <td>{{ $row['bg_balance_rp'] }}</td>
            <td>{{ $row['buy'] }}</td>
            <td>{{ $row['buy_rp'] }}</td>
            <td>{{ $row['sell'] }}</td>
            <td>{{ $row['sell_rp'] }}</td>
            <td>{{ $row['balance'] }}</td>
            <td>{{ $row['middle_rate'] }}</td>
            <td>{{ $row['balance_rp'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
