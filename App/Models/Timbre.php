<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Timbre extends \Core\Model
{

    protected $table = 'timbre';
    protected $primaryKey = 'id';
    protected $fillable = ['nom', 'date_creation', 'couleurs', 'tirage', 'dimensions', 'certifie', 'pays', 'condition_id', 'utilisateur_id', 'origine_id'];
    
    public static function getAll($utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM timbre WHERE utilisateur_id = :utilisateur_id');
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}