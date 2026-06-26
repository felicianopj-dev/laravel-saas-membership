<?php

namespace App\Providers;

use App\Services\Billing\Contracts\BillingProviderInterface;
use App\Services\Billing\Providers\MockBillingProvider;
use App\Services\Billing\Providers\StripeBillingProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BillingProviderInterface::class, function () {
            return match (config('billing.driver')) {
                'stripe' => new StripeBillingProvider,
                default => new MockBillingProvider,
            };
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
