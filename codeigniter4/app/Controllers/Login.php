<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function forgotPassword()
    {
        return view('forgot-password');
    }

    public function connexion()
    {

    }

    public function createAccount()
    {
        
    }
}
