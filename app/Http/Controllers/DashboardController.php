<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function viewDashboard()
    {
        return view('pages.admin.dashboard');
    }
 
    public function viewManageEvent()
    {
        return view('pages.admin.manage-event');
    }
}