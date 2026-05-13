<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Billing\Providers\MockBillingProvider;
use App\Services\Billing\Providers\StripeBillingProvider;
use App\Services\Billing\Contracts\BillingProviderInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BillingProviderInterface::class, function () {
            return match (config('billing.driver')) {
                'stripe' => new StripeBillingProvider(),
                default => new MockBillingProvider(),
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
