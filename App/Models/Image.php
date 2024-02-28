<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Image extends \Core\Model
{
    
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['nom', 'principale', 'timbre_id'];

    /**
     * Get all the images as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT image.*, timbre.nom AS timbre_nom FROM image LEFT JOIN timbre ON image.timbre_id = timbre.id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function uploadImages($files, $timbreId)
    {
        $db = static::getDB();
        $imageIds = [];
        
        if (isset($files['images'])) {
            foreach ($files['images']['name'] as $index => $imageName) {
                $uploadFile = $_SERVER['DOCUMENT_ROOT'] . '/stampee/public/assets/img/' . $imageName;

                if (move_uploaded_file($files['images']['tmp_name'][$index], $uploadFile)) {
                    $stmt = $db->prepare('INSERT INTO image(nom, principale, timbre_id) VALUES (:nom, :principale, :timbre_id)');
                    $stmt->execute(['nom' => $imageName, 'principale' => 1, 'timbre_id' => $timbreId]);

                    $imageIds[] = $db->lastInsertId();
                } else {
                    echo "Erreur lors du téléchargement de l'image.";
                    die();
                }
            }
        }

        return $imageIds;
    }

}