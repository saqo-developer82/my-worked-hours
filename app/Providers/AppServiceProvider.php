<?php

namespace App\Providers;

use App\Repositories\WorkedHourRepository;
use App\Repositories\WorkedHourRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WorkedHourRepositoryInterface::class,
            WorkedHourRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
