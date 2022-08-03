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
    <link rel="preconnect" href="https://fonts.gstatic.com/">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
        rel="stylesheet">
    <link href="/../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="/../falcon/assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl" disabled="true">
    <link href="/../falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="/../falcon/assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl" disabled="true">
    <link href="/../falcon/assets/css/user.min.css" rel="stylesheet" id="user-style-default">
</head>

<body>
    <main class="main" id="top">
        <div class="container-fluid">
            <div class="row min-vh-100 flex-center g-0">
                <div class="col-lg-8 col-xxl-5 py-3 position-relative"><img class="bg-auth-circle-shape"
                        src="../../falcon/assets/img/icons/spot-illustrations/bg-shape.png" alt="" width="250"><img
                        class="bg-auth-circle-shape-2" src="../falcon/assets/img/icons/spot-illustrations/shape-1.png"
                        alt="" width="150">
                    <div class="card overflow-hidden z-index-1">
                        <div class="card-body p-0">
                            <div class="row g-0 h-100">
                                <div class="col-md-5 text-center bg-card-gradient">
                                    <div class="position-relative p-4 pt-md-5 pb-md-7 light">
                                        <div class="bg-holder bg-auth-card-shape"
                                            style="background-image:url(../../../assets/img/icons/spot-illustrations/half-circle.png);">
                                        </div>
                                        <!--/.bg-holder-->
                                        <div class="z-index-1 position-relative"><a
                                                class="link-light mb-4 font-sans-serif fs-4 d-inline-block fw-bolder"
                                                href="#">Money Changer</a>
                                            <p class="opacity-75 text-white">Silahkan melakukan login terlebih dahulu
                                                sebelum masuk ke dalam sistem kasir money changer!
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 mb-4 mt-md-4 mb-md-5 light">
                                        <p class="text-white">PT. Riasta Valasindo
                                        </p>
                                        <p class="mb-0 mt-4 mt-md-5 fs--1 fw-semi-bold text-white opacity-75">Read our
                                            <a class="text-decoration-underline text-white" href="#!">terms</a> and <a
                                                class="text-decoration-underline text-white" href="#!">conditions </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex flex-center">
                                    <div class="p-4 p-md-5 flex-grow-1">
                                        <div class="row flex-between-center">
                                            <div class="col-auto">
                                                <h3>Account Login</h3>
                                            </div>
                                        </div>
                                        <form class="form-body" form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label" for="inputEmailAddress">Email address</label>
                                                <input class="form-control @error('email') is-invalid @enderror"
                                                    id="inputEmailAddress" placeholder="Email Address" type="email"
                                                    name="email" value="{{ old('email') }}" required
                                                    autocomplete="email" autofocus>
                                                    @error('email')
                                                    <div class="invalid-feedback">
                                                        <strong>Email atau Password yang Anda Masukan Salah</strong>
                                                    </div>
                                                    @enderror
                                            </div>
                                        

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between"><label class="form-label"
                                                        for="inputChoosePassword">Password</label></div>
                                                <input class="form-control @error('password') is-invalid @enderror"
                                                    id="inputChoosePassword" placeholder="Enter Password"
                                                    type="password" name="password" required
                                                    autocomplete="current-password">
                                                    @error('password')
                                                    <div class="invalid-feedback">
                                                        <strong>Email atau Password yang Anda Masukan Salah</strong>
                                                    </div>
                                                    @enderror
                                            </div>
                                            

                                            <div class="row flex-between-center">
                                                <div class="col-auto">
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" id="card-checkbox"
                                                        checked="" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        <label class="form-check-label mb-0" for="card-checkbox">Remember me</label></div>
                                                </div>
                                                <div class="col-auto"><a class="fs--1"
                                                        href="{{ route('password.request') }}">Forgot Password?</a></div>
                                            </div>
                                            <div class="mb-3"><button class="btn btn-primary d-block w-100 mt-3"
                                                    type="submit" >Log in</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <script src="/../falcon/vendors/popper/popper.min.js"></script>
    <script src="/../falcon/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="/../falcon/vendors/anchorjs/anchor.min.js"></script>
    <script src="/../falcon/vendors/is/is.min.js"></script>
    <script src="/../falcon/vendors/echarts/echarts.min.js"></script>
    <script src="/../falcon/vendors/fontawesome/all.min.js"></script>
    <script src="/../falcon/vendors/lodash/lodash.min.js"></script>
    <script src="/../falcon/vendors/list.js/list.min.js"></script>
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/theme.js"></script>
    <script src="/../falcon/assets/js/config.js"></script>


</body>

</html>
