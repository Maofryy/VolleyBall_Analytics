<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('home');
    }
    
    public function welcome()
    {
        return view('welcome_message');
    }
}
