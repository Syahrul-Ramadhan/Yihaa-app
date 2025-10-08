<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/**
 * View Routing for Authentication
 */
Route::get('login', [AuthController::class, 'viewLogin'])->name('login');
Route::get('register', [AuthController::class, 'viewRegister'])->name('register');
Route::get('forgot-password', [AuthController::class, 'viewForgotPassword'])->name('forgot-password');
Route::get('reset-password', [AuthController::class, 'viewResetPassword'])->name('reset-password');

/**
 * Action Routing for Authentication
 */
Route::post('login', [AuthController::class, 'actionLogin'])->name('action-login');
Route::post('register', [AuthController::class, 'actionRegister'])->name('action-register');
Route::post('forgot-password', [AuthController::class, 'actionForgotPassword'])->name('action-forgot-password');
Route::post('reset-password', [AuthController::class, 'actionResetPassword'])->name('action-reset-password');
Route::post('logout', [AuthController::class, 'actionLogout'])->name('action-logout');
