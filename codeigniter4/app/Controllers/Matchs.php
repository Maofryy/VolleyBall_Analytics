<?php

namespace App\Controllers;

class Matchs extends BaseController
{
    public function index()
    {
        try {
            $db = \Config\Database::connect();
            $sql = $db->query('SELECT * FROM match');
            $matchs = $sql->getResult();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return view('matchs', $matchs);
    }
}
