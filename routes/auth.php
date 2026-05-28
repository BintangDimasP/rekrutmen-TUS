<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ── Forgot Password (OTP via email) ─────────────────────────────────
    // Step 1: Email
    Route::get('forgot-password', [ForgotPasswordOtpController::class, 'showEmailForm'])
        ->name('password.request');
    Route::get('forgot-password/email', [ForgotPasswordOtpController::class, 'showEmailForm'])
        ->name('password.otp.email');
    Route::post('forgot-password/send-otp', [ForgotPasswordOtpController::class, 'sendOtp'])
        ->middleware('throttle:6,1')
        ->name('password.otp.send');

    // Step 2: OTP
    Route::get('forgot-password/otp', [ForgotPasswordOtpController::class, 'showOtpForm'])
        ->name('password.otp.form');
    Route::post('forgot-password/verify-otp', [ForgotPasswordOtpController::class, 'verifyOtp'])
        ->middleware('throttle:10,1')
        ->name('password.otp.verify');

    // Step 3: Password baru
    Route::get('forgot-password/reset', [ForgotPasswordOtpController::class, 'showResetForm'])
        ->name('password.otp.reset.form');
    Route::post('forgot-password/reset', [ForgotPasswordOtpController::class, 'resetPassword'])
        ->name('password.otp.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
