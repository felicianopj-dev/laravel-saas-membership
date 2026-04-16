<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\DashboardController;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/members', MemberController::class)->name('members.index');