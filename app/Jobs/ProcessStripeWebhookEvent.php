<?php

namespace App\Jobs;

use App\Models\StripeWebhookEvent;
use App\Services\Billing\StripeSubscriptionSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\Event;

class ProcessStripeWebhookEvent implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Bounded so a worker that dies while holding the lock cannot wedge an event
     * forever. Stripe retries for hours, well past this window.
     */
    public int $uniqueFor = 300;

    /**
     * @param  string  $payload  The raw, signature-verified webhook payload.
     */
    public function __construct(
        private readonly string $payload,
    ) {}

    /**
     * One in-flight job per Stripe event, so two simultaneous deliveries of the
     * same event cannot race each other.
     */
    public function uniqueId(): string
    {
        return $this->event()->id;
    }

    public function handle(StripeSubscriptionSynchronizer $sync): void
    {
        $event = $this->event();

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

        // Marks the event done. This is what the controller gates redeliveries on,
        // so it must only be written once the work above has actually succeeded.
        StripeWebhookEvent::query()
            ->where('stripe_event_id', $event->id)
            ->update(['processed_at' => now()]);
    }

    private function event(): Event
    {
        return Event::constructFrom(json_decode($this->payload, true));
    }
}
