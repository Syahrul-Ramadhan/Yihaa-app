<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminMaterialController;

/**
 * Admin routes - protected by 'auth.check' and 'admin' middleware
 */
Route::middleware(['auth.check', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'viewDashboard'])->name('dashboard');
    
    // Event Management Routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [DashboardController::class, 'viewManageEvent'])->name('index');
        
        // Seminar Routes
        Route::post('/seminar', [AdminEventController::class, 'storeSeminar'])->name('store.seminar');
        Route::put('/seminar/{id}', [AdminEventController::class, 'updateSeminar'])->name('update.seminar');
        Route::delete('/seminar/{id}', [AdminEventController::class, 'deleteSeminar'])->name('delete.seminar');
        
        // Beasiswa Routes
        Route::post('/beasiswa', [AdminEventController::class, 'storeBeasiswa'])->name('store.beasiswa');
        Route::put('/beasiswa/{id}', [AdminEventController::class, 'updateBeasiswa'])->name('update.beasiswa');
        Route::delete('/beasiswa/{id}', [AdminEventController::class, 'deleteBeasiswa'])->name('delete.beasiswa');
        
        // Lomba Routes
        Route::post('/lomba', [AdminEventController::class, 'storeLomba'])->name('store.lomba');
        Route::put('/lomba/{id}', [AdminEventController::class, 'updateLomba'])->name('update.lomba');
        Route::delete('/lomba/{id}', [AdminEventController::class, 'deleteLomba'])->name('delete.lomba');
    });
    

});