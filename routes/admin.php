<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserRoleController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        
        Route::get('/members', MemberController::class)->name('members.index');
        
        Route::get('/users', UserController::class)->name('users.index');
        Route::patch('/users/{user}/role', [UserRoleController::class, 'update'])->name('users.role.update');
    });