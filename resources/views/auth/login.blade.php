<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PT. Riasta Valasindo</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/../falcon/assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/../falcon/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/../falcon/assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="/../falcon/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#3864ff">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="/../falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">

    <style>
        :root {
            --primary-blue: #3b66ff;
            --primary-blue-hover: #2b54eb;
            --primary-dark-blue: #1d39c4;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Container Full Screen Tanpa Card */
        .auth-container {
            display: flex;
            width: 100vw;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* Sisi Kiri: Form Login Full Height */
        .auth-form-side {
            flex: 1 1 50%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 56px 40px;
            background: #ffffff;
        }

        .form-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-blue), #60a5fa);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(59, 102, 255, 0.28);
        }

        .brand-name {
            font-weight: 800;
            font-size: 21px;
            color: #2563eb;
            letter-spacing: -0.4px;
        }

        .auth-title {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.6px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        /* Form Inputs */
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon-left {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .input-icon-right {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .input-icon-right:hover {
            color: #475569;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 46px 14px 48px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            font-size: 14px;
            color: #1e293b;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control-custom:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 102, 255, 0.12);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
            font-size: 13.5px;
        }

        .form-control-custom.is-invalid {
            border-color: #ef4444;
            padding-right: 46px;
        }

        .form-control-custom:focus ~ .input-icon-left {
            color: var(--primary-blue);
        }

        .invalid-feedback-custom {
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
            padding-left: 18px;
        }

        /* Options Row */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
            font-size: 13px;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: #475569;
        }

        .custom-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            accent-color: var(--primary-blue);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark-blue);
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: 50px;
            border: none;
            background: linear-gradient(135deg, #4f75ff 0%, #3b66ff 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 22px rgba(59, 102, 255, 0.32);
            transition: all 0.25s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #436cf9 0%, #2b54eb 100%);
            box-shadow: 0 10px 26px rgba(59, 102, 255, 0.42);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .auth-footer a:hover {
            color: var(--primary-dark-blue);
            text-decoration: underline;
        }

        /* Sisi Kanan: Hero Banner Biru (Center Perfectly) */
        .auth-hero-side {
            flex: 1 1 50%;
            min-height: 100vh;
            background: linear-gradient(145deg, #5177ff 0%, #3864ff 50%, #2952e8 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            color: #ffffff;
            text-align: center;
        }

        /* Watermark Background Circles Centered */
        .hero-circle-1 {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.09);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .hero-circle-2 {
            position: absolute;
            width: 440px;
            height: 440px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.14);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .hero-circle-3 {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        /* Container Pembungkus Konten Sisi Kanan Agar Terpusat di Tengah */
        .hero-center-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto 0;
        }

        /* Wrapper Window Mockup & Floating Badges */
        .hero-window-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* Text-based Window Mockup di Sisi Kanan */
        .hero-window-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
            border-radius: 20px;
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.22);
            overflow: hidden;
            text-align: left;
            color: #1e293b;
        }

        .window-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .window-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .window-dot-red { background-color: #ef4444; }
        .window-dot-yellow { background-color: #f59e0b; }
        .window-dot-green { background-color: #10b981; }

        .window-header-title {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            margin-left: 8px;
            letter-spacing: 0.3px;
        }

        .window-body {
            padding: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            margin-bottom: 10px;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .feature-icon-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #eff6ff;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-content h6 {
            margin: 0 0 3px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .feature-content p {
            margin: 0;
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Floating Pill Badges di Sekitar Window (Text Only) */
        .floating-pill {
            position: absolute;
            background: #ffffff;
            color: #1e293b;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.16);
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .floating-pill-1 {
            top: -14px;
            left: -12px;
        }

        .floating-pill-2 {
            bottom: 30px;
            right: -12px;
        }

        /* Hero Text di Bawah (Centered) */
        .hero-text-container {
            margin-top: 36px;
            width: 100%;
            max-width: 440px;
        }

        .hero-title {
            font-size: 27px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: -0.4px;
        }

        .hero-desc {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.55;
            margin-bottom: 22px;
        }

        .hero-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .dot-pill {
            width: 24px;
            height: 5px;
            border-radius: 4px;
            background: #ffffff;
        }

        .dot-circle {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.45);
        }

        /* Responsiveness */
        @media (max-width: 991px) {
            .auth-container {
                flex-direction: column-reverse;
            }
            .auth-form-side {
                min-height: auto;
                padding: 48px 24px;
            }
            .auth-hero-side {
                min-height: auto;
                padding: 48px 24px;
            }
            .floating-pill {
                display: none;
            }
        }
    </style>
</head>

<body>
    @include('sweetalert::alert')

    <div class="auth-container">
        <!-- Sisi Kiri: Form Login Full Height -->
        <div class="auth-form-side">
            <div class="form-wrapper">
                <!-- Brand Logo -->
                <div class="brand-header">
                    <div class="brand-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                            <circle cx="16" cy="15" r="1"></circle>
                        </svg>
                    </div>
                    <span class="brand-name">PT. Riasta Valasindo</span>
                </div>

                <!-- Header Text -->
                <h1 class="auth-title">Log in to your Account</h1>
                <p class="auth-subtitle">Welcome back! Please enter your details to sign in:</p>

                <!-- Form Body -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="input-group-custom">
                        <span class="input-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input class="form-control-custom @error('email') is-invalid @enderror"
                            id="inputEmailAddress"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email Address"
                            required
                            autocomplete="email"
                            autofocus>
                        @error('email')
                        <div class="invalid-feedback-custom">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="input-group-custom">
                        <span class="input-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input class="form-control-custom @error('password') is-invalid @enderror"
                            id="inputChoosePassword"
                            type="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="current-password">
                        <span class="input-icon-right" id="togglePasswordBtn" title="Tampilkan/Sembunyikan Password">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </span>
                        @error('password')
                        <div class="invalid-feedback-custom">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>

                    <!-- Options: Remember Me & Forgot Password -->
                    <div class="options-row">
                        <label class="custom-checkbox" for="rememberCheckbox">
                            <input type="checkbox" id="rememberCheckbox" name="remember" {{ old('remember') ? 'checked' : 'checked' }}>
                            <span>Remember me</span>
                        </label>
                        <a class="forgot-link" href="{{ route('change_password_v2') }}">Forgot Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn-submit" type="submit">Login </button>

                    <!-- Footer: Hubungi Admin (Non-clickable) -->
                    <div class="auth-footer">
                        <span>Belum memiliki akun atau terkendala akses? Hubungi Admin</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sisi Kanan: Hero Banner Biru (Center On Screen, Text Only) -->
        <div class="auth-hero-side">
            <!-- Background Watermark Circles Centered -->
            <div class="hero-circle-1"></div>
            <div class="hero-circle-2"></div>
            <div class="hero-circle-3"></div>

            <div class="hero-center-content">
                <!-- Wrapper Mockup Window & Floating Badges -->
                <div class="hero-window-wrapper">
                    <!-- Floating Pill 1 -->
                    <div class="floating-pill floating-pill-1">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Bank Indonesia LKUB</span>
                    </div>

                    <!-- Window Card Berisi Teks Fitur Unggulan -->
                    <div class="hero-window-card">
                        <div class="window-header">
                            <span class="window-dot window-dot-red"></span>
                            <span class="window-dot window-dot-yellow"></span>
                            <span class="window-dot window-dot-green"></span>
                            <span class="window-header-title">PT. RIASTA VALASINDO CASHIER</span>
                        </div>
                        <div class="window-body">
                            <div class="feature-item">
                                <div class="feature-icon-badge">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 10h14l-4-4m4 8H3l4 4"/>
                                    </svg>
                                </div>
                                <div class="feature-content">
                                    <h6>Transaksi Valas & Kurs Real-Time</h6>
                                    <p>Pencatatan jual beli valuta asing multi-currency otomatis dan presisi.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon-badge">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                </div>
                                <div class="feature-content">
                                    <h6>Laporan LKUB & Summary Valas</h6>
                                    <p>Ekspor laporan bulanan format Excel, PDF, CSV, TXT, dan ringkasan mutasi valas.</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon-badge">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </div>
                                <div class="feature-content">
                                    <h6>Absensi Geofencing Cabang</h6>
                                    <p>Verifikasi kehadiran kasir berbasis titik koordinat & radius lokasi cabang.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Pill 2 -->
                    <div class="floating-pill floating-pill-2">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Realtime Currency</span>
                    </div>
                </div>

                <!-- Hero Text di Bawah Window (Centered) -->
                <div class="hero-text-container">
                    <h2 class="hero-title">Solusi Transaksi Valas Terpadu</h2>
                    <p class="hero-desc">Pencatatan transaksi kasir, kurs valas real-time, dan pelaporan keuangan LKUB yang akurat serta aman.</p>
                    <div class="hero-dots">
                        <span class="dot-pill"></span>
                        <span class="dot-circle"></span>
                        <span class="dot-circle"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Toggle Show/Hide Password -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('inputChoosePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePasswordBtn && passwordInput && eyeIcon) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        // Tampilkan ikon mata terbuka
                        eyeIcon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        `;
                    } else {
                        // Tampilkan ikon mata tertutup (coret)
                        eyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        `;
                    }
                });
            }
        });
    </script>
</body>

</html>
