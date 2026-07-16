<?php

namespace App\Http\Controllers\Web\Public\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Plan;
use App\Models\User;
use App\Services\Billing\BillingService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        try {
            $user = DB::transaction(function () use ($request): User {
                $user = User::query()->create([
                    'name' => $request->string('name')->toString(),
                    'email' => $request->string('email')->toString(),
                    'password' => $request->string('password')->toString(),
                    'role' => 'member',
                    'status' => 'active',
                ]);

                $plan = Plan::where('slug', 'free')->firstOrFail();

                $this->billingService->createSubscriptionCheckout(
                    user: $user,
                    plan: $plan,
                );

                return $user;
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->validator->errors()->first());
        }

        // Fires the framework's SendEmailVerificationNotification listener, so a
        // freshly registered member receives the signed verification link.
        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        // Member routes require a verified email; send the user straight to the
        // verification notice instead of bouncing them off the dashboard.
        return redirect()->route('verification.notice');
    }
}
