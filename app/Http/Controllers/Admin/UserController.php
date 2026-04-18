<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Support\Web\Admin\Users\AdminUsersData;
use App\Support\Web\Admin\Users\AdminEditUserData;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Users/Index', [
            ...AdminUsersData::make(
                $request->string('search')->toString()
            ),
        ]);
    }
    
    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            ...AdminEditUserData::make($user),
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        
        if ($request->user()->is($user) && $data['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot remove your own admin access.',
            ]);
        }
        
        if ($request->user()->is($user) && $data['status'] !== 'active') {
            return back()->withErrors([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }
        
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
        ]);
        
        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User updated successfully.');
    }
}