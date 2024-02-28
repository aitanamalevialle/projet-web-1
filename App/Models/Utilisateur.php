<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Utilisateur extends \Core\Model
{
    
    protected $table = 'utilisateur';
    protected $primaryKey = 'id';
    protected $fillable = ['nom', 'courriel', 'mot_de_passe', 'privilege_id'];
    
    public static function checkUser($courriel, $mot_de_passe = null) {
        $db = static::getDB(); 
        $stmt = $db->prepare('SELECT * FROM utilisateur WHERE courriel = ?');
        $stmt->execute([$courriel]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $mot_de_passe !== null) {
            $salt = "H3@_l?a";
            $saltPassword = $mot_de_passe . $salt;
    
                if (password_verify($saltPassword, $user['mot_de_passe'])) {
                    session_regenerate_id();
                    $_SESSION['utilisateur_id'] = $user['id'];
                    $_SESSION['courriel'] = $user['courriel'];
                    $_SESSION['privilege'] = $user['privilege_id'];
                    $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
                    return true;
                } else {
                    $errors = "<ul><li>Vérifiez le mot de passe</li></ul>";
                    return $errors;
                }

        } else {
            $errors = "<ul><li>Vérifiez le nom d'utilisateur</li></ul>";
            return $errors; 
        }
    }

}