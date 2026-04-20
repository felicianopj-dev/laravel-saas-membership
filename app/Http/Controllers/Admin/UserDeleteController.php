<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserDeleteController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        $authenticatedUser = auth()->user();
        
        if ($authenticatedUser !== null && $user->is($authenticatedUser)) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }
}