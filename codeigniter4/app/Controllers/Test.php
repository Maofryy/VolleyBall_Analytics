<?php

namespace App\Controllers;

class Test extends BaseController
{
    /**
     * Dislay list of match
     * @return view
     */
    public function index()
    {
        $datas = array();        

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('test/test', $datas);
        echo view('innerpages/footer');
    }

    /**
     * Dislay list of match
     * @return view
     */
    public function test2()
    {
        $datas = array();        

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('test/test2', $datas);
        echo view('innerpages/footer');
    }
}
