<?php

namespace App\Controllers;

class Matchs extends BaseController
{
    public function index()
    {
        try {
            $this->load->database();
            $sql = $this->database->query('SELECT * FROM ligue');
            $matchs = $sql->result();
            var_dump('allo ?');die;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return view('matchs', $matchs);
    }
}
