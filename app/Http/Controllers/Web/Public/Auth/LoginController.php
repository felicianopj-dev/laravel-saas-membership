<?php

namespace App\Http\Controllers\Web\Public\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Auth\RedirectUserByRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }
    
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        
        if (! Auth::attempt($credentials, true)) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }
        
        $request->session()->regenerate();
        
        if (! $request->user()?->isActive()) {
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'This account is inactive. Please contact an administrator.',
            ])->onlyInput('email');
        }
        
        return redirect()->intended(
            RedirectUserByRole::path($request->user())
        );
    }
    
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}