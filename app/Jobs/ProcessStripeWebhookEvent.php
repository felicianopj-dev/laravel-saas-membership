<?php

namespace App\Jobs;

use App\Models\StripeWebhookEvent;
use App\Services\Billing\StripeSubscriptionSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Event;

class ProcessStripeWebhookEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  string  $payload  The raw, signature-verified webhook payload.
     */
    public function __construct(
        private readonly string $payload,
    ) {}

    public function handle(StripeSubscriptionSynchronizer $sync): void
    {
        $event = Event::constructFrom(json_decode($this->payload, true));

        // Correlate every log line emitted while processing this event (including
        // the synchronizer's) back to the originating Stripe event.
        Log::withContext([
            'stripe_event_id' => $event->id,
            'stripe_event_type' => $event->type,
        ]);

        Log::info('Processing Stripe webhook event.');

        match ($event->type) {
            'checkout.session.completed' => $sync->syncFromCheckoutSession($event->data->object),
            'customer.subscription.created',
            'customer.subscription.updated' => $sync->syncSubscription($event->data->object),
            'customer.subscription.deleted' => $sync->markDeleted($event->data->object),
            default => Log::info('Unhandled Stripe webhook event.', ['type' => $event->type]),
        };

        StripeWebhookEvent::query()
            ->where('stripe_event_id', $event->id)
            ->update(['processed_at' => now()]);
    }
}
