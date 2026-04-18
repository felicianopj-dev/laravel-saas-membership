<?php

namespace App\Support\Web\Admin\Users;

use App\Models\User;

class AdminEditUserData
{
    public static function make(User $user): array
    {
        return [
            'title' => 'Edit User',
            'description' => 'Update account information and role.',
            'userRecord' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ];
    }
}