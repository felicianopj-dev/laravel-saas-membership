<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Member\ProfileController;
use App\Http\Controllers\Web\Member\DashboardController;

Route::prefix('member')
    ->name('member.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    });