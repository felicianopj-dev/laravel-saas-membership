<?php

namespace App\Support\Members;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberListing
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->where('role', 'member')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}