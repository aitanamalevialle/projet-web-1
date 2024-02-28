<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Condition extends \Core\Model
{
    
    protected $table = 'condition';
    protected $primaryKey = 'id';
    protected $fillable = ['condition'];

}