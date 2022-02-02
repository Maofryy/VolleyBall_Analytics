<?php

namespace App\Controllers;
use CodeIgniter\Model\Player;

class Players extends BaseController
{

    public function index()
    {
        try {
            $datas = array();

            $playerModel = new \App\Models\Player();
            $datas['players'] = $playerModel->getPlayersList();

        } catch (\Exception $e) {
            die($e->getMessage());
        }
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('player/list', $datas);
        echo view('innerpages/footer');
    }

    public function view($licence)
    {
        $datas = array();
        
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('player/view', $datas);
        echo view('innerpages/footer');
    }
}
