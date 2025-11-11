<?php

use App\Http\Controllers\MateriController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamChatController;
use App\Http\Controllers\AuthController;

// Route utama menggunakan view login yang sudah jadi
Route::view('/', 'pages.users.login')->name('login');
Route::view('/register', 'pages.users.register')->name('register');
Route::view('/forgot-password', 'pages.users.forgot-password')->name('password.request');
Route::view('/reset-password', 'pages.users.reset-password')->name('password.reset');
Route::view('/admin-login', 'pages.users.admin-login')->name('admin.login');

Route::view('/dashboard', 'dashboard')->name('dashboard');

// Jika nanti butuh proteksi halaman web, gunakan middleware 'supabase' di API saja
// atau bikin guard custom. Untuk sekarang biarkan halaman web public,
// sedangkan data sensitif diambil via /api/* yang sudah dilindungi middleware 'supabase'.

// Routes tambahan aplikasi
Route::prefix('home')->name('home.')->group(base_path('routes/home.php'));
// Route::prefix('teams')->name('teams.')->group(base_path('routes/team.php'));
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
Route::prefix('events')->name('events.')->group(base_path('routes/event.php'));
Route::prefix('materi')->name('materi.')->group(base_path('routes/materi.php'));
Route::prefix('notifikasi')->name('notifikasi.')->group(base_path('routes/notifikasi.php'));

// Contoh dan halaman lain
Route::get('/home', [PostController::class, 'index'])->name('posts.index');
Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
Route::get('/test-insert', [PostController::class, 'testInsert']);
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/chat', [TeamChatController::class, 'index'])->name('chat.index');
Route::get('/chat/{team_id}', [TeamChatController::class, 'show'])->name('chat.show');

// Hapus/komentar baris auth bawaan Laravel (session) kalau tidak dipakai:
// require __DIR__.'/auth.php';

// Auth Logic
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
