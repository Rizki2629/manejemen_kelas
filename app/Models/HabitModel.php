<?php

namespace App\Models;

use CodeIgniter\Model;

class HabitModel extends Model
{
    protected $table            = 'habits';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['code','name','description','input_type'];
    protected $useTimestamps    = true;
}
