<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Billable;

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    private bool $currentSubscriptionResolved = false;

    private ?Subscription $currentSubscriptionCache = null;

    public function currentSubscription(): ?Subscription
    {
        if ($this->currentSubscriptionResolved) {
            return $this->currentSubscriptionCache;
        }

        $this->currentSubscriptionResolved = true;

        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptions()
            ->where(function ($query) {
                $query
                    ->whereIn('status', ['active', 'trialing', 'past_due', 'incomplete'])
                    ->orWhere(function ($query) {
                        $query
                            ->where('status', 'canceled')
                            ->where('ends_at', '>', now());
                    });
            })
            ->latest('id')
            ->first();

        return $this->currentSubscriptionCache = $subscription;
    }

    /**
     * Forget the memoized current subscription (call after mutating subscriptions
     * within the same request).
     */
    public function forgetCurrentSubscription(): void
    {
        $this->currentSubscriptionResolved = false;
        $this->currentSubscriptionCache = null;
    }

    /**
     * The plan id the user may currently access content for, or null when the
     * user has no entitlement. Single source of truth for content gating.
     */
    public function accessiblePlanId(): ?int
    {
        return $this->hasAccess()
            ? $this->currentSubscription()?->plan_id
            : null;
    }

    public function hasAccess(): bool
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return false;
        }

        if (in_array($subscription->status, ['active', 'trialing'], true)) {
            return true;
        }

        if ($subscription->status === 'canceled' && $subscription->ends_at?->isFuture()) {
            return true;
        }

        return false;
    }

    public function onPlan(string $slug): bool
    {
        return $this->hasAccess()
            && $this->currentSubscription()?->plan?->slug === $slug;
    }

    public function subscriptionPlan(): ?Plan
    {
        return $this->currentSubscription()?->plan;
    }

    public function subscriptionStatus(): ?string
    {
        return $this->currentSubscription()?->status;
    }

    public function subscriptionEndsAt(): mixed
    {
        return $this->currentSubscription()?->ends_at;
    }

    public function hasExpiredSubscription(): bool
    {
        $subscription = $this->currentSubscription();

        return $subscription?->ends_at !== null
            && $subscription->ends_at->isPast();
    }

    public function isSubscribed(): bool
    {
        return $this->hasAccess();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
}
