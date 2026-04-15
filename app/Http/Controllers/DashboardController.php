<?php

namespace App\Http\Controllers;

use App\Support\Dashboard\DashboardMetrics;
use Inertia\Inertia;
use Inertia\Response;

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