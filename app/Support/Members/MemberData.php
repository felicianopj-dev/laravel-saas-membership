<?php

namespace App\Support\Members;

use App\Models\User;

class MemberData
{
    public static function fromModel(User $member): array
    {
        return [
            'id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'status' => $member->status,
            'created_at' => $member->created_at?->toISOString(),
        ];
    }
}