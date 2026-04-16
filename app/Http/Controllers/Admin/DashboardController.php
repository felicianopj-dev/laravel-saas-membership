<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Support\Dashboard\AdminDashboardData;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            ...AdminDashboardData::make(),
        ]);
    }
}