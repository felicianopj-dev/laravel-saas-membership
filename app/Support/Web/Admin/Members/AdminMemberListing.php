<?php

namespace App\Support\Web\Admin\Members;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminMemberListing
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