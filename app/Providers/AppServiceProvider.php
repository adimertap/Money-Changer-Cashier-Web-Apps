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
    }
}
