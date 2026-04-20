<?php

namespace App\Support\Web\Admin\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminUsersData
{
    public static function make(?string $search = null, bool $withDeleted = false): array
    {
        $users = User::query()
            ->when($withDeleted, function (Builder $query): void {
                $query->withTrashed();
            })
            ->when($search, function (Builder $query, string $search): void {
                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(function (User $user): array {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'is_deleted' => $user->trashed(),
                    'deleted_at' => $user->deleted_at?->toDateTimeString(),
                    'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                    'created_at' => $user->created_at?->toDateTimeString(),
                    'updated_at' => $user->updated_at?->toDateTimeString(),
                ];
            });
        
        return [
            'title' => 'Users',
            'description' => 'Manage user accounts and roles.',
            'filters' => [
                'search' => $search ?? '',
                'with_deleted' => $withDeleted,
            ],
            'users' => $users,
        ];
    }
}