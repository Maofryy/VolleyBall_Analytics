<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
       
        $session = session();

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('home');
        echo view('innerpages/footer');
    }
}
