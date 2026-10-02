<table>
    <thead>
        <tr>
            <th colspan="5">Rekapitulasi Operasional Cabang: {{ $report['cabang']->cabang_name }}</th>
        </tr>
        <tr>
            <th>No.</th>
            <th>Currency</th>
            <th>Jumlah</th>
            <th>Kurs</th>
            <th>Grand</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report['currency'] as $item)
        <tr>
            <td>{{ $loop->iteration }}.</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->total }}</td>
            <td>Rp. {{ number_format($item->jumlah_kurs, 0, ',', '.') }}</td>
            <td>Rp. {{ number_format($item->total * $item->jumlah_kurs, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total Debit Tercatat</th>
            <th>Rp. {{ number_format($report['totalDebit'], 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="4">Penjualan Valas</th>
            <th>Rp. {{ number_format($report['totalKredit'], 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="4">Sisa Modal</th>
            <th>Rp. {{ number_format($report['grand'], 0, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>
