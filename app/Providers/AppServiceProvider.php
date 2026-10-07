<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        if (\Illuminate\Support\Facades\Schema::hasTable('profil_sekolahs')) {
            \Illuminate\Support\Facades\View::share('profil_sekolah', \App\Models\ProfilSekolah::first());
        }
    }
}
