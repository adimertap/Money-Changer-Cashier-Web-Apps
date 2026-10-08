<?php

namespace App\Providers;

use App\Models\MasterCabang;
use App\Services\MenuAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('menuAccess', function ($menuOrMenus) {
            return app(MenuAccessService::class)->allows($menuOrMenus);
        });

        // pilihan cabang di header: Owner semua cabang aktif, Pegawai cabang miliknya
        View::composer('layouts.header', function ($view) {
            $options = Auth::check() && Auth::user()->role === 'Owner'
                ? MasterCabang::where('is_active', 1)->orderBy('cabang_name')->get(['cabang_id', 'cabang_name'])->toArray()
                : session('cabangs', []);
            $view->with('cabangOptions', $options);
        });

        // Pastikan kolom Document / CDD & Lampiran tersedia di tb_master_customer dan tb_transaksi
        try {
            $documentCols = [
                'npwp' => 50, 'domicile' => 150, 'income' => 100, 'job' => 100,
                'company' => 150, 'company_form' => 150, 'position' => 100,
                'business_sector' => 100, 'transaction_purpose' => 150,
                'relationship' => 100, 'source_of_funds' => 100,
                'supporting_document_file' => 255,
            ];

            if (\Illuminate\Support\Facades\Schema::hasTable('tb_master_customer')) {
                \Illuminate\Support\Facades\Schema::table('tb_master_customer', function (\Illuminate\Database\Schema\Blueprint $table) use ($documentCols) {
                    foreach ($documentCols as $c => $l) {
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('tb_master_customer', $c)) {
                            $table->string($c, $l)->nullable();
                        }
                    }
                });
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('tb_transaksi')) {
                \Illuminate\Support\Facades\Schema::table('tb_transaksi', function (\Illuminate\Database\Schema\Blueprint $table) use ($documentCols) {
                    foreach ($documentCols as $c => $l) {
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('tb_transaksi', $c)) {
                            $table->string($c, $l)->nullable();
                        }
                    }
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if DB connection issue
        }
    }
}
