<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

/**
 * View Routing for Events
 */
Route::get('/seminar', [EventController::class, 'viewSeminar'])->name('seminar');
Route::get('/beasiswa', [EventController::class, 'viewBeasiswa'])->name('beasiswa');
Route::get('/lomba', [EventController::class, 'viewLomba'])->name('lomba');

/**
 * Action Routing for Events
 */
