<?php

namespace App\Controllers;

use \Core\View;

/**
 * Enchere controller
 *
 * PHP version 7.0
 */
class Favoris extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['utilisateur_id'])) {
            $utilisateur_id = $_SESSION['utilisateur_id'];
            
            $favoris = \App\Models\Favoris::getAll($utilisateur_id);
            View::renderTemplate('Favoris/index.html', ["favoris" => $favoris]);
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    }

    public function deleteAction()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $favorisId = $_POST['id'];

            if (\App\Models\CRUD::delete('favoris', $favorisId)) {
                header("Location: /stampee/public/favoris/index");
                exit();
            } else {
                echo "La suppression a échoué.";
            }
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

}

?>