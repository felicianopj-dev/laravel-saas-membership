<?php

use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\DashboardStatsController;

Route::get('/dashboard/stats', DashboardStatsController::class);
Route::get('/members', MemberController::class)->name('api.members.index');
