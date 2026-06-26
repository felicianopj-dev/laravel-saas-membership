<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessStripeWebhookEvent;
use App\Models\StripeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Stripe webhook signature verification failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response('Invalid webhook.', 400);
        }

        // Idempotency: Stripe may deliver the same event more than once and out
        // of order. Record the event id first; a duplicate is acknowledged but
        // never re-processed.
        $record = StripeWebhookEvent::query()->firstOrCreate(
            ['stripe_event_id' => $event->id],
            ['type' => $event->type],
        );

        if (! $record->wasRecentlyCreated) {
            Log::info('Stripe webhook ignored (duplicate).', ['event_id' => $event->id]);

            return response('Webhook already handled.', 200);
        }

        // Acknowledge fast; do the heavy lifting (and any Stripe API calls) off
        // the request lifecycle so Stripe never sees a slow/timed-out endpoint.
        ProcessStripeWebhookEvent::dispatch($payload);

        return response('Webhook received.', 200);
    }
}
