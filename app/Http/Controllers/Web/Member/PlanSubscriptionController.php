<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Billing\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PlanSubscriptionController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {}

    public function __invoke(Plan $plan): Response|RedirectResponse
    {
        try {
            $checkoutUrl = $this->billingService->createSubscriptionCheckout(
                user: auth()->user(),
                plan: $plan,
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('member.plans.index')
                ->with('error', $exception->validator->errors()->first());
        }

        if ($checkoutUrl === route('member.plans.index')) {
            return to_route('member.plans.index')
                ->with('success', 'Plan updated successfully.');
        }

        return Inertia::location($checkoutUrl);
    }
}
