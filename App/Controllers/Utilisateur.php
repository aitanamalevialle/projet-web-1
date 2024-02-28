<?php

namespace App\Controllers;

use \Core\View;
use \Core\Library\CheckSession;
use \Core\Library\Validation;
use \App\Models\CRUD;

/**
 * Utilisateur controller
 *
 * PHP version 7.0
 */
class Utilisateur extends \Core\Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {

        if (CheckSession::sessionExists() && ($_SESSION['privilege'] != 1 && $_SESSION['privilege'] != 2)) {
            View::renderTemplate('Login/index.html');
            exit();
        }
        
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $utilisateurs = CRUD::getAll('utilisateur');
        View::renderTemplate('Utilisateur/index.html', ["utilisateurs" => $utilisateurs]);
    }

    /**
     * Show the create page
     *
     * @return void
     */
    public function createAction()
    {
        $privileges = CRUD::getAll('privilege');

        if (!empty($_POST)) {

            $_POST['privilege_id'] = 2;

            $validation = new Validation();
            $validation->name('nom')->value($_POST['nom'])->max(50)->min(5)->required();
            $validation->name('courriel')->value($_POST['courriel'])->max(50)->required()->pattern('email');
            $validation->name('mot de passe')->value($_POST['mot_de_passe'])->max(255)->min(5)->required();
            
            if (!$validation->isSuccess()) {
                $errors = $validation->displayErrors();
                View::renderTemplate('Utilisateur/create.html', ['errors' => $errors]);
                die();
            }

            $data['privilege_id'] = 2;

            $options = [
                'cost' => 10
            ];
            $salt = "H3@_l?a";
            $passwordSalt = $_POST['mot_de_passe'] . $salt;
            $hashedPassword = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);

            $_POST['mot_de_passe'] = $hashedPassword;

            $utilisateurs = \App\Models\CRUD::insert('utilisateur', $_POST);
            header("location:/stampee/public/");
            die();
        }

        View::renderTemplate('Utilisateur/create.html', ['privileges' => $privileges]);
    }
    
    public function deleteAction()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $utilisateurId = $_POST['id'];

            if (\App\Models\CRUD::delete('utilisateur', $utilisateurId)) {
                header("Location: /stampee/public/utilisateur/index");
                exit();
            } else {
                echo "La suppression a échoué.";
            }
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

}