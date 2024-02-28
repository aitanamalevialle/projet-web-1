<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Favoris extends \Core\Model
{
    protected $table = 'favoris';
    protected $primaryKey = 'id';
    protected $fillable = ['enchere_id', 'utilisateur_id'];
    
    public static function getAll($utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM favoris WHERE utilisateur_id = :utilisateur_id');
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>