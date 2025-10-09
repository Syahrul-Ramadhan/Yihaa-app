<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

/**
 * View Routing for Home
 */
Route::get('/', [DashboardController::class, 'viewDashboard'])->name('dashboard');

/**
 * Action Routing for Home
 */