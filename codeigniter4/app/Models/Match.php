<?php

namespace App\Models;

class Match extends Model
{
    protected $table = 'match';

    /**
     * Called during initialization. Appends
     * our custom field to the module's model.
     */
    protected function initialize()
    {
        $this->allowedFields[] = 'middlename';
    }
}