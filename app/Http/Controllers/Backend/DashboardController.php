<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {

        return view('Backend.pages.index');

    }
    public function userindex()
    {
        return view('Backend.pages.user-index');
    }
}
