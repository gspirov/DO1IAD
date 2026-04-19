<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('register', 'auth.register')
         ->name('register');

    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    Route::view('login', 'auth.login')
         ->name('login');

    Route::post('login', [LoginController::class, 'authenticate']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
         ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    Route::get('/profile/change-password', [ChangePasswordController::class, 'edit'])->name('profile.change-password');
    Route::post('/profile/change-password', [ChangePasswordController::class, 'update'])->name('profile.update-password');
});
