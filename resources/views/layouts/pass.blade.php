<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"><!-- /Added by HTTrack -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PT. Riasta Valasindo</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../falcon/assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../falcon/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../falcon/assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="/../falcon/assets/img/favicons/favicon.ico">
    <link rel="manifest" href="/../falcon/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="/../falcon/assets/js/config.js"></script>
    <script src="/../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
        rel="stylesheet">
    <link href="/../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="/../falcon/assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl" disabled="true">
    <link href="/../falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="/../falcon/assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl" disabled="true">
    <link href="/../falcon/assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <link href="/../falcon/vendors/choices/choices.min.css" rel="stylesheet" />
    
   
    <script>
        var isFluid = JSON.parse(localStorage.getItem('isFluid'));
        if (isFluid) {
            var container = document.querySelector('[data-layout]');
            container.classList.remove('container');
            container.classList.add('container-fluid');
        }

    </script>
</head>


<body>
    @include('sweetalert::alert')
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            <div class="content">
                @yield('content')
            </div>

        </div>
    </main>
  
  
    {{-- <script src="sweetalert2.all.min.js"></script> --}}
    <script src="/../falcon/vendors/choices/choices.min.js"></script>
    <script src="/../falcon/vendors/popper/popper.min.js"></script>
    <script src="/../falcon/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="/../falcon/vendors/anchorjs/anchor.min.js"></script>
    <script src="/../falcon/vendors/is/is.min.js"></script>
    <script src="/../falcon/vendors/echarts/echarts.min.js"></script>
    <script src="/../falcon/vendors/fontawesome/all.min.js"></script>
    <script src="/../falcon/vendors/lodash/lodash.min.js"></script>
    {{-- <script src="/../falcon/vendors/list.js/list.min.js"></script> --}}
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/config.js"></script>
    
</body>


</html>
