<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Gunakan Bootstrap untuk pagination
        Paginator::useBootstrap();

        // Set lokal tanggal dan jam ke Bahasa Indonesia
        setlocale(LC_TIME, 'id_ID.UTF-8');
        Carbon::setLocale('id');

        // Set timezone ke WIB (opsional, kalau belum di config/app.php)
        date_default_timezone_set('Asia/Jakarta');
    }
}
