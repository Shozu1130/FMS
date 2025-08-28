<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
       $this->configureRateLimiting();

    $this->routes(function () {
        Route::middleware('web')
            ->group(base_path('routes/web.php')); // This line should exist
    });
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Define rate limiting here if needed; left empty to avoid undefined method errors.
    }

    /**
     * Execute given routes callback so callers can register routes.
     */
    protected function routes(callable $routes): void
    {
        $routes();
    }
}
