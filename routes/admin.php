<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserDeleteController;
use App\Http\Controllers\Admin\UserRestoreController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminLessonController;
use App\Http\Controllers\Admin\UserPasswordController;
use App\Http\Controllers\Admin\AdminSubscriptionController;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
            
            Route::put('/{user}/password', [UserPasswordController::class, 'update'])->name('users.password.update');
            
            Route::delete('/{user}', UserDeleteController::class)->name('users.destroy');
            
            Route::patch('/{user}/restore', UserRestoreController::class)->name('admin.users.restore');
        });
        
        Route::resource('/courses', AdminCourseController::class)->names('courses');
        
        Route::resource('/courses.lessons', AdminLessonController::class)
            ->except(['show'])
            ->names('courses.lessons');
        
        Route::get('/subscriptions', AdminSubscriptionController::class)->name('subscriptions.index');
        
        Route::resource('plans', PlanController::class);
    });