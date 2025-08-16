<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DerprTransporteService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DerprTransporteService::class, function ($app) {
            return new DerprTransporteService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
