<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Services\Billing\StripeSubscriptionSynchronizer;
use App\Support\Web\Member\MemberPlansData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PlanController extends Controller
{
    public function index(Request $request, StripeSubscriptionSynchronizer $sync): Response
    {
        $user = $request->user();

        // Stripe returns the member here after checkout with a `session_id`. The
        // async webhook worker may not have synced the new subscription yet, so
        // sync eagerly before rendering to avoid a stale page that needs a
        // manual reload. The webhook stays a backstop; a failure here just logs
        // and falls through to the current DB state.
        if ($sessionId = $request->query('session_id')) {
            try {
                $sync->syncFromCheckoutSessionId($sessionId, $user->id);
            } catch (Throwable $e) {
                Log::warning('Eager checkout sync failed; falling back to async webhook.', [
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return Inertia::render('Member/Plans/Index', [
            ...MemberPlansData::make($user),
        ]);
    }
}
