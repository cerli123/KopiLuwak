<?php

namespace App\Providers;

use App\Models\Pengaturan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Semua ->links() memakai tampilan pagination Kopiluwak (ikon kecil Font Awesome).
        // Tanpa ini Laravel memakai view Tailwind bawaan yang ikon panahnya
        // berukuran raksasa karena Tailwind tidak dimuat di layout.
        Paginator::defaultView('vendor.pagination.kopi');

        // Bila APP_URL memakai https://, semua link & aset ikut https (mencegah mixed content yang memblokir lokasi/pembayaran).
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Rute pengaturan lokasi produksi (admin) dimuat dari file terpisah.
        Route::group([], base_path('routes/lokasi-asal.php'));

        View::composer('*', function ($view) {
            $view->with('siteLogo', Pengaturan::logoUrl());
        });
    }
}
