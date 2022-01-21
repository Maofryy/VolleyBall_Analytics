<?php

namespace App\Controllers;

class Matchs extends BaseController
{
    /**
     * Dislay list of match
     * @return view
     */
    public function index()
    {
        $datas = array();

        $matchsModel = new \App\Models\Matchs();
        
        $datas['matchs'] = $matchsModel->getMatchsList();

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('match/list', $datas);
        echo view('innerpages/footer');
    }

    /**
     * Display detail of one match
     * @return view
     */
    public function view($code)
    {
      
        $datas = array();

        $matchsModel = new \App\Models\Matchs();
        $playerModel = new \App\Models\Player();

        $datas['match'] = $matchsModel->getMatch($code);
        $teams_id = array($datas['match']->t1id, $datas['match']->t2id);
        foreach($teams_id as $key => $id)
        {
            var_dump($id);
            $datas['team' . $key+1] = $playerModel->getPlayersFromTeam($id);
        }
       
        $datas['title'] = 'Match ' . $code;
        $datas['pathpdf'] = $this->getPathMatchPdf($code);        

        // reformat date
        $date = \DateTime::createFromFormat("Y-m-d H:i:s", $datas['match']->date_match);
        $datas['match']->date_match = $date->format('l d F') . ' à ' . $date->format('H') . 'h' . $date->format('i');

        echo view('innerpages/header');
        echo view('innerpages/menu');
        echo view('innerpages/top_header');
        echo view('match/view', $datas);
        echo view('innerpages/footer');
    }

    /**
     * Return path file from code match
     * @return string
     */
    private function getPathMatchPdf($code)
    {
        $path = '';
        $pdfs = $this->getDirContents('../../data');
        foreach ($pdfs as $pdf)
        {
            if (strpos($pdf, $code))
            {
                $path = substr($pdf, -53);
            }
        }

        return $path;
    }

    /**
     * Should be in helper folder
     */
    private function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }
    
        return $results;
    }
}
