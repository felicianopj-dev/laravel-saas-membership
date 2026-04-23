<?php

namespace App\Support\Web\Member;

use App\Models\User;

class MemberProfileData
{
    public static function make(?User $user): array
    {
        return [
            'profile' => [
                'name' => $user?->name,
                'email' => $user?->email,
                'role' => $user?->role,
                'status' => $user?->status,
                'joined_at' => $user?->created_at?->toDateString(),
            ],
        ];
    }
}