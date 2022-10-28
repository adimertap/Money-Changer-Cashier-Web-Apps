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
		<h5>Laporan Transaksi Hari Ini</h4>
        <p>{{ $today }}</h4>
	</center>
 
	<table class='table table-bordered'>
		<thead>
          
			<tr>
				<th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
				<th>Pegawai</th>
                <th>Currency</th>
				<th>Harga Currency</th>
                <th>Jumlah Tukar</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			@php 
				$i=1; 
				$total_banget = 0; 
				foreach ($transaksi as $key => $item) {
					$total_banget = $total_banget + $item->total_tukar;
				}
				
			@endphp
			@foreach($transaksi as $p)
			<tr>
				<td>{{ $i++ }}</td>
                <td>{{$p->kode_transaksi}}</td>
				<td>{{ date('d-M-Y', strtotime($p->tanggal_transaksi)) }}</td>
				<td>{{$p->Pegawai->name}}</td>
				<td>{{$p->nama_currency}}, {{ $p->jenis_Kurs }}</td>
				<td>Rp. {{ number_format($p->jumlah_currency)}}</td>
				<td>{{$p->jumlah_tukar}}</td>
				<td>Rp. {{ number_format($p->total_tukar)}}</td>
			</tr>
			@endforeach
		</tbody>
        <tr>
            <th colspan="6">Transaksi</th>
            <th colspan="1">{{ $jumlah }} Transaksi</th>
            <th colspan="1">Rp. {{ number_format($total_banget) }}</th>
        </tr>
	</table>
 
</body>
</html>