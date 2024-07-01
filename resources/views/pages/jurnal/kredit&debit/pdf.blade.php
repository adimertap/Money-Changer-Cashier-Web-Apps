<!DOCTYPE html>
<html>
<head>
	<title>Laporan Transaksi</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h5>Laporan Transaksi</h4>
        <p>PT. Riasta Valasindo</h4>
	</center>

	<table class='table table-bordered'>
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Currency</th>
            <th>Jumlah Tukar</th>
            <th>Kurs</th>
            <th>Debit</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i=1;
        $total_debit = 0;
        $total_kredit = 0;
        $total_modal = 0;
        foreach ($totalDebit as $key => $item) {
            $total_debit = $total_debit + $item->total_tukar;
        }

        foreach ($totalKredit as $key => $value) {
            $total_kredit = $total_kredit + $value->total_tukar;
        }

        foreach ($totalModal as $key => $modal) {
            $total_modal = $total_modal + $modal->jumlah_modal;
        }

        $grand = ($total_kredit + $total_modal) - $total_debit;
    @endphp
        @foreach ($jurnal as $item)
        <tr>
            <th>{{ $i++ }}.</th>
            <td>{{ date('d-M-Y H:i:s', strtotime($item->updated_at)) }}</td>
            @if ($item->jenis_jurnal == 'Debit')
                <td>Jual</td>
                <td>{{ $item->Currency->nama_currency ?? '' }}, {{ $item->Currency->jenis_kurs ?? '' }}</td>
                <td>{{ $item->jumlah_tukar }}</td>
                <td>Rp. {{ number_format($item->kurs, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($item->total_tukar, 0, ',', '.') }}</td>
                <td>-</td>
                @elseif ($item->jenis_jurnal == 'Kredit Jual')
                <td>Jual Valas</td>
                <td>{{ $item->Currency->nama_currency ?? '' }}, {{ $item->Currency->jenis_kurs ?? '' }}</td>
                <td>{{ $item->jumlah_tukar }}</td>
                <td>Rp. {{ number_format($item->kurs, 0, ',', '.') }}</td>
                <td>-</td>
                <td>Rp. {{ number_format($item->total_tukar, 0, ',', '.') }}</td>
            @else
                <td>Modal</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>Rp. {{ number_format($item->jumlah_modal, 0, ',', '.') }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th colspan="1"></th>
        <th colspan="5">Total Debit dan Kredit</th>
        <th colspan="1">Rp. {{ number_format($total_debit, 0, ',', '.') }}</th>
        <th colspan="1">Rp. {{ number_format($total_kredit + $total_modal, 0, ',', '.') }}</th>
    </tr>
</table>

<table class='table table-bordered'>
    <thead>
        <tr>
            <th>No.</th>
            <th>Currency</th>
            <th>Jumlah</th>
            <th>Kurs</th>
            <th>Grand</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i=1;

        @endphp
        @foreach ($kurs as $item)
        <tr>
            <th>{{ $i++ }}.</th>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->total }}</td>
            <td>Rp. {{ number_format($item->jumlah_kurs, 0, ',','.') }}</td>
            <td>Rp. {{ number_format($item->total * $item->jumlah_kurs, 0, ',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tr></tr>
    <tr>
        <th colspan="4">Total Debit Tercatat</th>
        <th colspan="1">Rp. {{ number_format($total_debit, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th colspan="4">Penjualan Valas</th>
        <th colspan="1">Rp. {{ number_format($total_kredit, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th colspan="4">Sisa Modal</th>
        <th colspan="1">Rp. {{ number_format($grand, 0, ',', '.') }}</th>
    </tr>
</table>

</body>
</html>
