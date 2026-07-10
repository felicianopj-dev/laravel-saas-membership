<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'current_period_end',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * The subscription is in a live billing state. Mirrors Cashier: a scheduled
     * cancellation keeps `status` = active until the period actually ends, so
     * "scheduled to cancel" is tracked by `ends_at`, not by overloading `status`.
     */
    public function active(): bool
    {
        return in_array($this->status, ['active', 'trialing'], true);
    }

    /**
     * A cancellation is scheduled but the paid-through period has not lapsed yet,
     * so the member still has access (Cashier's "grace period").
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isFuture();
    }

    /**
     * A cancellation has been scheduled or has already taken effect.
     */
    public function canceled(): bool
    {
        return $this->ends_at !== null;
    }

    /**
     * The subscription is canceled and its access window has closed.
     */
    public function ended(): bool
    {
        return $this->canceled() && ! $this->onGracePeriod();
    }

    /**
     * The member is entitled to content: live, or canceled but still within the
     * paid-through grace period.
     */
    public function valid(): bool
    {
        return $this->active() || $this->onGracePeriod();
    }
}
