<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/**
 * View Routing for Home
 */
Route::get('/', [HomeController::class, 'viewHome'])->name('home');

/**
 * Action Routing for Home
 */