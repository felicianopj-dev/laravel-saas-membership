<?php

namespace App\Http\Controllers\Web\Member;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\Web\Member\MemberDashboardData;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Member/Dashboard', [
            ...MemberDashboardData::make($request->user()),
        ]);
    }
}