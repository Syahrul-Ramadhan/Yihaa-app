<?php

use App\Http\Controllers\MateriController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotifikasiController;

// Route utama menggunakan view login yang sudah jadi
Route::view('/', 'pages.users.login')->name('login');
Route::view('/register', 'pages.users.register')->name('register');
Route::view('/forgot-password', 'pages.users.forgot-password')->name('forgot');
Route::view('/reset-password', 'pages.users.reset-password')->name('reset');

// Auth Logic
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
Route::view('/admin-login', 'pages.users.admin-login')->name('admin.login');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::middleware(['auth.check'])->group(function () {
    Route::get('/home', [PostController::class, 'index'])->name('posts.index');
    Route::get('/test-insert', [PostController::class, 'testInsert']);
    // event
    Route::get('/seminar', [EventController::class, 'viewSeminar'])->name('seminar');
    Route::get('/beasiswa', [EventController::class, 'viewBeasiswa'])->name('beasiswa');
    Route::get('/lomba', [EventController::class, 'viewLomba'])->name('lomba');
    // materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/notifikasi', [NotifikasiController::class, 'viewNotifikasi'])->name('index');
    // team
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/chat', [TeamChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{team_id}', [TeamChatController::class, 'show'])->name('chat.show');
    // profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
});
