<?php

namespace App\Support\Web\Admin\Dashboard;

class AdminDashboardData
{
    public static function make(): array
    {
        return [
            'title' => 'Dashboard',
            'description' => 'Overview of your SaaS performance, membership activity, and business health.',
            
            // These dashboard values are currently mocked to establish the admin UI contract.
            // They can be replaced later with database-driven metrics without changing the view.
            
            'stats' => [
                [
                    'label' => 'Total members',
                    'value' => '1,284',
                    'change' => '+12.4%',
                    'description' => 'Compared to last month',
                ],
                [
                    'label' => 'Active subscriptions',
                    'value' => '932',
                    'change' => '+8.1%',
                    'description' => 'Recurring paid members',
                ],
                [
                    'label' => 'Monthly revenue',
                    'value' => '$18,420',
                    'change' => '+14.9%',
                    'description' => 'Estimated MRR',
                ],
                [
                    'label' => 'Churn rate',
                    'value' => '2.3%',
                    'change' => '-0.6%',
                    'description' => 'Lower is better',
                ],
            ],
            
            'recentMembers' => [
                [
                    'name' => 'Olivia Carter',
                    'email' => 'olivia@example.com',
                    'plan' => 'Pro Monthly',
                    'status' => 'Active',
                    'joinedAt' => '2026-04-15',
                ],
                [
                    'name' => 'Noah Bennett',
                    'email' => 'noah@example.com',
                    'plan' => 'Starter',
                    'status' => 'Trial',
                    'joinedAt' => '2026-04-14',
                ],
                [
                    'name' => 'Emma Brooks',
                    'email' => 'emma@example.com',
                    'plan' => 'Pro Yearly',
                    'status' => 'Active',
                    'joinedAt' => '2026-04-13',
                ],
                [
                    'name' => 'Liam Parker',
                    'email' => 'liam@example.com',
                    'plan' => 'Starter',
                    'status' => 'Canceled',
                    'joinedAt' => '2026-04-10',
                ],
            ],
            
            'growthSummary' => [
                [
                    'label' => 'New members',
                    'value' => 74,
                    'color' => 'slate',
                ],
                [
                    'label' => 'Retention',
                    'value' => 88,
                    'color' => 'indigo',
                ],
                [
                    'label' => 'Upgrade rate',
                    'value' => 41,
                    'color' => 'emerald',
                ],
            ],
            
            'nextStep' => [
                'eyebrow' => 'Next step',
                'title' => 'Billing and subscription management',
                'description' => 'This layout is ready to receive subscription pages, plan management, invoices, and member detail screens.',
                'badge' => 'Portfolio-ready admin foundation',
            ],
        ];
    }
}