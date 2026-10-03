<table>
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
        @php
            $jt = (float) $item->jumlah_tukar;
            $kr = (float) $item->kurs;
            $jtFmt = (floor($jt) == $jt) ? '#,##0' : '#,##0.00';
            $krFmt = (floor($kr) == $kr) ? '#,##0' : '#,##0.00##';
        @endphp
        <tr>
            <th>{{ $i++ }}.</th>
            <td>{{ date('d-M-Y H:i:s', strtotime($item->updated_at)) }}</td>
            @if ($item->jenis_jurnal == 'Debit')
                <td>Jual</td>
                <td>{{ $item->Currency->nama_currency ?? '' }}, {{ $item->Currency->jenis_kurs ?? '' }}</td>
                <td data-format="{{ $jtFmt }}">{{ floor($jt) == $jt ? (int)$jt : $jt }}</td>
                <td data-format="{{ $krFmt }}">{{ floor($kr) == $kr ? (int)$kr : $kr }}</td>
                <td data-format="#,##0">{{ round((float) $item->total_tukar) }}</td>
                <td>-</td>
            @elseif ($item->jenis_jurnal == 'Kredit Jual')
                <td>Jual Valas</td>
                <td>{{ $item->Currency->nama_currency ?? '' }}, {{ $item->Currency->jenis_kurs ?? '' }}</td>
                <td data-format="{{ $jtFmt }}">{{ floor($jt) == $jt ? (int)$jt : $jt }}</td>
                <td data-format="{{ $krFmt }}">{{ floor($kr) == $kr ? (int)$kr : $kr }}</td>
                <td>-</td>
                <td data-format="#,##0">{{ round((float) $item->total_tukar) }}</td>
            @else
                <td>Modal</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td data-format="#,##0">{{ round((float) $item->jumlah_modal) }}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th colspan="1"></th>
        <th colspan="5">Total Debit dan Kredit</th>
        <th colspan="1" data-format="#,##0">{{ round((float) $total_debit) }}</th>
        <th colspan="1" data-format="#,##0">{{ round((float) ($total_kredit + $total_modal)) }}</th>
    </tr>
</table>

<table>
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
        @php
            $itTotal = (float) $item->total;
            $itKurs = (float) $item->jumlah_kurs;
            $itTotalFmt = (floor($itTotal) == $itTotal) ? '#,##0' : '#,##0.00';
            $itKursFmt = (floor($itKurs) == $itKurs) ? '#,##0' : '#,##0.00##';
        @endphp
        <tr>
            <th>{{ $i++ }}.</th>
            <td>{{ $item->nama }}</td>
            <td data-format="{{ $itTotalFmt }}">{{ floor($itTotal) == $itTotal ? (int)$itTotal : $itTotal }}</td>
            <td data-format="{{ $itKursFmt }}">{{ floor($itKurs) == $itKurs ? (int)$itKurs : $itKurs }}</td>
            <td data-format="#,##0">{{ round((float) ($item->total * $item->jumlah_kurs)) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th colspan="4">Total Debit Tercatat</th>
        <th colspan="1" data-format="#,##0">{{ round((float) $total_debit) }}</th>
    </tr>
    <tr>
        <th colspan="4">Penjualan Valas</th>
        <th colspan="1" data-format="#,##0">{{ round((float) $total_kredit) }}</th>
    </tr>
    <tr>
        <th colspan="4">Sisa Modal</th>
        <th colspan="1" data-format="#,##0">{{ round((float) $grand) }}</th>
    </tr>
</table>
