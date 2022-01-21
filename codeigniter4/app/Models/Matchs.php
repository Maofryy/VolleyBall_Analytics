<?php

namespace App\Models;

use CodeIgniter\Model;

class Matchs extends Model
{
    protected $table = 'match';

    protected $db;

    /**
     * Called during initialization. Appends
     * our custom field to the module's model.
     */
    protected function initialize()
    {
        $db = \Config\Database::connect();
        $this->allowedFields[] = 'middlename';
    }

    /**
     * Display list of match
     */
    public function getMatchsList()
    {
        $sql = $this->db->query("
        SELECT
            m.match_number,
            m.city,
            m.gym,
            DATE_FORMAT( m.date_match,'%d/%m/%Y') as date_match,
            t1.name as team1name,
            t2.name as team2name
        FROM
            `match` m
            INNER JOIN team t1 ON m.team_home_id = t1.team_id
            INNER JOIN team t2 ON m.team_out_id = t2.team_id;
        ");
        return $sql->getResult();
    }

    /**
     * Display data from one match
     */
    public function getMatch($code)
    {
        $sql = $this->db->query("
        SELECT
            m.match_number,
            m.match_day,
            m.city,
            m.gym,
            m.ligue,
            date_match,
            t1.name as team1name,
            t2.name as team2name,
            t1.team_id as t1id,
            t2.team_id as t2id
        FROM
            `match` m
            INNER JOIN team t1 ON m.team_home_id = t1.team_id
            INNER JOIN team t2 ON m.team_out_id = t2.team_id
        WHERE m.match_number = ?
        ", [$code]);
        return $sql->getRow();
    }
}