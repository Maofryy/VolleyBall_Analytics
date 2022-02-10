<?php

namespace App\Controllers;

class Club extends BaseController
{
    public function index()
    {
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('club/list');
        echo view('innerpages/footer');
    }

    public function view($id)
    {
        $datas = [];
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('club/view', $datas);
        echo view('innerpages/footer');
    }
}
