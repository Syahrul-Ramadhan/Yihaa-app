<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

/**
 * Admin routes - protected by 'auth' and 'admin' middleware
 */
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'viewDashboard'])->name('dashboard');
    
    /**
     * Action Routing for Admin
     */
});