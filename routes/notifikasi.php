<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotifikasiController;

/**
 * View Routing for Materi
 */
Route::get('/', [NotifikasiController::class, 'viewNotifikasi'])->name('index');
