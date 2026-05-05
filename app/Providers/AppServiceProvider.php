<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Billing\MockBillingProvider;
use App\Services\Billing\BillingProviderInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BillingProviderInterface::class, MockBillingProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
