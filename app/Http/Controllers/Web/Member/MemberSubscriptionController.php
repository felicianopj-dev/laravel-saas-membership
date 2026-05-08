<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillingService;

class MemberSubscriptionController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService
    ) {
    }
    
    public function cancel()
    {
        $this->billingService->cancel(auth()->user());
        
        return back()->with('success', 'Subscription canceled.');
    }
    
    public function resume()
    {
        $this->billingService->resume(auth()->user());
        
        return back()->with('success', 'Subscription resumed.');
    }
}
