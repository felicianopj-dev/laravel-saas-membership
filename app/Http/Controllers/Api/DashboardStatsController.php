<?php

namespace App\Http\Controllers\Api;

use App\Support\ApiResponse;
use App\Http\Controllers\Controller;
use App\Support\Dashboard\DashboardMetrics;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends Controller
{
    public function __invoke(DashboardMetrics $dashboardMetrics): JsonResponse
    {
        return ApiResponse::success([
            'stats' => $dashboardMetrics->stats(),
            'recent_members' => $dashboardMetrics->recentMembers(),
        ], 'Dashboard stats retrieved successfully.');
    }
}