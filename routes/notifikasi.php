<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotifikasiController;

/**
 * Notification Routes
 */
Route::get('/', [NotifikasiController::class, 'index'])->name('index');
Route::post('/mark-as-read/{id}', [NotifikasiController::class, 'markAsRead'])->name('markAsRead');
Route::post('/mark-all-as-read', [NotifikasiController::class, 'markAllAsRead'])->name('markAllAsRead');
Route::delete('/{id}', [NotifikasiController::class, 'delete'])->name('delete');
Route::post('/accept-team/{notificationId}/{teamId}', [NotifikasiController::class, 'acceptTeamRequest'])->name('acceptTeam');
