<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// INI YANG PALING PENTING, JANGAN SAMPAI KETINGGALAN:
use Illuminate\Pagination\Paginator; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Panggil fungsi ini untuk memperbaiki panah raksasa tadi
        Paginator::useBootstrapFive();
    }
}