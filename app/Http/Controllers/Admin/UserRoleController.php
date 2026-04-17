<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;

class UserRoleController extends Controller
{
    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->user()->is($user) && $data['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot remove your own admin access.',
            ]);
        }
        
        $user->update([
            'role' => $data['role'],
        ]);
        
        return back()->with('success', 'User role updated successfully.');
    }
}