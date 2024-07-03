<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
    <div class="d-flex align-items-center">

        <div class="toggle-icon-wrapper">

        </div>
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <div class="d-flex align-items-center py-3"><span class="font-sans-serif">Kasir</span></div>
        </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                @if (Auth::user()->role == 'Owner')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon">
                                    <i class="fas fa-home"></i>
                                </span>
                                <span class="nav-link-text ps-1">Dashboard</span>
                            </div>
                        </a>
                        <a class="nav-link" href="{{ url('https://www.rate.ptriastavalasindo.com') }}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon">
                                    <i class="fas fa-server"></i>
                                </span>
                                <span class="nav-link-text ps-1">Web Exchange</span>
                            </div>
                        </a>
                    </li>

                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Master Data</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    <a class="nav-link" href="{{ route('master-pegawai.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-user-friends"></i>
                            </span>
                            <span class="nav-link-text ps-1">Pegawai</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('master-currency') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <span class="nav-link-text ps-1">Currency</span>
                        </div>
                    </a>
                @endif

                @if(Auth::user()->role != 'Owner')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-home"></i>
                            </span>
                            <span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Jadwal</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    @if (Auth::user()->role == 'Owner')
                    <a class="nav-link" href="{{ route('shift.index') }}" role="button" data-bs-toggle=""
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-business-time"></i>
                            </span>
                            <span class="nav-link-text ps-1">Shift</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('jadwal.index') }}" role="button" data-bs-toggle=""
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <span class="nav-link-text ps-1">Jadwal</span>
                        </div>
                    </a>
                    @endif
                    <a class="nav-link" href="{{ route('jadwal-user.index') }}" role="button" data-bs-toggle=""
                        aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <span class="nav-link-text ps-1">Jadwal & Absen</span>
                        </div>
                    </a>


                </li>

                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Transaction</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    <a class="nav-link" href="{{ route('modal.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <span class="nav-link-text ps-1">Modal</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('transaksi.create') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-cash-register"></i>
                            </span>
                            <span class="nav-link-text ps-1">Transaksi</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('transaksi-jual.create') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-cash-register"></i>
                            </span>
                            <span class="nav-link-text ps-1">Jual Valas</span>
                        </div>
                    </a>
                </li>

                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Pelaporan</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    <a class="nav-link" href="{{ route('transaksi.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-sync-alt"></i>
                            </span>
                            <span class="nav-link-text ps-1">Rekapan Hari Ini</span>
                        </div>
                    </a>
                    @if (Auth::user()->role == 'Owner')
                    <a class="nav-link" href="{{ route('jurnal-harian.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            <span class="nav-link-text ps-1">Seluruh Transaksi</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('jurnal-bulanan.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span class="nav-link-text ps-1">Jurnal Bulanan</span>
                        </div>
                    </a>
                    @endif
                    <a class="nav-link" href="{{ route('jurnal-debit-kredit.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span class="nav-link-text ps-1">Jurnal Debit Kredit</span>
                        </div>
                    </a>

                    <a class="nav-link dropdown-indicator collapsed" href="#laporanAbsen" role="button"
                        data-bs-toggle="collapse" aria-expanded="true" aria-controls="laporanAbsen">
                        <div class="d-flex align-items-center"><span class="nav-link-icon">
                                <i class="fas fa-calendar-alt"></i></span><span class="nav-link-text ps-1">Laporan
                                Absensi</span></div>
                    </a>
                    @php
                    $laporan_absensi = ['getUserReport', 'jadwal-laporan.index','report-jadwal-harian' ];
                    @endphp
                    <ul class="nav collapse {{ (in_array(request()->route()->getName(), $laporan_absensi)) ? 'show' : '' }}"
                        id="laporanAbsen" style="">
                        @if(Auth::user()->role == 'Owner')
                        <li class="nav-item mt-1">
                            <a class="nav-link" href="{{ route('report-jadwal-harian') }}" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-text ps-1">
                                        Laporan Harian</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item mt-1">
                            <a class="nav-link" href="{{ route('getUserReport') }}" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-text ps-1">
                                        Laporan Pegawai</span>
                                </div>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item mt-1">
                            <a class="nav-link" href="{{ route('jadwal-laporan.index') }}" data-bs-toggle=""
                                aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-text ps-1">
                                        Laporan Saya</span>
                                </div>
                            </a>
                        </li>
                    </ul>


                </li>
                @if (Auth::user()->role == 'Owner')
                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Log dan Approval</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    <a class="nav-link" href="{{ route('approval-modal.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <span class="nav-link-text ps-1">Approval Modal</span>
                        </div>
                    </a>
                    <a class="nav-link" href="{{ route('log-edit.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="nav-link-icon">
                                <i class="fas fa-table"></i>
                            </span>
                            <span class="nav-link-text ps-1">Log Transaksi</span>
                        </div>
                    </a>
                </li>
                @endif


                <li class="nav-item">
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Sesi Login</div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider">
                        </div>
                    </div>
                    <h6 class="mb-0">Role Anda<span class="text-primary"> {{ Auth::user()->role }}</span></h6>
                </li>

            </ul>
            {{-- <div class="settings mb-3 mt-5">
                <div class="card alert p-0 shadow-none" role="alert">
                    <div class="btn-close-falcon-container">
                        <div class="btn-close-falcon" aria-label="Close" data-bs-dismiss="alert"></div>
                    </div>
                    <div class="card-body text-center">
                        <p class="fs--2 mt-2">Hari ini anda belum menginputkan Modal<br>Input Cepat disini!</p>
                        <div class="d-grid"><a class="btn btn-sm btn-purchase">Input Modal</a></div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</nav>
