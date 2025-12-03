<?php

namespace App\Providers;

use PDO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Kita cek dulu apa isi config database saat ini
            if (config('database.default') == 'pgsql') {

                // --- JEBAKAN DEBUGGING ---
                // Jika kode sampai sini, layar akan menampilkan tulisan ini & berhenti.
                //dd('Berhasil masuk ke settingan PGSQL!');
                // -------------------------

                DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            } else {
                // Tambahkan ini untuk cek jika ternyata bukan pgsql
                //dd('Config bukan pgsql, tapi: ' . config('database.default'));
            }
        } catch (\Exception $e) {
            dd($e->getMessage()); // <--- Kita paksa errornya muncul di layar!
        }
    // ---------------------------
        // Share data user login ke semua view
        View::composer('*', function ($view) {
            $user = [
                'id' => session('user_id'),
                'name' => session('user_name'),
                'email' => session('user_email'),
                'role' => session('user_role'),
            ];
            $view->with('authUser', $user);
        });
    }
}
