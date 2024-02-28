<?php

namespace App\Controllers;
use \Core\View;
use \Core\Library\CheckSession;
use \Core\Library\Validation;
use App\Models\Utilisateur;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller {

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(){
        View::renderTemplate('Login/index.html');
    }

    public function auth() {

        $validation = new Validation();
        $validation->name('courriel')->value($_POST['courriel'])->max(50)->required()->pattern('email');
        $validation->name('mot de passe')->value($_POST['mot_de_passe'])->max(20)->min(6);

        if (!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            View::renderTemplate('Login/index.html', ['errors' => $errors, 'utilisateur' => $_POST]);
            exit();
        }

        $utilisateur = new Utilisateur;
        $checkUser = $utilisateur->checkUser($_POST['courriel'], $_POST['mot_de_passe']);
        
        if ($checkUser === true) {
            header("Location: /stampee/public/compte");
            exit();
        } else {
            View::renderTemplate('Login/index.php', ['errors' => $checkUser, 'utilisateur' => $_POST]);
        }
    }
    
    public function logout(){
        session_destroy();
        header("Location: /stampee/public");
        exit();
    }

}

?>