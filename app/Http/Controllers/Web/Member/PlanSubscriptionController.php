<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class PlanSubscriptionController extends Controller
{
    public function __invoke(Plan $plan): RedirectResponse
    {
        if (! $plan->is_active) {
            return redirect()
                ->route('member.plans.index')
                ->with('error', 'This plan is not available.');
        }
        
        $currentSubscription = auth()->user()
            ->subscriptions()
            ->where('status', 'active')
            ->latest('id')
            ->first();
        
        if ($currentSubscription?->plan_id === $plan->id) {
            return redirect()
                ->route('member.plans.index')
                ->with('error', 'You are already subscribed to this plan.');
        }
        
        $startsAt = Carbon::now();
        
        $endsAt = $plan->billing_interval === 'yearly'
            ? $startsAt->copy()->addYear()
            : $startsAt->copy()->addMonth();
        
        Subscription::query()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'trial_ends_at' => null,
            ],
        );
        
        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Subscription updated successfully.');
    }
}