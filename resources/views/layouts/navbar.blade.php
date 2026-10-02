<style>
    #navbarVerticalNav > .nav-item + .nav-item {
        border-top: 1px solid rgba(75, 85, 99, .18);
        margin-top: .55rem;
        padding-top: .35rem;
    }

    #navbarVerticalNav > .nav-item > .nav-link {
        margin-bottom: .15rem;
    }

    #navbarVerticalNav .nav.collapse > .nav-item + .nav-item {
        margin-top: .1rem;
    }

    #navbarVerticalNav .nav.collapse > .nav-item > .nav-link {
        padding-top: .35rem;
        padding-bottom: .35rem;
    }

    #navbarVerticalNav #laporanAbsen {
        padding-top: .15rem;
    }
</style>

<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
    <div class="d-flex align-items-center">
        <div class="toggle-icon-wrapper"></div>
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <div class="d-flex align-items-center py-3"><span class="font-sans-serif">Kasir</span></div>
        </a>
    </div>
    @php
    $open = [
        'master' => request()->routeIs('role-hak-akses.*', 'master-*'),
        'jadwal' => true,
        'transaksi' => true,
        'pelaporan' => request()->routeIs('transaksi.index', 'transaksi.show', 'jurnal-*', 'bulanan-transaksi', 'getUserReport', 'jadwal-laporan.*', 'report-jadwal-harian', 'laporan-rekap-cabang.*'),
        'absen' => request()->routeIs('getUserReport', 'jadwal-laporan.*', 'report-jadwal-harian'),
        'log' => request()->routeIs('approval-modal.*', 'log-edit.*', 'filterLog'),
    ];
    @endphp
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                @menuAccess(['dashboard', 'web-exchange'])
                <li class="nav-item">
                    @menuAccess('dashboard')
                    <a class="nav-link" href="{{ route('dashboard') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon"><i class="fas fa-home"></i></span>
                            <span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>
                    @endmenuAccess
                    @menuAccess('web-exchange')
                    <a class="nav-link" href="{{ url('https://www.rate.ptriastavalasindo.com') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon"><i class="fas fa-server"></i></span>
                            <span class="nav-link-text ps-1">Web Exchange</span>
                        </div>
                    </a>
                    @endmenuAccess
                </li>
                @endmenuAccess

                @menuAccess(['master-cabang', 'master-pegawai', 'role-hak-akses', 'master-currency', 'master-customer', 'master-terduga', 'master-threshold'])
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator {{ $open['master'] ? '' : 'collapsed' }}" href="#navMaster" role="button"
                        data-bs-toggle="collapse" aria-expanded="{{ $open['master'] ? 'true' : 'false' }}" aria-controls="navMaster">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-database"></i></span><span class="nav-link-text ps-1">Master Data</span></div>
                    </a>
                    <ul class="nav collapse {{ $open['master'] ? 'show' : '' }}" id="navMaster">
                        @menuAccess('master-cabang')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-cabang.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-store"></i></span><span class="nav-link-text ps-1">Cabang</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('master-pegawai')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-pegawai.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-user-friends"></i></span><span class="nav-link-text ps-1">Pegawai</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('role-hak-akses')
                        <li class="nav-item"><a class="nav-link" href="{{ route('role-hak-akses.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-user-lock"></i></span><span class="nav-link-text ps-1">Role &amp; Hak Akses</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('master-currency')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-currency') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-dollar-sign"></i></span><span class="nav-link-text ps-1">Currency</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('master-customer')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-customer.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-users"></i></span><span class="nav-link-text ps-1">Customer</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('master-terduga')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-terduga.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-user-shield"></i></span><span class="nav-link-text ps-1">Terduga</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('master-threshold')
                        <li class="nav-item"><a class="nav-link" href="{{ route('master-threshold.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-sliders-h"></i></span><span class="nav-link-text ps-1">Batas Atas Transaksi</span></div></a></li>
                        @endmenuAccess
                    </ul>
                </li>
                @endmenuAccess

                @menuAccess(['shift', 'jadwal', 'jadwal-user'])
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator {{ $open['jadwal'] ? '' : 'collapsed' }}" href="#navJadwal" role="button" data-bs-toggle="collapse" aria-expanded="{{ $open['jadwal'] ? 'true' : 'false' }}" aria-controls="navJadwal">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-calendar"></i></span><span class="nav-link-text ps-1">Jadwal</span></div>
                    </a>
                    <ul class="nav collapse {{ $open['jadwal'] ? 'show' : '' }}" id="navJadwal">
                        @menuAccess('shift')
                        <li class="nav-item"><a class="nav-link" href="{{ route('shift.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-business-time"></i></span><span class="nav-link-text ps-1">Shift</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('jadwal')
                        <li class="nav-item"><a class="nav-link" href="{{ route('jadwal.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-calendar-alt"></i></span><span class="nav-link-text ps-1">Jadwal</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('jadwal-user')
                        <li class="nav-item"><a class="nav-link" href="{{ route('jadwal-user.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-calendar-check"></i></span><span class="nav-link-text ps-1">Jadwal &amp; Absen</span></div></a></li>
                        @endmenuAccess
                    </ul>
                </li>
                @endmenuAccess

                @menuAccess(['modal', 'transaksi', 'transaksi-jual'])
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator {{ $open['transaksi'] ? '' : 'collapsed' }}" href="#navTransaksi" role="button" data-bs-toggle="collapse" aria-expanded="{{ $open['transaksi'] ? 'true' : 'false' }}" aria-controls="navTransaksi">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-exchange-alt"></i></span><span class="nav-link-text ps-1">Transaction</span></div>
                    </a>
                    <ul class="nav collapse {{ $open['transaksi'] ? 'show' : '' }}" id="navTransaksi">
                        @menuAccess('modal')
                        <li class="nav-item"><a class="nav-link" href="{{ route('modal.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-credit-card"></i></span><span class="nav-link-text ps-1">Modal</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('transaksi')
                        <li class="nav-item"><a class="nav-link" href="{{ route('transaksi.create') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-cash-register"></i></span><span class="nav-link-text ps-1">Transaksi</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('transaksi-jual')
                        <li class="nav-item"><a class="nav-link" href="{{ route('transaksi-jual.create') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-cash-register"></i></span><span class="nav-link-text ps-1">Jual Valas</span></div></a></li>
                        @endmenuAccess
                    </ul>
                </li>
                @endmenuAccess

                @menuAccess(['rekapan-hari-ini', 'seluruh-transaksi', 'jurnal-bulanan', 'jurnal-debit-kredit', 'laporan-harian', 'laporan-pegawai', 'laporan-saya', 'laporan-rekap-cabang'])
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator {{ $open['pelaporan'] ? '' : 'collapsed' }}" href="#navPelaporan" role="button" data-bs-toggle="collapse" aria-expanded="{{ $open['pelaporan'] ? 'true' : 'false' }}" aria-controls="navPelaporan">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-file-alt"></i></span><span class="nav-link-text ps-1">Pelaporan</span></div>
                    </a>
                    <ul class="nav collapse {{ $open['pelaporan'] ? 'show' : '' }}" id="navPelaporan">
                        @menuAccess('rekapan-hari-ini')
                        <li class="nav-item"><a class="nav-link" href="{{ route('transaksi.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-sync-alt"></i></span><span class="nav-link-text ps-1">Rekapan Hari Ini</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('seluruh-transaksi')
                        <li class="nav-item"><a class="nav-link" href="{{ route('jurnal-harian.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-clipboard-list"></i></span><span class="nav-link-text ps-1">Seluruh Transaksi</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('jurnal-bulanan')
                        <li class="nav-item"><a class="nav-link" href="{{ route('jurnal-bulanan.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-book"></i></span><span class="nav-link-text ps-1">Jurnal Bulanan</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('jurnal-debit-kredit')
                        <li class="nav-item"><a class="nav-link" href="{{ route('jurnal-debit-kredit.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-book"></i></span><span class="nav-link-text ps-1">Jurnal Debit Kredit</span></div></a></li>
                        @endmenuAccess
                        @menuAccess(['laporan-harian', 'laporan-pegawai', 'laporan-saya'])
                        <li class="nav-item">
                            <a class="nav-link dropdown-indicator {{ $open['absen'] ? '' : 'collapsed' }}" href="#laporanAbsen" role="button" data-bs-toggle="collapse" aria-expanded="{{ $open['absen'] ? 'true' : 'false' }}" aria-controls="laporanAbsen">
                                <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-calendar-alt"></i></span><span class="nav-link-text ps-1">Laporan Absensi</span></div>
                            </a>
                            <ul class="nav collapse {{ $open['absen'] ? 'show' : '' }}" id="laporanAbsen">
                                @menuAccess('laporan-harian')
                                <li class="nav-item mt-1"><a class="nav-link" href="{{ route('report-jadwal-harian') }}"><div class="d-flex align-items-center"><span class="nav-link-text ps-1">Laporan Harian</span></div></a></li>
                                @endmenuAccess
                                @menuAccess('laporan-pegawai')
                                <li class="nav-item mt-1"><a class="nav-link" href="{{ route('getUserReport') }}"><div class="d-flex align-items-center"><span class="nav-link-text ps-1">Laporan Pegawai</span></div></a></li>
                                @endmenuAccess
                                @menuAccess('laporan-saya')
                                <li class="nav-item mt-1"><a class="nav-link" href="{{ route('jadwal-laporan.index') }}"><div class="d-flex align-items-center"><span class="nav-link-text ps-1">Laporan Saya</span></div></a></li>
                                @endmenuAccess
                            </ul>
                        </li>
                        @endmenuAccess
                        @menuAccess('laporan-rekap-cabang')
                        <li class="nav-item"><a class="nav-link" href="{{ route('laporan-rekap-cabang.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-file-excel"></i></span><span class="nav-link-text ps-1">Rekapitulasi Cabang (Excel)</span></div></a></li>
                        @endmenuAccess
                    </ul>
                </li>
                @endmenuAccess

                @menuAccess(['approval-modal', 'log-transaksi'])
                <li class="nav-item">
                    <a class="nav-link dropdown-indicator {{ $open['log'] ? '' : 'collapsed' }}" href="#navLog" role="button" data-bs-toggle="collapse" aria-expanded="{{ $open['log'] ? 'true' : 'false' }}" aria-controls="navLog">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-clipboard-check"></i></span><span class="nav-link-text ps-1">Log dan Approval</span></div>
                    </a>
                    <ul class="nav collapse {{ $open['log'] ? 'show' : '' }}" id="navLog">
                        @menuAccess('approval-modal')
                        <li class="nav-item"><a class="nav-link" href="{{ route('approval-modal.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-check-circle"></i></span><span class="nav-link-text ps-1">Approval Modal</span></div></a></li>
                        @endmenuAccess
                        @menuAccess('log-transaksi')
                        <li class="nav-item"><a class="nav-link" href="{{ route('log-edit.index') }}"><div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-table"></i></span><span class="nav-link-text ps-1">Log Transaksi</span></div></a></li>
                        @endmenuAccess
                    </ul>
                </li>
                @endmenuAccess

                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2"><div class="col-auto navbar-vertical-label">Sesi Login</div><div class="col ps-0"><hr class="mb-0 navbar-vertical-divider"></div></div>
                    <h6 class="mb-0">Role Anda<span class="text-primary"> {{ Auth::user()->role }}</span></h6>
                </li>
            </ul>
        </div>
    </div>
</nav>
