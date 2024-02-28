<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Mise extends \Core\Model
{
    protected $table = 'mise';
    protected $fillable = ['enchere_id', 'utilisateur_id', 'prix_actuel', 'date'];

    public static function getAll($utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM mise WHERE utilisateur_id = :utilisateur_id');
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function joinMiseWithEnchere($utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare(
        "SELECT mise.*, enchere.*
        FROM mise
        JOIN enchere ON mise.enchere_id = enchere.id
        WHERE mise.utilisateur_id = :utilisateur_id"
        );
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMisesUtilisateurEnchere($utilisateur_id, $enchere_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM mise WHERE utilisateur_id = :utilisateur_id AND enchere_id = :enchere_id');
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':enchere_id', $enchere_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMisesEnchere($enchere_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM mise WHERE enchere_id = :enchere_id');
        $stmt->bindParam(':enchere_id', $enchere_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getLastMiseForEnchere($enchere_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT prix_actuel FROM mise WHERE enchere_id = :enchere_id ORDER BY date DESC LIMIT 1');
        $stmt->bindParam(':enchere_id', $enchere_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}