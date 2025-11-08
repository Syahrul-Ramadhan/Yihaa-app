<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix otomatis /api (lihat RouteServiceProvider). Tambahkan endpoint di sini.
*/

Route::get('/ping', fn () => ['pong' => true, 'time' => now()->toIso8601String()]);

Route::middleware('supabase')->group(function () {
    Route::get('/me', function (Request $request) {
        $u = auth()->user();
        return [
            'id' => $u->id,
            'supabase_id' => $u->supabase_id,
            'email' => $u->email,
            'name' => $u->name,
        ];
    });
});

/*
|--------------------------------------------------------------------------
| Fallback (opsional)
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => response()->json(['message' => 'Not Found'], 404));