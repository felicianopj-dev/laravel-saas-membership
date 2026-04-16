<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Support\Dashboard\DashboardMetrics;

class DashboardController extends Controller
{
    public function __invoke(DashboardMetrics $dashboardMetrics): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => $dashboardMetrics->stats(),
            'recentMembers' => $dashboardMetrics->recentMembers(),
        ]);
    }
}