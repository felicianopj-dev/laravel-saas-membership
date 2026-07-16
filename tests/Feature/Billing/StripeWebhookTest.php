<?php

use App\Jobs\ProcessStripeWebhookEvent;
use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

const WEBHOOK_SECRET = 'whsec_test_secret';

beforeEach(function () {
    config(['services.stripe.webhook_secret' => WEBHOOK_SECRET]);
});

function signedWebhook(array $payload): TestResponse
{
    $body = json_encode($payload);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$body}", WEBHOOK_SECRET);

    return test()->call(
        'POST',
        '/api/webhooks/stripe',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        ],
        $body,
    );
}

function subscriptionEvent(
    string $eventId,
    User $user,
    Plan $plan,
    string $status = 'active',
    string $type = 'customer.subscription.updated',
    array $overrides = [],
): array {
    return [
        'id' => $eventId,
        'type' => $type,
        'data' => [
            'object' => array_merge([
                'id' => 'sub_123',
                'object' => 'subscription',
                'customer' => 'cus_123',
                'status' => $status,
                'cancel_at_period_end' => false,
                'start_date' => now()->subDay()->timestamp,
                'current_period_end' => now()->addMonth()->timestamp,
                'metadata' => ['user_id' => (string) $user->id],
                'items' => [
                    'data' => [[
                        'id' => 'si_123',
                        'price' => ['id' => $plan->stripe_price_id],
                        'current_period_end' => now()->addMonth()->timestamp,
                    ]],
                ],
            ], $overrides),
        ],
    ];
}

it('rejects webhooks with an invalid signature', function () {
    $response = test()->call(
        'POST',
        '/api/webhooks/stripe',
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'application/json', 'HTTP_STRIPE_SIGNATURE' => 't=1,v1=deadbeef'],
        json_encode(['id' => 'evt_bad', 'type' => 'customer.subscription.updated']),
    );

    $response->assertStatus(400);
    expect(Subscription::count())->toBe(0);
});

it('syncs a subscription from a verified webhook', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    $response = signedWebhook(subscriptionEvent('evt_1', $user, $plan, status: 'active'));

    $response->assertOk();

    $this->assertDatabaseHas('subscriptions', [
        'stripe_id' => 'sub_123',
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
    ]);

    expect($user->fresh()->stripe_id)->toBe('cus_123');
    $this->assertDatabaseHas('stripe_webhook_events', [
        'stripe_event_id' => 'evt_1',
    ]);
});

it('does not re-process a duplicate event id', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    signedWebhook(subscriptionEvent('evt_dup', $user, $plan, status: 'active'))->assertOk();

    // Same event id, but a payload that would flip the status if processed.
    signedWebhook(subscriptionEvent('evt_dup', $user, $plan, status: 'canceled'))
        ->assertOk();

    // The duplicate must be ignored: status stays as first processed.
    $this->assertDatabaseHas('subscriptions', [
        'stripe_id' => 'sub_123',
        'status' => 'active',
    ]);
    expect(Subscription::where('stripe_id', 'sub_123')->count())->toBe(1);
});

it('re-dispatches an event that was recorded but never processed', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    // A previous delivery recorded the event, then its job died on the queue and
    // exhausted its retries: the row exists but processed_at was never written.
    StripeWebhookEvent::query()->create([
        'stripe_event_id' => 'evt_stuck',
        'type' => 'customer.subscription.deleted',
    ]);

    Queue::fake();

    signedWebhook(subscriptionEvent(
        'evt_stuck',
        $user,
        $plan,
        status: 'canceled',
        type: 'customer.subscription.deleted',
    ))->assertOk();

    // Stripe's redelivery must get another chance to process, not a silent drop.
    Queue::assertPushed(ProcessStripeWebhookEvent::class);
});

it('does not re-dispatch an event that already finished processing', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    StripeWebhookEvent::query()->create([
        'stripe_event_id' => 'evt_done',
        'type' => 'customer.subscription.updated',
        'processed_at' => now(),
    ]);

    Queue::fake();

    signedWebhook(subscriptionEvent('evt_done', $user, $plan, status: 'active'))->assertOk();

    Queue::assertNotPushed(ProcessStripeWebhookEvent::class);
});

it('marks a subscription canceled on the deleted event', function () {
    $user = User::factory()->create(['stripe_id' => 'cus_123']);
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    $subscription = Subscription::factory()->create([
        'user_id' => $user->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'stripe_id' => 'sub_123',
    ]);

    signedWebhook(subscriptionEvent(
        'evt_del',
        $user,
        $plan,
        status: 'canceled',
        type: 'customer.subscription.deleted',
        overrides: ['ended_at' => now()->timestamp],
    ))->assertOk();

    expect($subscription->fresh()->status)->toBe('canceled');
});

it('keeps the status active and opens a grace period when cancel_at_period_end is set', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    signedWebhook(subscriptionEvent(
        'evt_grace',
        $user,
        $plan,
        status: 'active',
        overrides: ['cancel_at_period_end' => true],
    ))->assertOk();

    $subscription = Subscription::query()->where('stripe_id', 'sub_123')->first();

    // Status is not overloaded with 'canceled': a scheduled cancellation is
    // tracked by ends_at (grace period), and access is preserved until then.
    expect($subscription->status)->toBe('active')
        ->and($subscription->onGracePeriod())->toBeTrue()
        ->and($subscription->ends_at->toDateString())->toBe(now()->addMonth()->toDateString())
        ->and($user->fresh()->hasAccess())->toBeTrue();
});

it('falls back to the subscription item for current_period_end (Stripe 2025+ API)', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['stripe_price_id' => 'price_abc']);

    // Top-level current_period_end absent (as in newer API versions); only the
    // subscription item carries it.
    signedWebhook(subscriptionEvent(
        'evt_period',
        $user,
        $plan,
        status: 'active',
        overrides: ['current_period_end' => null],
    ))->assertOk();

    $subscription = Subscription::query()->where('stripe_id', 'sub_123')->first();

    expect($subscription->current_period_end)->not->toBeNull()
        ->and($subscription->current_period_end->toDateString())
        ->toBe(now()->addMonth()->toDateString());
});
