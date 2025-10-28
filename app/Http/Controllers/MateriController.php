<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function viewMateri()
    {
        return view('pages.materi');
    }
}