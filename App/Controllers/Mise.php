<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\CRUD;
use \Core\Library\Validation;

/**
 * Mise controller
 *
 * PHP version 7.0
 */
class Mise extends \Core\Controller {
    
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['utilisateur_id'])) {
            $utilisateur_id = $_SESSION['utilisateur_id'];
            $mises = \App\Models\Mise::joinMiseWithEnchere($utilisateur_id);
            View::renderTemplate('Mise/index.html', ["mises" => $mises]);
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    }
    
}

?>