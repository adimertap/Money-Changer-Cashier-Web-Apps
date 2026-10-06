<?php

use App\Http\Controllers\Absensi\JadwalKerjaController;
use App\Http\Controllers\Absensi\JadwalUserController;
use App\Http\Controllers\Absensi\LaporanAbsensiController;
use App\Http\Controllers\Absensi\MasterShiftController;
use App\Http\Controllers\ApprovalModalController;
use App\Http\Controllers\CurrencyDetailController;
use App\Http\Controllers\JurnalBulananController;
use App\Http\Controllers\JurnalHarianController;
use App\Http\Controllers\JurnalKreditDebitController;
use App\Http\Controllers\LkubController;
use App\Http\Controllers\SummaryValasController;
use App\Http\Controllers\LogEditController;
use App\Http\Controllers\LaporanRekapCabangController;
use App\Http\Controllers\MasterCurrencyController;
use App\Http\Controllers\MasterPegawaiController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiJualController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/change-password/v2', [\App\Http\Controllers\DashboardController::class, 'change_password_v2'])->name('change_password_v2');
Route::post('/change-password/v2', [\App\Http\Controllers\DashboardController::class, 'change_password_v2_post'])->name('change_password_v2_post');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/change-password', [\App\Http\Controllers\DashboardController::class, 'change_password'])->name('change_password');
    Route::post('/switch-cabang', [\App\Http\Controllers\DashboardController::class, 'switch_cabang'])->name('switch-cabang');



    Route::resource('jadwal', JadwalKerjaController::class);
    Route::resource('jadwal-user', JadwalUserController::class);
    Route::resource('jadwal-laporan', LaporanAbsensiController::class);
    Route::get('/report/jadwal/harian', [App\Http\Controllers\Absensi\LaporanAbsensiController::class, 'today'])->name('report-jadwal-harian');

    Route::get('/api/jadwal-kerja', [App\Http\Controllers\Absensi\JadwalKerjaController::class, 'getJadwalKerja']);
    Route::get('/api/cabang/{cabangId}/employees', [App\Http\Controllers\Absensi\JadwalKerjaController::class, 'employeesByCabang']);
    Route::post('/api/jadwal-kerja/download-format', [App\Http\Controllers\Absensi\JadwalKerjaController::class, 'jadwalDownloadFormat'])->name('jadwal.download-format');
    Route::post('/api/jadwal-kerja/upload-excel', [App\Http\Controllers\Absensi\JadwalKerjaController::class, 'jadwalUploadExcel'])->name('jadwal.upload-excel');

    Route::get('/api/get-event/{id}', [App\Http\Controllers\Absensi\JadwalKerjaController::class, 'getEventDetails']);
    Route::get('/api/get-user', [App\Http\Controllers\Absensi\LaporanAbsensiController::class, 'getUser'])->name('getUserReport');

    Route::group(['middleware' => ['jadwal.checking']], function () {
        Route::prefix('owner')->middleware(['Owner'])->group(function () {
            // MASTER DATA
            Route::resource('master-pegawai', MasterPegawaiController::class);
            Route::get('role-hak-akses', [\App\Http\Controllers\RoleHakAksesController::class, 'index'])->name('role-hak-akses.index');
            Route::post('role-hak-akses', [\App\Http\Controllers\RoleHakAksesController::class, 'store'])->name('role-hak-akses.store');
            Route::put('role-hak-akses/{role}', [\App\Http\Controllers\RoleHakAksesController::class, 'update'])->name('role-hak-akses.update');
            Route::delete('role-hak-akses/{role}', [\App\Http\Controllers\RoleHakAksesController::class, 'destroy'])->name('role-hak-akses.destroy');
            Route::get('/master-pegawa/reset/{id}', [\App\Http\Controllers\MasterPegawaiController::class, 'reset_password'])->name('master-pegawai-reset');
            Route::put('/master-pegawa/reset/{id}', [\App\Http\Controllers\MasterPegawaiController::class, 'reset_password_post'])->name('master-pegawai-reset-post');


            Route::resource('shift', MasterShiftController::class);
            Route::resource('master-cabang', \App\Http\Controllers\MasterCabangController::class)->except(['create', 'edit']);
            Route::patch('master-cabang/{id}/status', [\App\Http\Controllers\MasterCabangController::class, 'status'])->name('master-cabang.status');
            Route::resource('master-cabang-user', \App\Http\Controllers\MasterCabangUserController::class)->except(['create', 'edit']);
            Route::get('master-customer/search', [\App\Http\Controllers\MasterCustomerController::class, 'search'])->name('master-customer.search');
            Route::post('master-customer/screen', [\App\Http\Controllers\MasterCustomerController::class, 'screen'])->name('master-customer.screen');
            Route::resource('master-customer', \App\Http\Controllers\MasterCustomerController::class)->except(['create', 'edit']);
            Route::patch('master-customer/{id}/status', [\App\Http\Controllers\MasterCustomerController::class, 'status'])->name('master-customer.status');
            Route::get('master-terduga', [\App\Http\Controllers\MasterTerdugaController::class, 'index'])->name('master-terduga.index');
            Route::post('master-terduga', [\App\Http\Controllers\MasterTerdugaController::class, 'store'])->name('master-terduga.store');
            Route::post('master-terduga/upload', [\App\Http\Controllers\MasterTerdugaController::class, 'upload'])->name('master-terduga.upload');
            Route::get('master-terduga/{id}/export/excel', [\App\Http\Controllers\MasterTerdugaController::class, 'exportExcel'])->name('master-terduga.export.excel');
            Route::get('master-terduga/{id}/export/pdf', [\App\Http\Controllers\MasterTerdugaController::class, 'exportPdf'])->name('master-terduga.export.pdf');
            Route::get('master-terduga/{id}', [\App\Http\Controllers\MasterTerdugaController::class, 'show'])->name('master-terduga.show');
            Route::get('master-terduga/{id}/edit', [\App\Http\Controllers\MasterTerdugaController::class, 'edit'])->name('master-terduga.edit');
            Route::put('master-terduga/{id}', [\App\Http\Controllers\MasterTerdugaController::class, 'update'])->name('master-terduga.update');
            Route::delete('master-terduga/{id}', [\App\Http\Controllers\MasterTerdugaController::class, 'destroy'])->name('master-terduga.destroy');
            Route::get('master-terduga/{headerId}/detail/create', [\App\Http\Controllers\MasterTerdugaController::class, 'createDetail'])->name('master-terduga.detail.create');
            Route::post('master-terduga/{headerId}/detail', [\App\Http\Controllers\MasterTerdugaController::class, 'storeDetail'])->name('master-terduga.detail.store');
            Route::get('master-terduga/{headerId}/detail/{id}/edit', [\App\Http\Controllers\MasterTerdugaController::class, 'editDetail'])->name('master-terduga.detail.edit');
            Route::put('master-terduga/{headerId}/detail/{id}', [\App\Http\Controllers\MasterTerdugaController::class, 'updateDetail'])->name('master-terduga.detail.update');
            Route::delete('master-terduga/{headerId}/detail/{id}', [\App\Http\Controllers\MasterTerdugaController::class, 'destroyDetail'])->name('master-terduga.detail.destroy');
            Route::resource('master-threshold', \App\Http\Controllers\MasterThresholdController::class)->except(['create', 'edit']);
            Route::patch('master-threshold/{id}/status', [\App\Http\Controllers\MasterThresholdController::class, 'status'])->name('master-threshold.status');
            Route::resource('master-limit-transaksi', \App\Http\Controllers\MasterLimitTransaksiController::class)->except(['create', 'show', 'edit']);
            Route::patch('master-limit-transaksi/{id}/status', [\App\Http\Controllers\MasterLimitTransaksiController::class, 'status'])->name('master-limit-transaksi.status');
            Route::post('/delete-pegawai', [\App\Http\Controllers\MasterPegawaiController::class, 'hapus'])->name('master-pegawai-delete');
            Route::get('/master-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'index'])->name('master-currency');
            Route::post('/tambah-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'store'])->name('master-currency-store');
            Route::post('/delete-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'hapus']);
            Route::post('/update-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'updatedata']);
            Route::post('/update-nilai-kurs', [\App\Http\Controllers\MasterCurrencyController::class, 'updatekurs']);

            // JURNAL
            Route::resource('jurnal-harian', JurnalHarianController::class);
            Route::get('/jurnal-jual', [\App\Http\Controllers\JurnalHarianController::class, 'jual'])->name('jurnal-harian-jual');
            Route::get('/download-dokumen/jual', [\App\Http\Controllers\JurnalHarianController::class, 'Export_dokumen_jual'])->name('export-dokumen-jual');

            Route::resource('jurnal-bulanan', JurnalBulananController::class);
            Route::get('/jurnal-bulanan/detail/{id}', [\App\Http\Controllers\JurnalBulananController::class, 'DetailTransaksi'])->name('bulanan-transaksi');

            // EXCEL DAN PDF
            Route::get('/download-dokumen', [\App\Http\Controllers\JurnalHarianController::class, 'Export_dokumen'])->name('export-dokumen');
        });
        Route::resource('jurnal-debit-kredit', JurnalKreditDebitController::class);
        Route::post('/delete-jurnal', [\App\Http\Controllers\JurnalKreditDebitController::class, 'hapus'])->name('jurnal-delete');

        // LAPORAN REKAP CABANG
        Route::get('/laporan-rekap-cabang', [LaporanRekapCabangController::class, 'index'])->name('laporan-rekap-cabang.index');
        Route::get('/laporan-rekap-cabang/download', [LaporanRekapCabangController::class, 'download'])->name('laporan-rekap-cabang.download');
        Route::get('/laporan-lkub', [LkubController::class, 'index'])->name('laporan-lkub.index');
        Route::get('/laporan-lkub/download', [LkubController::class, 'download'])->name('laporan-lkub.download');
        Route::get('/summary-valas', [SummaryValasController::class, 'index'])->name('summary-valas.index');
        Route::get('/summary-valas/download', [SummaryValasController::class, 'download'])->name('summary-valas.download');

        // CUSTOMER AJAX UNTUK TRANSAKSI
        Route::get('/api/customer/search', [\App\Http\Controllers\MasterCustomerController::class, 'search'])->name('api.customer.search');
        Route::post('/api/customer/screen', [\App\Http\Controllers\MasterCustomerController::class, 'screen'])->name('api.customer.screen');
        Route::post('/api/customer', [\App\Http\Controllers\MasterCustomerController::class, 'store'])->name('api.customer.store');

        // TRANSAKSI
        Route::post('/api/transaksi/passport-threshold', [TransaksiController::class, 'passportThreshold'])->name('api.transaksi.passport-threshold');
        Route::get('/api/transaksi/next-passport', [TransaksiController::class, 'nextIncrementalPassportApi'])->name('api.transaksi.next-passport');
        Route::post('/api/transaksi/validate-terduga', [TransaksiController::class, 'validateTerduga'])->name('api.transaksi.validate-terduga');
        Route::resource('transaksi', TransaksiController::class);
        Route::get('/transaksi/{id}/dokumen', [\App\Http\Controllers\TransaksiController::class, 'downloadDokumen'])->name('transaksi.dokumen');
        Route::get('transaksi/getkurs/{id_currency}', [\App\Http\Controllers\TransaksiController::class, 'getkurs']);
        Route::get('/edit/getkurs/{id_currency}', [\App\Http\Controllers\TransaksiController::class, 'getkursedit']);
        Route::post('/delete-transaksi', [\App\Http\Controllers\TransaksiController::class, 'hapus'])->name('transaksi-delete');

        //JUAL
        Route::resource('transaksi-jual', TransaksiJualController::class);
        Route::get('transaksi-jual/getkursJumlah/{id_currency}', [\App\Http\Controllers\TransaksiJualController::class, 'getkursJumlah']);

        // MODAL
        Route::resource('modal', ModalController::class);
        Route::post('/delete-modal', [\App\Http\Controllers\ModalController::class, 'hapus'])->name('modal-delete');
        Route::post('/transfer-modal', [\App\Http\Controllers\ModalController::class, 'transfer'])->name('modal-transfer');
        Route::post('/tambah-modal', [\App\Http\Controllers\ModalController::class, 'tambah']);

        // LOG EDIT
        Route::resource('log-edit', LogEditController::class);
        Route::get('log-edit/getdetail/{id}', [\App\Http\Controllers\LogEditController::class, 'getdetail']);
        Route::get('/filter-log', [\App\Http\Controllers\LogEditController::class, 'filterLog'])->name('filterLog');

        // APPROVAL
        Route::resource('approval-modal', ApprovalModalController::class)->middleware(['Owner']);

        // CETAK DOWNLOAD
        Route::get('/cetak/{id}', [\App\Http\Controllers\CetakController::class, 'cetak'])->name('cetak');
        Route::get('/exportexcel/{today}', [\App\Http\Controllers\CetakController::class, 'exportexcel'])->name('exportexcel');

        Route::get('/download-harian', [\App\Http\Controllers\TransaksiController::class, 'Export_dokumen'])->name('export-dokumen-harian');
        Route::get('/download-harian/jual', [\App\Http\Controllers\TransaksiController::class, 'Export_dokumen_jual'])->name('export-dokumen-harian-jual');

    });
});
