<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserDeleteController;
use App\Http\Controllers\Admin\UserRestoreController;
use App\Http\Controllers\Admin\UserPasswordController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        
        Route::put('/users/{user}/password', [UserPasswordController::class, 'update'])->name('users.password.update');
        
        Route::delete('/users/{user}', UserDeleteController::class)->name('users.destroy');
        
        Route::patch('/users/{user}/restore', UserRestoreController::class)->name('admin.users.restore');
    });