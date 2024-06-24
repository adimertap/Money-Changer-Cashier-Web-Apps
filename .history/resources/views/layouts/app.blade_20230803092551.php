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
    {{-- <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" defer></script> --}}
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
    crossorigin="anonymous" />
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
    <style>
        .table-responsive { font-size: 12px }
    </style>
   
    <script>
        var isFluid = JSON.parse(localStorage.getItem('isFluid'));
        if (isFluid) {
            var container = document.querySelector('[data-layout]');
            container.classList.remove('container');
            container.classList.add('container-fluid');
        }

        $(document).ready(function () {
            $('.changeBtn').click(function (e) {
                e.preventDefault();
                $('#modalChangePassword').modal('show');
            })
        })

    </script>
</head>


<body>
    @include('sweetalert::alert')
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            @include('layouts.navbar')
            <div class="content">
                @include('layouts.header')
                @yield('content')
            </div>
        </div>
        <div class="modal fade" id="modalChangePassword" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0">
                    <div class="position-absolute top-0 end-0 mt-3 me-3 z-index-1"><button
                            class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base"
                            data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <form action="{{ route('change_password') }}" method="POST">
                        @csrf
                        <div class="modal-body p-0">
                            <div class="bg-light rounded-top-lg py-3 ps-4 pe-6">
                                <h4 class="mb-1">Change Password</h4>
                            </div>
                            <div class="p-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="d-flex">
                                            <div class="flex-1">
                                                <input type="hidden" name="email" value={{ Auth::user()->email }}>
                                                <div class="col-12">
                                                    <label class="form-label" for="password">New Password</label>
                                                    <input class="form-control form-select-sm  @error('password') is-invalid @enderror"
                                                        name="password" type="password" placeholder="Input Password Baru Anda"
                                                        value="{{ old('password') }}" />
                                                    @error('password')
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                                                    <input class="form-control form-select-sm  @error('password_confirmation') is-invalid @enderror"
                                                        name="password_confirmation" type="password" placeholder="Confirm your Password"
                                                        value="{{ old('password_confirmation') }}" />
                                                    @error('password_confirmation')
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="text-danger small mt-3 fs--1">
                                                   Note:
                                                </div>
                                                <div class="text-muted small mt-0 mb-2 fs--1">
                                                    - Password must contain number <br>
                                                    - Minimum 6 Character with 1 Uppercase Letter
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button"
                                data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary btn-sm" type="submit">Simpan </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
  
  
    {{-- <script src="sweetalert2.all.min.js"></script> --}}
    <script src="/../falcon/vendors/choices/choices.min.js"></script>
    <script src="/../falcon/vendors/popper/popper.min.js"></script>
    <script src="/../falcon/vendors/bootstrap/bootstrap.min.js"></script>
    {{-- <script src="/../falcon/vendors/anchorjs/anchor.min.js"></script> --}}
    <script src="/../falcon/vendors/is/is.min.js"></script>
    {{-- <script src="/../falcon/vendors/echarts/echarts.min.js"></script> --}}
    <script src="/../falcon/vendors/fontawesome/all.min.js"></script>
    <script src="/../falcon/vendors/lodash/lodash.min.js"></script>
    {{-- <script src="/../falcon/vendors/list.js/list.min.js"></script> --}}
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/config.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    
</body>


</html>
