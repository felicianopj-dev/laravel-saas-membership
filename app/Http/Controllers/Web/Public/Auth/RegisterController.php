<?php

namespace App\Http\Controllers\Web\Public\Auth;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use App\Services\Billing\BillingService;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService,
    ) {
    }
    
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }
    
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'role' => 'member',
            'status' => 'active',
        ]);
        
        try {
            $plan = Plan::where('slug', 'free')->firstOrFail();
            
            $this->billingService->createSubscriptionCheckout(
                user: $user->fresh(),
                plan: $plan,
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->validator->errors()->first());
        }
        
        Auth::login($user);
        
        $request->session()->regenerate();
        
        return redirect()->route('member.dashboard');
    }
}