<?php

use App\Http\Controllers\ApprovalModalController;
use App\Http\Controllers\MasterCurrencyController;
use App\Http\Controllers\MasterPegawaiController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\TransaksiController;
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



Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('owner')->middleware(['Owner'])->group(function(){
        Route::resource('master-pegawai', MasterPegawaiController::class);
        Route::post('/delete-pegawai', [\App\Http\Controllers\MasterPegawaiController::class, 'hapus'])->name('master-pegawai-delete');

        Route::get('/master-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'index'])->name('master-currency');
        Route::post('/tambah-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'store'])->name('master-currency-store');
        Route::post('/delete-currency', [\App\Http\Controllers\MasterCurrencyController::class, 'hapus'])->name('master-currency-delete');
    });

    // TRANSAKSI
    Route::resource('transaksi', TransaksiController::class);
    Route::post('/delete-transaksi', [\App\Http\Controllers\TransaksiController::class, 'hapus'])->name('transaksi-delete');

    // MODAL
    Route::resource('modal', ModalController::class);
    Route::post('/delete-modal', [\App\Http\Controllers\ModalController::class, 'hapus'])->name('modal-delete');
    Route::post('/transfer-modal', [\App\Http\Controllers\ModalController::class, 'transfer'])->name('modal-transfer');
  
    // APPROVAL
    Route::resource('approval-modal', ApprovalModalController::class)->middleware(['Owner']);

});
