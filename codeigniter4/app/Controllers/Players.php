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
        return view('players', $datas);
    }
}
