<?php

return [
    // Used only to seed the roles table during migration; new roles are stored in the database.
    'roles' => ['Owner', 'Pegawai', 'Admin Cabang', 'Kasir'],
    'menus' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'group' => 'Dashboard'],
        ['key' => 'web-exchange', 'label' => 'Web Exchange', 'group' => 'Dashboard'],
        ['key' => 'master-cabang', 'label' => 'Cabang', 'group' => 'Master Data'],
        ['key' => 'master-pegawai', 'label' => 'Pegawai', 'group' => 'Master Data'],
        ['key' => 'role-hak-akses', 'label' => 'Role & Hak Akses', 'group' => 'Master Data'],
        ['key' => 'master-currency', 'label' => 'Currency', 'group' => 'Master Data'],
        ['key' => 'master-customer', 'label' => 'Customer', 'group' => 'Master Data'],
        ['key' => 'master-terduga', 'label' => 'Terduga', 'group' => 'Master Data'],
        ['key' => 'master-threshold', 'label' => 'Batas Atas Transaksi', 'group' => 'Master Data'],
        ['key' => 'shift', 'label' => 'Shift', 'group' => 'Jadwal'],
        ['key' => 'jadwal', 'label' => 'Jadwal', 'group' => 'Jadwal'],
        ['key' => 'jadwal-user', 'label' => 'Jadwal & Absen', 'group' => 'Jadwal'],
        ['key' => 'modal', 'label' => 'Modal', 'group' => 'Transaction'],
        ['key' => 'transaksi', 'label' => 'Transaksi', 'group' => 'Transaction'],
        ['key' => 'transaksi-jual', 'label' => 'Jual Valas', 'group' => 'Transaction'],
        ['key' => 'rekapan-hari-ini', 'label' => 'Rekapan Hari Ini', 'group' => 'Pelaporan'],
        ['key' => 'seluruh-transaksi', 'label' => 'Seluruh Transaksi', 'group' => 'Pelaporan'],
        ['key' => 'jurnal-bulanan', 'label' => 'Jurnal Bulanan', 'group' => 'Pelaporan'],
        ['key' => 'jurnal-debit-kredit', 'label' => 'Jurnal Debit Kredit', 'group' => 'Pelaporan'],
        ['key' => 'laporan-absensi', 'label' => 'Laporan Absensi', 'group' => 'Pelaporan'],
        ['key' => 'laporan-harian', 'label' => 'Laporan Harian', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-pegawai', 'label' => 'Laporan Pegawai', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-saya', 'label' => 'Laporan Saya', 'group' => 'Laporan Absensi'],
        ['key' => 'laporan-rekap-cabang', 'label' => 'Rekapitulasi Cabang (Excel)', 'group' => 'Pelaporan'],
        ['key' => 'approval-modal', 'label' => 'Approval Modal', 'group' => 'Log dan Approval'],
        ['key' => 'log-transaksi', 'label' => 'Log Transaksi', 'group' => 'Log dan Approval'],
    ],
    'defaults' => [
        'Pegawai' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
        'Admin Cabang' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
        'Kasir' => [
            'dashboard', 'jadwal-user', 'modal', 'transaksi', 'transaksi-jual',
            'rekapan-hari-ini', 'jurnal-debit-kredit', 'laporan-saya', 'laporan-rekap-cabang',
        ],
    ],
];
