<?php

namespace App\Models;

use CodeIgniter\Model;

class Club extends Model
{
    protected $table = 'club';

    protected $db;

    protected $allowedFields = [];

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Called during initialization. Appends
     * our custom field to the module's model.
     */
    protected function initialize()
    {
        $db = \Config\Database::connect();
    }

    public function getClubWithAjaxSearch($search)
    {
        $search = '%' . $search . '%';        
        
        $sql = $this->db->query('
            SELECT
                c.club_id,
                c.name,
                "club" as type
            FROM
                `club` AS c
            WHERE 
                c.name LIKE ?
        ', [$search]);
        
        return $sql->getResult();
    }
}