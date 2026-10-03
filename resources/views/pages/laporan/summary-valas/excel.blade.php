<table>
    <thead>
        <tr><th colspan="8">Summary Valas</th></tr>
        <tr><th colspan="8">{{ $report['period_label'] }} - {{ $report['branch_label'] }}</th></tr>
        <tr><th colspan="8"></th></tr>
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
            <td>{{ $row['beginning_balance'] }}</td>
            <td>{{ $row['buy'] }}</td>
            <td>{{ $row['sell'] }}</td>
            <td>{{ $row['ending_balance'] }}</td>
            <td>{{ $row['buy_idr'] }}</td>
            <td>{{ $row['sell_idr'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
