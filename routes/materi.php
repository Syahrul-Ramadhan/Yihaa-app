<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MateriController;

/**
 * View Routing for Materi
 */
Route::get('/', [MateriController::class, 'viewMateri'])->name('index');
