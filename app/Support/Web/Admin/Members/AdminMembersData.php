<?php

namespace App\Support\Web\Admin\Members;

class AdminMembersData
{
    public static function make(): array
    {
        return [
            'title' => 'Members',
            'description' => 'View, search, and manage the people subscribed to your SaaS platform.',
            
            // These member records are currently mocked to establish the admin UI contract.
            
            'members' => [
                [
                    'id' => 1,
                    'name' => 'Olivia Carter',
                    'email' => 'olivia@example.com',
                    'plan' => 'Pro Monthly',
                    'status' => 'Active',
                    'joinedAt' => '2026-04-15',
                ],
                [
                    'id' => 2,
                    'name' => 'Noah Bennett',
                    'email' => 'noah@example.com',
                    'plan' => 'Starter',
                    'status' => 'Trial',
                    'joinedAt' => '2026-04-14',
                ],
                [
                    'id' => 3,
                    'name' => 'Emma Brooks',
                    'email' => 'emma@example.com',
                    'plan' => 'Pro Yearly',
                    'status' => 'Active',
                    'joinedAt' => '2026-04-13',
                ],
                [
                    'id' => 4,
                    'name' => 'Liam Parker',
                    'email' => 'liam@example.com',
                    'plan' => 'Starter',
                    'status' => 'Canceled',
                    'joinedAt' => '2026-04-10',
                ],
                [
                    'id' => 5,
                    'name' => 'Sophia Reed',
                    'email' => 'sophia@example.com',
                    'plan' => 'Growth',
                    'status' => 'Active',
                    'joinedAt' => '2026-04-08',
                ],
            ],
        ];
    }
}