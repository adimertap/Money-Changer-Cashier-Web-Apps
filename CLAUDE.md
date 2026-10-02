# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Aturan operasi

- **Dilarang mengeksekusi perintah terminal apa pun**, termasuk PowerShell, Bash/Git Bash, `cmd`, Composer, npm, dan Artisan. Gunakan tool baca/search/edit file saja.
- Daftar command di bawah hanya dokumentasi untuk developer yang menjalankan project secara manual; jangan dieksekusi oleh Claude Code tanpa instruksi eksplisit yang mencabut aturan di atas.
- Hindari membaca atau mengindeks `vendor/`, `node_modules/`, `public/`, `storage/`, dan file `db_kasir_*.sql` kecuali diminta. Fokus default pada `app/`, `routes/`, `resources/views/`, `config/`, `tests/`, dan file konfigurasi root.
- Jangan menganggap perubahan working tree sebagai pekerjaan Claude; cek konteks perubahan sebelum mengedit file yang sudah berubah.

## Stack dan command referensi

Laravel 8 pada PHP `^7.3|^8.0`, Blade, Bootstrap 5 melalui Laravel Mix, MySQL, dan autentikasi `laravel/ui`. Integrasi utama: `maatwebsite/excel`, Dompdf, KNP Snappy, Cloudinary, dan SweetAlert.

Command yang tersedia menurut konfigurasi repository (referensi manual saja):

```text
composer install
npm install
php artisan serve
npm run dev                 # Mix development build
npm run watch               # Mix watch
npm run production          # Mix production build
php artisan test
vendor/bin/phpunit
vendor/bin/phpunit --filter NamaTest
php artisan route:list
php artisan migrate
```

Tidak ada script lint khusus di `composer.json` atau `package.json`. Untuk pemeriksaan PHP manual, developer dapat memakai `php -l path/to/file.php`; validasi aplikasi tetap bergantung pada test Laravel/PHPUnit dan pemeriksaan route secara manual.

Test suite berada di `tests/Unit` dan `tests/Feature`, dikonfigurasi oleh `phpunit.xml`. Konfigurasi testing default memakai array untuk cache/session/mail/queue; konfigurasi SQLite in-memory masih dikomentari, sehingga test yang menyentuh database memerlukan setup database testing tersendiri.

## Gambaran arsitektur

- `routes/web.php` adalah peta utama aplikasi. Route web memakai middleware `web` dari `RouteServiceProvider`; sebagian besar halaman berada di dalam `auth`, kemudian pemeriksaan jadwal, dan beberapa route master data berada di prefix `owner` + middleware `Owner`.
- Controller ada di `app/Http/Controllers`. Controller menangani alur transaksi, modal, jurnal, absensi, master data, laporan, export/cetak, dan autentikasi. Operasi multi-tabel memakai `DB::transaction` atau begin/commit/rollback manual.
- Model Eloquent ada di `app/Models`, tetapi sebagian besar model bisnis menunjuk ke tabel legacy bernama `tb_*` dengan primary key dan relasi eksplisit. Struktur database produksi terutama berasal dari dump `db_kasir_*.sql`; migration yang ada hanya migration standar Laravel.
- Transaksi beli/jual menggunakan `Transaksi` (`tb_transaksi`) dan `DetailTransaksi` (`tb_detail_transaksi`). `Jurnal` (`tb_jurnal`) mencatat jurnal transaksi/modal, sedangkan `ModalTransaksi` (`tb_modal_transaksi`) menangani modal kasir dan approval. Relasi utama ada di model, dan banyak relasi memakai `withTrashed()` karena `Transaksi` dan `User` menggunakan soft delete.
- Pemisahan cabang adalah concern lintas model: `app/Models/Concerns/BelongsToCabang.php` menambahkan global scope berdasarkan `session('cabang_aktif')`, mengisi `cabang_id` saat create, dan menyediakan relasi ke `MasterCabang`. `Owner` tanpa cabang aktif melihat semua cabang; pegawai tanpa cabang aktif tidak melihat data cabang. Gunakan `withoutGlobalScope('cabang')` hanya untuk kebutuhan lintas cabang yang jelas.
- Setelah login, `LoginController` mengisi session `cabangs` dan `cabang_aktif`. Owner memakai semua cabang (`null`), sedangkan Pegawai memakai cabang aktif pertama. Header memilih cabang melalui `DashboardController` dan view composer di `AppServiceProvider`.
- Hak akses menu dipisahkan dari role route. `MenuAccessService` memuat key menu ke session; Owner mendapat semua `AccessMenu`, sedangkan role lain mendapat `RoleMenu`. Blade directive `@menuAccess` didaftarkan di `AppServiceProvider` dan dipakai untuk menyembunyikan menu.
- Middleware role berada di `app/Http/Middleware`: `Owner` hanya mengizinkan role Owner, `Pegawai` hanya role Pegawai, dan `jadwal.checking` memastikan pegawai memiliki jadwal hari ini serta sudah absen masuk sebelum mengakses route terproteksi.
- View Blade berada di `resources/views`. Layout bersama ada di `resources/views/layouts`; modul utama berada di `pages/`, absensi di `absensi/`, output cetak di `print/`, output export di `export/`, dan template email di root `resources/views/SendEmail*.blade.php`.
- Asset frontend hanya dikompilasi dari `resources/js/app.js` dan `resources/sass/app.scss` menuju `public/js` dan `public/css` melalui `webpack.mix.js`. Jangan menambahkan pipeline frontend baru tanpa kebutuhan nyata.
- Export Excel berada di `app/Exports`, sedangkan PDF/cetak ditangani controller dan Blade export. Perubahan nama kolom legacy harus ditelusuri ke model, controller, route, view, dan export terkait.

## Area domain utama

| Area | Entry point | Penyimpanan/view utama |
|---|---|---|
| Transaksi beli/jual | `TransaksiController`, `TransaksiJualController` | `tb_transaksi`, `tb_detail_transaksi`, `pages/transaksi*` |
| Modal dan approval | `ModalController`, `ApprovalModalController` | `tb_modal_transaksi`, `pages/modal` |
| Jurnal | `JurnalHarianController`, `JurnalBulananController`, `JurnalKreditDebitController` | `tb_jurnal`, `pages/jurnal` |
| Absensi/jadwal/shift | `app/Http/Controllers/Absensi/*` | `tb_jadwal_kerja`, `tb_master_shift`, `resources/views/absensi` |
| Master data | `MasterCurrency`, `MasterPegawai`, `MasterCabang*`, `MasterCustomer`, `MasterTerduga`, `MasterThreshold` | tabel `tb_*` terkait dan `pages/master*` |
| Audit/laporan | `LogEditController`, `LaporanRekapCabangController`, `CetakController` | tabel log, `app/Exports`, `pages/log`, `pages/laporan`, `print`, `export` |

## File penting saat menelusuri perubahan

- Route dan middleware: `routes/web.php`, `app/Http/Kernel.php`, `app/Providers/RouteServiceProvider.php`.
- Session cabang dan menu: `app/Http/Controllers/Auth/LoginController.php`, `app/Http/Controllers/DashboardController.php`, `app/Providers/AppServiceProvider.php`, `app/Services/MenuAccessService.php`.
- Isolasi data cabang: `app/Models/Concerns/BelongsToCabang.php` dan model yang memakai trait tersebut.
- Kontrak dependency/build: `composer.json`, `package.json`, `webpack.mix.js`, `phpunit.xml`.
- Dokumen fitur tingkat tinggi: `README.md`.
