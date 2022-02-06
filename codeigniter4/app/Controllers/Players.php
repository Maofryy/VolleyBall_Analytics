<?php

namespace App\Controllers;
use CodeIgniter\Model\Player;

class Players extends BaseController
{

    /*public function index()
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
    }*/

    public function view($licence)
    {
        $datas = array();
        
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('player/view', $datas);
        echo view('innerpages/footer');
    }
    
    public function index()
    {
        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('player/list2');
        echo view('innerpages/footer');
    }

    public function playerListAjax()
    {
        $datas = [];
        $page = intval($this->request->getVar('page'));
        $sort = $this->request->getVar('sort');
        $perPage = intval($this->request->getVar('per_page'));
      
        $playerModel = new \App\Models\Player();
        $datas['data'] = $playerModel->getPlayersListAjax($page, $sort, $perPage);
        $datas['total'] = 100;
        $datas['per_page'] = $perPage;
        $datas['current_page'] = $page;
        $datas['last_page'] = 12;
        $datas['next_page_url'] = base_url() . '/public/codeigniter4/Players/playerListAjax?page=' . $page + 1;// check if exist
        $datas['next_prev_url'] = null;
        $datas['from'] = 1;
        $datas['to'] = 15;

        return json_encode($datas);
    }
}
