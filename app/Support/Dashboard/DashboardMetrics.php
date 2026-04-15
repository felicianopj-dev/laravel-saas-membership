<?php

namespace App\Support\Dashboard;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardMetrics
{
    public function stats(): array
    {
        return [
            'users' => User::query()->count(),
            'admins' => User::query()->where('role', 'admin')->count(),
            'members' => User::query()->where('role', 'member')->count(),
            'activeSubscriptions' => Subscription::query()->where('status', 'active')->count(),
            'plans' => Plan::query()->count(),
        ];
    }
    
    public function recentMembers(int $limit = 5): Collection
    {
        return User::query()
            ->where('role', 'member')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'created_at' => $user->created_at?->toISOString(),
            ]);
    }
}