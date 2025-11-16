<?php

namespace App\Providers;

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
