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
            t2.team_id as t2id,
            m.winner_team_id,
            m.score
        FROM
            `match` m
            INNER JOIN team t1 ON m.team_home_id = t1.team_id
            INNER JOIN team t2 ON m.team_out_id = t2.team_id
        WHERE m.match_number = ?
        ", [$code]);
        return $sql->getRow();
    }

    /**
     * get Subsitutions
     */
    public function getSubstitutions($code)
    {
        $query = '
            SELECT 
                ss.set,
                ss.licence_in,
                ss.licence_out,
                ss.score,
                ss.team_id,
                tpin.number,
                tpout.number,
                ss.team_id
            FROM `match_set_substitution` ss
            INNER JOIN player pin ON pin.licence = ss.licence_in
            INNER JOIN player pout ON pout.licence = ss.licence_out
            INNER JOIN `match` m ON ss.match_id = m.match_number
            INNER JOIN team_player tpin ON m.match_number = tpin.match_id AND tpin.player_id = pin.licence
            INNER JOIN team_player tpout ON m.match_number = tpout.match_id AND tpout.player_id = pin.licence
            WHERE m.match_number = ?
        ';
        $params = [$code];
        $sql = $this->db->query($query, $params);        
        return $sql->getResult();
    }


    public function getPlayerPositionStats($match_id, $player_id)
    {
        $positions = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0
        );

        $sets = array(
            'set 1' => $positions,
            'set 2' => $positions,
            'set 3' => $positions,
            'set 4' => $positions,
            'set 5' => $positions,
        );

        // On récupère de quel équipe fait partie le joueur pour ce match
        $query = '
            SELECT team_id
            FROM `match` m 
            INNER JOIN team_player tp ON m.match_id= tp.match_id
            WHERE m.match_number = ?
            AND tp.player_id = ?
        ';

        $params = array();
        $params[] = $match_id;
        $params[] = $player_id;
        $sql = $this->db->query($query, $params);
        $res = $sql->getRow();
        
        if (!empty($res))
        {
            $team_id = $res->team_id;
        }  
     
        // On récupère les postitions de départ pour chaque sets
        $query = '
            SELECT match_id, `set`, position_1, position_2, position_3, position_4, position_5, position_6
            FROM `match_set_position` msp
            WHERE msp.team_id = ?
        ';

        $params = array();
        $params[] = $team_id;
        $sql = $this->db->query($query, $params);
        $res = $sql->getResultArray();
        $startPos = array(
            'set 1' => false,
            'set 2' => false,
            'set 3' => false,
            'set 4' => false,
            'set 5' => false,
        );

        foreach($res as $key => $startPosition)
        {
            $pos = array_search($player_id, $startPosition);
            if ($pos)
            {
                $sets['set ' . strval($key + 1)][substr($pos, -1)]++;
                $startPos['set ' . strval($key + 1)] = intval(substr($pos, -1));
            }
        }

        // On récupère les rotations !
        $query = "
            SELECT `set`, `point`, team_id
            FROM `match` m 
            INNER JOIN match_set_rotation msr ON msr.match_id = m.match_id
            WHERE m.match_number = ?
            order BY `set`, `point`;
        ";

        $params = array();
        $params[] = $match_id;
        $sql = $this->db->query($query, $params);
        $res = $sql->getResultArray();

        $last_point = 0;
        $last_pos = $startPos;
        $diff = 0;

        foreach ($res as $key => $position)
        {
            if ($position['team_id'] == $team_id)
            {
                if (intval($position['point']) - $last_point < 0 )
                {
                  $last_point = 0; // on reset à 0 au changement de set
                }
                else
                {
                    var_dump(intval($position['point']) - $last_point);
                }
                $sets['set ' . $position['set']][$last_pos['set ' . $position['set']]] += intval($position['point']) - $last_point;
                $last_pos['set ' . $position['set']] += 1;
                if ($last_pos['set ' . $position['set']] == 7)
                {
                    $last_pos['set ' . $position['set']] = 1;
                }
                $last_point = intval($position['point']);
            }
        }

        $front_or_back = array();
        foreach ($sets as $set_number => $set)
        {
            foreach ($set as $key => $point)
            {
                if (in_array($key, [2,3,4]))
                {
                    if (!isset($front_or_back[$set_number]))
                    {
                        $front_or_back[$set_number] = array();
                    }
                    if (!isset($front_or_back[$set_number]['front']))
                    {
                        $front_or_back[$set_number]['front'] = 0;
                    }
                    $front_or_back[$set_number]['front'] += $point;
                }
                else
                {
                    if (!isset($front_or_back[$set_number]))
                    {
                        $front_or_back[$set_number] = array();
                    }
                    if (!isset($front_or_back[$set_number]['back']))
                    {
                        $front_or_back[$set_number]['back'] = 0;
                    }
                    $front_or_back[$set_number]['back'] += $point;
                }
                
            }
        }
       
        $response = array(
            'sets' => $sets,
            'front_or_back' => $front_or_back
        );
        return $response;
    }
}