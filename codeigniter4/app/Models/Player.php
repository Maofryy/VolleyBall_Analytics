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
        $sql = $this->db->query("
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
        ");
        return $sql->getResult();
    }

    /**
     * Retourne les joueurs d'une équipe 
     */
    public function getPlayersFromTeam($team_id)
    {
        $sql = $this->db->query("
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
        ", [$team_id]);
        return $sql->getResult();
    }

    /**
     * 
     */
    public function getPlayersFromMatch($match_id, $team_id = null)
    {

        $query = "
        SELECT 
            p.licence,
            p.first_name,
            p.last_name,
            tp.number,
            op.function_id
        FROM `match` AS m
        INNER JOIN team_player tp ON tp.match_id = m.match_id
        INNER JOIN player p on tp.player_id = p.licence
        LEFT JOIN match_other_player op ON p.licence = op.licence
        WHERE m.match_number = ?
        ";
        $params = array($match_id);

        if (!empty($team_id))
        {
            $query .= ' AND tp.team_id = ?';
            $params[] = $team_id;
        }
        $query .= "
        group by p.licence
        ORDER BY tp.number;";
        $sql = $this->db->query($query, $params);        
        return $sql->getResult();
    }

    public function getPlayerWithAjaxSearch($search)
    {
        $search = '%' . $search . '%';        
        
        $sql = $this->db->query('
            SELECT
                p.licence,
                p.first_name,
                p.last_name,
                "player" as type
            FROM
                `player` AS p
            WHERE 
                p.licence LIKE ?
                OR CONCAT(p.first_name, " ", p.last_name) LIKE ?
                OR CONCAT(p.last_name, " ", p.first_name) LIKE ?
            GROUP BY p.licence;
        ', [$search, $search, $search]);
        
        return $sql->getResult();
    }
}