<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::get('/members', MemberController::class)->name('members.index');