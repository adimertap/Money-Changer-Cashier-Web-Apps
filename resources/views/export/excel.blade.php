<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Pegawai</th>
            <th>Jenis Transaksi</th>
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
            <td>{{$p->nama_currency}}, {{ $p->jenis_kurs }}</td>
            <td>Rp. {{ number_format($p->jumlah_currency)}}</td>
            <td>{{$p->jumlah_tukar}}</td>
            <td>Rp. {{ number_format($p->total_tukar)}}</td>
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th colspan="7">Transaksi</th>
        <th colspan="1">{{ $today }} Transaksi</th>
        <th colspan="1">Rp. {{ number_format($total_banget) }}</th>
    </tr>
</table>