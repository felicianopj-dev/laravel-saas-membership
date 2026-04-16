<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Support\ApiResponse;
use App\Support\Members\MemberListing;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function __invoke(MemberListing $memberListing): JsonResponse
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