<?php

namespace App\Controllers;

class Search extends BaseController
{
    public function global()
    {
        $response = array();
        $search = $this->request->getVar('srsearch');
        
        $playerModel = new \App\Models\Player();
        $matchsModel = new \App\Models\Matchs();
        $clubModel = new \App\Models\Club();
        $teamModel = new \App\Models\Team();

        $resultPlayers = $playerModel->getPlayerWithAjaxSearch($search);
        $resultClubs = $clubModel->getClubWithAjaxSearch($search);

        $response = array_merge($resultPlayers, $resultClubs);
        
        return json_encode($response);
    }
}
