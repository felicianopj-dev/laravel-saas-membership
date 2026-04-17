<?php

namespace App\Support\Auth;

use App\Models\User;

class RedirectUserByRole
{
    public static function path(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'member' => route('member.dashboard'),
            default => route('login'),
        };
    }
}