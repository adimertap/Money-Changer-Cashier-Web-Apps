<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Cabang</title>
    <style>
        @page {
            margin: 20px 24px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
            line-height: 1.3;
        }
        .company-header {
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #8d2c2c;
            padding-bottom: 8px;
        }
        .company-header h1 {
            margin: 0;
            font-size: 16px;
            color: #8d2c2c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-header h2 {
            margin: 3px 0 2px;
            font-size: 12px;
            color: #2c3e50;
            font-weight: 600;
        }
        .company-header p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #555;
        }
        .section {
            page-break-after: always;
        }
        .section:last-child {
            page-break-after: auto;
        }
        .cabang-title {
            font-size: 11px;
            font-weight: bold;
            background: #f4f6f9;
            border-left: 4px solid #8d2c2c;
            padding: 5px 8px;
            margin-bottom: 8px;
            color: #333;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #bbb;
            padding: 5px 6px;
        }
        table.data-table th {
            background: #2c3e50;
            color: #fff;
            font-size: 8.5px;
            text-transform: uppercase;
            text-align: center;
        }
        td.text-center { text-align: center; }
        td.text-right { text-align: right; }
        td.text-left { text-align: left; }
        .summary-box {
            width: 55%;
            margin-left: auto;
            margin-top: 6px;
            border-collapse: collapse;
        }
        .summary-box td {
            border: none;
            padding: 3px 6px;
            font-size: 9px;
        }
        .summary-box tr.total-row td {
            border-top: 1px solid #333;
            border-bottom: 2px solid #333;
            font-weight: bold;
            background: #f8f9fa;
        }
        .footer-note {
            margin-top: 14px;
            font-size: 8px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>
@foreach ($reports as $report)
    <section class="section">
        <div class="company-header">
            <h1>PT RIASTA VALASINDO</h1>
            <h2>Laporan Rekapitulasi Operasional Cabang</h2>
            <p>Periode: {{ $periodLabel }}</p>
        </div>

        <div class="cabang-title">
            Cabang: {{ $report['cabang']->cabang_name }}
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 34%;">Currency</th>
                    <th style="width: 20%;">Jumlah (Valas)</th>
                    <th style="width: 20%;">Kurs</th>
                    <th style="width: 20%;">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['currency'] as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $item->nama }}</td>
                    <td class="text-right">{{ number_format($item->total, 2, ',', '.') }}</td>
                    <td class="text-right">Rp. {{ number_format($item->jumlah_kurs, 0, ',', '.') }}</td>
                    <td class="text-right">Rp. {{ number_format($item->total * $item->jumlah_kurs, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #888; padding: 10px;">Tidak ada transaksi valas debit tercatat pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <table class="summary-box">
            <tr>
                <td style="width: 55%;">Total Debit Tercatat</td>
                <td style="width: 5%;">:</td>
                <td class="text-right" style="width: 40%; font-weight: bold;">Rp. {{ number_format($report['totalDebit'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Penjualan Valas</td>
                <td>:</td>
                <td class="text-right" style="font-weight: bold;">Rp. {{ number_format($report['totalKredit'], 0, ',', '.') }}</td>
            </tr>
            @if ($report['totalModal'] > 0)
            <tr>
                <td>Total Modal Awal</td>
                <td>:</td>
                <td class="text-right" style="font-weight: bold;">Rp. {{ number_format($report['totalModal'], 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Sisa Modal</td>
                <td>:</td>
                <td class="text-right" style="color: #8d2c2c;">Rp. {{ number_format($report['grand'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer-note">
            Dicetak otomatis oleh Sistem Kasir PT Riasta Valasindo
        </div>
    </section>
@endforeach
</body>
</html>
