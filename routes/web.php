<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::prefix('home')->name('home.')->group(base_path('routes/home.php'));
// Route::prefix('teams')->name('teams.')->group(base_path('routes/team.php'));
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
Route::prefix('events')->name('events.')->group(base_path('routes/event.php'));
Route::prefix('materi')->name('materi.')->group(base_path('routes/materi.php'));
Route::prefix('notifikasi')->name('notifikasi.')->group(base_path('routes/notifikasi.php'));
Route::get('/home', [PostController::class, 'index'])->name('posts.index');

Route::get('/test-insert', [PostController::class, 'testInsert']);
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');

Route::get('/chat', [TeamChatController::class, 'index'])->name('chat.index');
Route::get('/chat/{team_id}', [TeamChatController::class, 'show'])->name('chat.show');
    
