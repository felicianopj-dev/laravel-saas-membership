<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserRestoreController extends Controller
{
    public function __invoke(int $userId): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($userId);
        
        if (! $user->trashed()) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User is already active.');
        }
        
        $user->restore();
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User restored successfully.');
    }
}