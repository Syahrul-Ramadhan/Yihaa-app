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

Route::view('/forgot-password', 'pages.users.forgot-password')->name('forgot');
Route::view('/reset-password', 'pages.users.reset-password')->name('reset');

// register route
Route::view('/register', 'pages.users.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// Login route
// Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::view('/', 'pages.users.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
Route::view('/admin-login', 'pages.users.admin-login')->name('admin.login');
Route::view('/dashboard', 'pages.admin.dashboard')->name('dashboard');

    Route::get('/home', [PostController::class, 'index'])->name('posts.index');
    Route::post('/post/store', [PostController::class, 'store'])->name('posts.store');
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

