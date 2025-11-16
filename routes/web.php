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

// ========================================
// PUBLIC ROUTES (No Auth Required)
// ========================================

// Landing/Home - Langsung ke home tanpa login
Route::get('/', [PostController::class, 'index'])->name('home');

// Auth Pages
Route::view('/login', 'pages.users.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::view('/register', 'pages.users.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

Route::view('/forgot-password', 'pages.users.forgot-password')->name('forgot');
Route::view('/reset-password', 'pages.users.reset-password')->name('reset');

// Admin Login
Route::view('/admin-login', 'pages.users.admin-login')->name('admin.login');

// ========================================
// PROTECTED ROUTES (Auth Required)
// ========================================
Route::middleware(['supabase'])->group(function () {
    
    // Home/Posts (setelah login bisa create post)
    Route::get('/home', [PostController::class, 'index'])->name('posts.index');
    Route::post('/post/store', [PostController::class, 'store'])->name('posts.store');
    Route::get('/test-insert', [PostController::class, 'testInsert']);
    
    // Events
    Route::get('/seminar', [EventController::class, 'viewSeminar'])->name('seminar');
    Route::get('/beasiswa', [EventController::class, 'viewBeasiswa'])->name('beasiswa');
    Route::get('/lomba', [EventController::class, 'viewLomba'])->name('lomba');
    
    // Materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    
    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'viewNotifikasi'])->name('notifikasi.index');
    
    // Teams & Chat
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/chat', [TeamChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{team_id}', [TeamChatController::class, 'show'])->name('chat.show');
    
    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    
    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));

/**
 * DEPRECATED - Routes moved to web.php
 * File ini bisa dihapus atau dikosongkan
 */

// Route sudah dipindah ke web.php
// Route::get('/', [HomeController::class, 'viewHome'])->name('home');

