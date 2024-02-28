<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Privilege extends \Core\Model
{
    
    protected $table = 'privilege';
    protected $primaryKey = 'id';
    protected $fillable = ['privilege'];

}