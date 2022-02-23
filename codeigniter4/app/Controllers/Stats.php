<?php

namespace App\Controllers;

class Stats extends BaseController
{
    public function getPlayerPositionStats($match_id, $player_id)
    {
        $matchsModel = new \App\Models\Matchs();
        $playerModel = new \App\Models\Player();

        $datas = $matchsModel->getPlayerPositionStats($match_id, $player_id);

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('Stats/test', $datas);
        echo view('innerpages/footer');
    }
}
