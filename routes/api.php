<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApiEventController;
use App\Http\Controllers\TeamController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix otomatis /api (lihat RouteServiceProvider). Tambahkan endpoint di sini.
*/

// Route::get('/ping', fn () => ['pong' => true, 'time' => now()->toIso8601String()]);

// Route::middleware('supabase')->group(function () {
//     Route::get('/me', function (Request $request) {
//         $u = auth()->user();
//         return [
//             'id' => $u->id,
//             'supabase_id' => $u->supabase_id,
//             'email' => $u->email,
//             'name' => $u->name,
//             'role' => $u->role,
//             'avatar_url' => $u->avatar_url,
//             'is_admin' => $u->isAdmin(),
//             'created_at' => $u->created_at,
//         ];
//     });
// });

// /*
// |--------------------------------------------------------------------------
// | Fallback (opsional)
// |--------------------------------------------------------------------------
// */
// Route::fallback(fn () => response()->json(['message' => 'Not Found'], 404));

// User
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Seminar
Route::post('/seminar', [ApiEventController::class, 'apiStoreSeminar']);
Route::put('/seminar/{id}', [ApiEventController::class, 'apiUpdateSeminar']);
Route::delete('/seminar/{id}', [ApiEventController::class, 'apiDeleteSeminar']);

// Lomba
Route::post('/lomba', [ApiEventController::class, 'apiStoreLomba']);
Route::put('/lomba/{id}', [ApiEventController::class, 'apiUpdateLomba']);
Route::delete('/lomba/{id}', [ApiEventController::class, 'apiDeleteLomba']);

// Beasiswa
Route::post('/beasiswa', [ApiEventController::class, 'apiStoreBeasiswa']);
Route::put('/beasiswa/{id}', [ApiEventController::class, 'apiUpdateBeasiswa']);
Route::delete('/beasiswa/{id}', [ApiEventController::class, 'apiDeleteBeasiswa']);

// Team
Route::post('/team', [TeamController::class, 'apiStoreTeam']);
Route::put('/team/{id}', [TeamController::class, 'apiUpdateTeam']);
Route::delete('/team/{id}', [TeamController::class, 'apiDeleteTeam']);