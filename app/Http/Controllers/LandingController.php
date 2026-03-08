<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index(): View
    {
        return view('pages.landing');
    }
}
