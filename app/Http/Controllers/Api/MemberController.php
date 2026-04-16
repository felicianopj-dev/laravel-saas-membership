<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Support\Web\Admin\Members\AdminMemberListing;

class MemberController extends Controller
{
    public function __invoke(AdminMemberListing $memberListing): JsonResponse
    {
        $members = $memberListing->paginate(10);
        
        return ApiResponse::success([
            'members' => MemberResource::collection($members->items()),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ],
        ], 'Members retrieved successfully.');
    }
}