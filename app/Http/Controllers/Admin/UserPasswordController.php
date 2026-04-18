<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserPasswordRequest;

class UserPasswordController extends Controller
{
    public function update(UpdateUserPasswordRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        
        $user->update([
            'password' => $data['password'],
        ]);
        
        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User password updated successfully.');
    }
}