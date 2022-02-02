<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function me()
    {
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('users/me');
        echo view('innerpages/footer');
    }
}
