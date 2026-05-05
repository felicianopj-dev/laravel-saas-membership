<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Billing\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class PlanSubscriptionController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {
    }
    
    public function __invoke(Plan $plan): RedirectResponse
    {
        try {
            $this->billingService->subscribe(
                user: auth()->user(),
                plan: $plan,
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('member.plans.index')
                ->with('error', $exception->validator->errors()->first());
        }
        
        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Subscription updated successfully.');
    }
}