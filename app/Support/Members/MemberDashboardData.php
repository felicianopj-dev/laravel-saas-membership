<?php

namespace App\Support\Members;

use App\Models\User;

class MemberDashboardData
{
    public static function make(?User $user): array
    {
        return [
            'member' => [
                'name' => $user?->name,
                'email' => $user?->email,
                'membership_status' => 'Active',
                'joined_at' => $user?->created_at?->toDateString(),
            ],
        ];
    }
}