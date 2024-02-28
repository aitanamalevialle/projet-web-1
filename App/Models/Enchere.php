<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Enchere extends \Core\Model
{
    protected $table = 'enchere';
    protected $primaryKey = 'id';
    protected $fillable = ['timbre_id, date_debut', 'date_fin', 'prix_plancher', 'quantite', 'coup_de_coeur', 'utilisateur_id'];

    public static function getAll($utilisateur_id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM enchere WHERE utilisateur_id = :utilisateur_id');
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEnchereWithLastMise()
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT enchere.id as enchere_id, enchere.date_debut, enchere.date_fin, enchere.prix_plancher, enchere.quantite, enchere.coup_de_coeur,
        timbre.*, utilisateur.nom as utilisateur_nom,
        condition.condition as condition_nom,
        mise.prix_actuel, mise.date as date_derniere_mise
        FROM enchere
        JOIN timbre ON enchere.timbre_id = timbre.id
        JOIN utilisateur ON enchere.utilisateur_id = utilisateur.id
        JOIN `condition` ON timbre.condition_id = condition.id
        LEFT JOIN mise ON enchere.id = mise.enchere_id
        WHERE mise.date = (SELECT MAX(date) FROM mise WHERE enchere_id = enchere.id);
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function joinEnchereWithTimbre()
    {
        $db = static::getDB();
        $stmt = $db->prepare("
        SELECT enchere.*, timbre.nom AS nom_timbre, image.nom AS nom_image
        FROM enchere
        JOIN timbre ON enchere.timbre_id = timbre.id
        LEFT JOIN image ON timbre.id = image.timbre_id
        WHERE image.timbre_id IS NOT NULL
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}