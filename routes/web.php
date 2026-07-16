<?php

use App\Http\Controllers\Web\Member\BillingPortalController;
use App\Http\Controllers\Web\Member\DashboardController;
use App\Http\Controllers\Web\Member\MemberCourseController;
use App\Http\Controllers\Web\Member\MemberCourseShowController;
use App\Http\Controllers\Web\Member\MemberLessonShowController;
use App\Http\Controllers\Web\Member\MemberSubscriptionController;
use App\Http\Controllers\Web\Member\PlanController;
use App\Http\Controllers\Web\Member\PlanSubscriptionController;
use App\Http\Controllers\Web\Member\ProfileController;
use App\Http\Controllers\Web\Member\ProfilePasswordController;
use App\Http\Controllers\Web\Public\Auth\EmailVerificationController;
use App\Http\Controllers\Web\Public\Auth\LoginController;
use App\Http\Controllers\Web\Public\Auth\RegisterController;
use App\Http\Controllers\Web\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::prefix('member')
        ->middleware(['member', 'verified'])
        ->name('member.')
        ->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');

            Route::group(['prefix' => 'profile'], function () {
                Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
                Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
                Route::put('/password', ProfilePasswordController::class)->name('profile.password.update');
            });

            Route::group(['prefix' => 'plans'], function () {
                Route::get('/', [PlanController::class, 'index'])->name('plans.index');
                Route::post('/{plan}/subscribe', PlanSubscriptionController::class)->name('plans.subscribe');
            });

            Route::group(['prefix' => 'subscription'], function () {
                Route::post('/cancel', [MemberSubscriptionController::class, 'cancel'])->name('subscription.cancel');
                Route::post('/resume', [MemberSubscriptionController::class, 'resume'])->name('subscription.resume');
            });

            Route::group(['prefix' => '/courses'], function () {
                Route::get('/', MemberCourseController::class)->name('courses.index');
                Route::get('/{course}', MemberCourseShowController::class)->name('courses.show');

                Route::group(['prefix' => '{course}'], function () {
                    Route::get('/lessons/{lesson}', MemberLessonShowController::class)->name('courses.lessons.show');
                });
            });

            Route::get('/billing/portal', BillingPortalController::class)->name('billing.portal');
        });
});
