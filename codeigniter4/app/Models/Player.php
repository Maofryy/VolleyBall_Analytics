<?php

namespace App\Models;

use CodeIgniter\Model;

class Player extends Model
{
    protected $table = 'player';

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

    public function getPlayersList()
    {
        $sql = $this->db->query('
        SELECT
            licence,
            first_name,
            last_name,
            tp.number,
            t.name as team_name
        FROM
            `player` AS p
        LEFT JOIN team_player tp ON
            p.licence = tp.player_id
        LEFT JOIN team t ON
            tp.team_id = t.team_id;
        ');
        return $sql->getResult();
    }

    public function getPlayersFromTeam($team_id)
    {
        $sql = $this->db->query('
        SELECT
            licence,
            first_name,
            last_name,
            tp.number
        FROM
            `player` AS p
        LEFT JOIN team_player tp ON
            p.licence = tp.player_id
       WHERE team_id = ?;
        ', [$team_id]);
        return $sql->getResult();
    }
}