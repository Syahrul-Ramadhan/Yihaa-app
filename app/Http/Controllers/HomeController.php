<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function viewHome()
    {
        return view('pages.users.home');
    }
 
    public function viewSplashScreen()
    {
        return view('pages.users.splash-screen');
    }
}