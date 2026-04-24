<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Public\HomeController;
use App\Http\Controllers\Web\Member\PlanController;
use App\Http\Controllers\Web\Member\ProfileController;
use App\Http\Controllers\Web\Member\DashboardController;
use App\Http\Controllers\Web\Public\Auth\LoginController;
use App\Http\Controllers\Web\Public\Auth\RegisterController;
use App\Http\Controllers\Web\Member\ProfilePasswordController;
use App\Http\Controllers\Web\Member\PlanSubscriptionController;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'destroy'])->name('logout');
    
    Route::prefix('member')
        ->middleware('member')
        ->name('member.')
        ->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
            
            Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::put('/profile/password', ProfilePasswordController::class)->name('profile.password.update');
            Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
            Route::post('/plans/{plan}/subscribe', PlanSubscriptionController::class)
                ->name('plans.subscribe');
        });
});
