<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

/**
 * View Routing for Home
 */
Route::get('/', [TeamController::class, 'viewTeam'])->name('team');
