<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'welcomeMessage' => __('messages.welcome'),
            'description' => __('messages.description')
        ]);
    }
}