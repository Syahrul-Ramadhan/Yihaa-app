<?php

use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\MateriController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminMaterialController;
use App\Http\Controllers\commentController;
use App\Http\Controllers\LikeController;

Route::view('/forgot-password', 'pages.users.forgot-password')->name('forgot');
Route::view('/reset-password', 'pages.users.reset-password')->name('reset');

// register route
Route::view('/register', 'pages.users.register')->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// Login route
// Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::view('/', 'pages.users.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/login-loading', [AuthController::class, 'loginLoading'])->name('login.loading');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// Route::get('/event', [DashboardController::class, 'manage-event'])->name('manage-event');

// Admin routes - enabled
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));

// Admin login routes
Route::view('/admin-login', 'pages.admin.admin-login')->name('admin.login');
Route::post('/admin-login', [AuthController::class, 'adminLogin'])->name('admin.login.process');
Route::get('/admin-logout', [AuthController::class, 'logoutAdmin'])->name('admin.logout');

// Legacy dashboard route - redirect to admin dashboard (if user is admin)
Route::get('/dashboard', [DashboardController::class, 'viewDashboard'])->name('dashboard');
Route::get('/manage-event', [DashboardController::class, 'viewManageEvent'])->name('viewManageEvent');
// seminar
Route::post('/manage-event/addSeminar', [AdminEventController::class, 'storeSeminar'])->name('seminar.store');
Route::post('/manage-event/updateSeminar', [AdminEventController::class, 'updateSeminar'])->name('seminar.update');
Route::post('/manage-event/deleteSeminar', [AdminEventController::class, 'deleteSeminar'])->name('seminar.delete');
// beasiswa
Route::post('/manage-event/addBeasiswa', [AdminEventController::class, 'storeBeasiswa'])->name('beasiswa.store');
Route::post('/manage-event/updateBeasiswa', [AdminEventController::class, 'updateBeasiswa'])->name('beasiswa.update');
Route::post('/manage-event/deleteBeasiswa', [AdminEventController::class, 'deleteBeasiswa'])->name('beasiswa.delete');
// lomba
Route::post('/manage-event/addLomba', [AdminEventController::class, 'storeLomba'])->name('lomba.store');
Route::post('/manage-event/updateLomba', [AdminEventController::class, 'updateLomba'])->name('lomba.update');
Route::post('/manage-event/deleteLomba', [AdminEventController::class, 'deleteLomba'])->name('lomba.delete');

Route::get('/manage-material', [DashboardController::class, 'viewManageMaterial'])->name('viewManageMaterial');
Route::get('/manage-user', [DashboardController::class, 'viewManageUser'])->name('viewManageUser');

// Material Management Routes
Route::prefix('materials')->name('materials.')->group(function () {
    Route::get('/', [AdminMaterialController::class, 'index'])->name('index');
    Route::post('/', [AdminMaterialController::class, 'store'])->name('store');
    Route::put('/{id}', [AdminMaterialController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminMaterialController::class, 'destroy'])->name('delete');
    Route::post('/{id}/approve', [AdminMaterialController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [AdminMaterialController::class, 'reject'])->name('reject');
});

Route::get('/home', [PostController::class, 'index'])->name('posts.index');
Route::post('/post/store', [PostController::class, 'store'])->name('posts.store');
Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
Route::get('/comments/{postId}', [CommentController::class, 'fetch']);
Route::post('/comments/add', [CommentController::class, 'add']);
Route::post('/like', [LikeController::class, 'toggle'])->name('posts.like');

    // event
Route::get('/seminar', [EventController::class, 'viewSeminar'])->name('seminar');
Route::get('/beasiswa', [EventController::class, 'viewBeasiswa'])->name('beasiswa');
Route::get('/lomba', [EventController::class, 'viewLomba'])->name('lomba');
    // materi
Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
Route::delete('/materi/{id}', [MateriController::class, 'destroy'])->name('materi.destroy');
    // notifikasi
Route::prefix('notifikasi')->name('notifikasi.')->group(base_path('routes/notifikasi.php'));
    // team
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');
Route::put('/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy');
Route::post('/teams/{id}/join', [TeamController::class, 'join'])->name('teams.join');
Route::post('/teams/{team_id}/accept/{user_id}', [TeamController::class, 'acceptMember'])->name('teams.acceptMember');
Route::post('/teams/{team_id}/reject/{user_id}', [TeamController::class, 'rejectMember'])->name('teams.rejectMember');
Route::delete('/teams/{team_id}/kick/{user_id}', [TeamController::class, 'kickMember'])->name('teams.kickMember');
Route::get('/chat', [TeamChatController::class, 'index'])->name('chat.index');
Route::get('/chat/{team_id}', [TeamChatController::class, 'show'])->name('chat.show');
Route::post('/chat/{team_id}/send', [TeamChatController::class, 'sendMessage'])->name('chat.send');
    // profile
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::get('/profile/edit', [ProfileController::class, 'showEdit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');