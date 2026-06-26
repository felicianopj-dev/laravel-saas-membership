<?php

namespace App\Services\Billing\Data;

use Illuminate\Support\Carbon;

readonly class BillingSubscriptionResult
{
    public function __construct(
        public string $provider,
        public string $status,
        public ?string $providerSubscriptionId,
        public ?string $providerCustomerId,
        public Carbon $startsAt,
        public Carbon $endsAt,
        public ?Carbon $trialEndsAt = null,
    ) {}
}
