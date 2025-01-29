<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    //---Register---//
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    //---Login---//
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login',[AuthController::class, 'login']);

    //---Password reset---//
    Route::get('/forgot-password', [PasswordResetController::class, 'requestPassword'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});



