<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingPortalController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (! $user->stripe_id) {
            return redirect()
                ->route('member.plans.index')
                ->with('error', 'You need an active billing account before managing billing.');
        }
        
        return redirect()->away(
            $user->billingPortalUrl(route('member.plans.index'))
        );
    }
}