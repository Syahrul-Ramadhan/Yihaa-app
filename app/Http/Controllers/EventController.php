<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function viewSeminar()
    {
        return view('pages.events.seminar');
    }

    public function viewBeasiswa()
    {
        return view('pages.events.beasiswa');
    }

    public function viewLomba()
    {
        return view('pages.events.lomba');
    }
}
