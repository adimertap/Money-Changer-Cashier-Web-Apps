<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"><!-- /Added by HTTrack -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Receipt</title>
    <link rel="manifest" href="/../../falcon/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="/../../falcon/assets/js/config.js"></script>
    <script src="/../../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
        rel="stylesheet">
    <link href="/../../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="/../../falcon/assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl" disabled="true">
    <link href="/../../falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="/../../falcon/assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl" disabled="true">
    <link href="/../../falcon/assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <style>
        @page {
            size: 58mm 50mm
        }

        body.receipt .sheet {
            width: 58mm;
            height: 50mm
        }

        @media print {
            body.receipt {
                width: 58mm
            }
        }

        body {
            margin: 0
        }

        margin: 0;
        overflow: hidden;
        position: relative;
        box-sizing: border-box;
        page-break-after: always;
        }

        @media print {

            html,
            body {
                size: 80mm;
                /* auto is the initial value */
                size: 80mm portrait;
                margin: 0;
                /* this affects the margin in the printer settings */
                border: 1px solid red;
                /* set a border for all printed pages */
                position: absolute;
            }
        }

        .page {
            width: 80mm;
            min-height: auto;
            padding: 0.4cm;
            margin: 0cm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .text-kecil {
            font-size: 16pt;
        }

        @page {
            size: A4;
            margin: 0;
        }

    </style>
</head>


<body class="page">
    <main class="text-kecil" style="color: white">
        <div class="row align-items-center  text-center mb-3 mt-3">
            <div class="col-sm-2 text-sm-start justify-content-center">
                <img class="ms-3 mb-3" src="{{ asset('pt.png') }}" width="40" alt="invoice" width="150"></div>
            <div class="col-sm-9 text-center mt-3">
                <h6 class="text-danger fw-bold m-0 ">PT. Riasta Valasindo</h6>
                <h6 class="m-0 text-600">Authorized Money Changer</h6>
                <p class="fs--2 text-black m-0 text-600">Legian Street No. 39</p>
                <p class="fs--2 text-black m-0 text-600">Phone: 0361-7409732</p>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <h6 class="text-500 fs--2">{{ $transaksi->tanggal_transaksi }}</h6>
                <h6 class="m-0 fs--2 text-600">Buying</h6>
                <p class="fs--2 text-black m-0 text-600">{{ $transaksi->Pegawai->nama_panggilan }}</p>
            </div>
            <div class="col text-end">
                <h6 class="text-500 fs--2">{{ date('H:i:s', strtotime($transaksi->created_at)) }}</h6>
                <h6 class="text-500 fs--2 m-0">Order</h6>
                <p class="fs--2 text-danger fw-bold m-0">{{ $transaksi->kode_transaksi }}</p>
            </div>
        </div>
        <hr class="mt-2 mb-2" style="height:1px;border-width:0;color:gray;background-color:gray">
        @forelse ($transaksi->detailTransaksi as $item)
        <div class="row align-items-center mt-2">
            <div class="col-sm-1">
                <h6 class="text-500 fs--2">{{ $loop->iteration }}.</h6>
            </div>
            <div class="col-4">
                <p class="fs--2 text-danger fw-bold m-0">{{ $item->Currency->nama_currency }}</p>
                <h6 class="m-0 fs--2 text-600">{{ number_format($item->jumlah_currency) }}</h6>
            </div>
            <div class="col-1 mt-3">
                <p class="fs--2 text-danger fw-bold m-0 text-600">{{ $item->jumlah_tukar }}</p>
            </div>
            <div class="col-5 text-end mt-3 ms-2">
                <h6 class="m-0 fs--2 text-600">{{ number_format($item->total_tukar) }}</h6>
            </div>
        </div>
        <hr class="mt-2 mb-2" style="height:1px;border-width:0;color:gray;background-color:gray">

        @empty

        @endforelse

        <div class="row">
            <div class="col-6 text-start">
                <h6 class="m-0 fs--2 text-600">Total Rp.</h6>
            </div>
            <div class="col-6 text-end">
                <p class="fs--2 text-danger fw-bold m-0" id="total">{{ number_format($transaksi->total) }}</p>
            </div>
            <h6 class="text-600 m-0 fs--2 text-center mt-4">#Customer Signature</h6>
        </div>
        <div class=" mt-7">
         
           
        </div>
        <hr class="mb-2" style="height:1px;border-width:0;color:gray;background-color:gray">
        <p class="text-600 fs--2 text-center m-0 justify">
            THANK YOU FOR TRANSACTING WITH PT. RIASTA VALASINDO.
            PLEASE CHECK YOUR CASH AND TRANSACTION BEFORE LEAVING. 
            FOR ANY QUERY OR COMPLAINT. PLEASE CONTACT <br>
            +62-813-389-394-54 OR EMAIL: <br>
            info@ptriastavalasindo.com

        </p>
        {{-- @php
            function penyebut($nilai) {
                $nilai = abs($nilai);
                $huruf = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven","Twelve",'Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen','Twenty','Twenty One','Twenty Two','Twenty Three');
                $temp = "";
                if ($nilai < 23) {
                    $temp = " ". $huruf[$nilai];
                } else if ($nilai < 100) {
                    $temp = " And ".penyebut($nilai/10)."ty". penyebut($nilai % 10);
                } else if ($nilai < 1000) {
                    $temp = penyebut($nilai/100) . " Hundred" . penyebut($nilai % 100);
                } else if ($nilai < 2000) {
                    $temp = " seribu" . penyebut($nilai - 1000);
                } else if ($nilai < 1000000) {
                    $temp = penyebut($nilai/1000) . " Thousand" . penyebut($nilai % 1000);
                } else if ($nilai < 1000000000) {
                    $temp = penyebut($nilai/1000000) . " Million" . penyebut($nilai % 1000000);
                } else if ($nilai < 1000000000000) {
                    $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
                } else if ($nilai < 1000000000000000) {
                    $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
                }     
                return $temp;
            }

            function terbilang($nilai) {
                if($nilai<0) {
                    $hasil = "minus ". trim(penyebut($nilai));
                } else {
                    $hasil = trim(penyebut($nilai));
                }     		
                return $hasil;
            }

            $terbilang = terbilang($transaksi->total);
        @endphp --}}
       



    </main>

    <script>
          $(document).ready(function () {

          });

        

    </script>

    {{-- <script src="sweetalert2.all.min.js"></script> --}}
    <script src="/../../falcon/vendors/choices/choices.min.js"></script>
    <script src="/../../falcon/vendors/popper/popper.min.js"></script>
    <script src="/../../falcon/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="/../../falcon/vendors/anchorjs/anchor.min.js"></script>
    <script src="/../../falcon/vendors/is/is.min.js"></script>
    <script src="/../../falcon/vendors/echarts/echarts.min.js"></script>
    <script src="/../../falcon/vendors/fontawesome/all.min.js"></script>
    <script src="/../../falcon/vendors/lodash/lodash.min.js"></script>
    <script src="/../../falcon/vendors/list.js/list.min.js"></script>
    <script src="/../../falcon/assets/js/theme.js"></script>
    <script src="/../../falcon/assets/js/theme.js"></script>
    <script src="/../../falcon/assets/js/config.js"></script>

</body>


</html>
