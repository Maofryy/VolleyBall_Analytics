<?php

namespace App\Models;

use CodeIgniter\Model;

class Team extends Model
{
    protected $table = 'team';

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
}