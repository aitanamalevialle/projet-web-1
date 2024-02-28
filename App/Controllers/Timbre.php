<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\CRUD;
use \App\Models\Image;
use \Core\Library\Validation;

/**
 * Timbre controller
 *
 * PHP version 7.0
 */
class Timbre extends \Core\Controller
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
            $timbres = \App\Models\Timbre::getAll($utilisateur_id);

            View::renderTemplate('Timbre/index.html', ["timbres" => $timbres]);
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    }

    public function deleteAction()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $timbreId = $_POST['id'];

            if (\App\Models\CRUD::delete('timbre', $timbreId)) {
                header("Location: /stampee/public/timbre/index");
                exit();
            } else {
                echo "La suppression a échoué.";
            }
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

    /**
     * Show the create page
     *
     * @return void
     */
    public function createAction()
    {
        $conditions = CRUD::getAll('condition');
        $origines = CRUD::getAll('origine');

        if (!empty($_POST)) {

            if (isset($_SESSION['utilisateur_id'])) {
                $_POST['utilisateur_id'] = $_SESSION['utilisateur_id'];
            }

            $validation = new Validation();
            $validation->name('nom')->value($_POST['nom'])->max(50)->min(2)->required();
            $validation->name('date creation')->value($_POST['date_creation'])->required()->pattern('date_ymd');
            $validation->name('couleurs')->value($_POST['couleurs'])->max(50)->min(2)->required();
            $validation->name('tirage')->value($_POST['tirage'])->required()->pattern('int');
            $validation->name('dimensions')->value($_POST['dimensions'])->max(50)->min(2)->required();
            $validation->name('certifie')->value($_POST['certifie'])->required()->pattern('int');
            $validation->name('pays')->value($_POST['pays'])->max(50)->min(5)->required();

            if (!$validation->isSuccess()) {
                $errors = $validation->displayErrors();
                View::renderTemplate('Timbre/create.html', ['errors' => $errors, 'conditions' => $conditions, 'origines' => $origines]);
                die();
            }

            $timbreData = [
                'nom' => $_POST['nom'],
                'date_creation' => $_POST['date_creation'],
                'couleurs' => $_POST['couleurs'],
                'tirage' => $_POST['tirage'],
                'dimensions' => $_POST['dimensions'],
                'certifie' => $_POST['certifie'],
                'pays' => $_POST['pays'],
                'condition_id' => $_POST['condition_id'],
                'utilisateur_id' => $_POST['utilisateur_id'],
                'origine_id' => $_POST['origine_id'],
            ];

            $timbreId = \App\Models\CRUD::insert('timbre', $timbreData);

            $imageModel = new Image();
            $imageIds = $imageModel->uploadImages($_FILES, $timbreId);

            header("Location: /stampee/public/timbre/index");
            exit();
        }

        View::renderTemplate('Timbre/create.html', ['conditions' => $conditions, 'origines' => $origines]);
    }

    public function editAction()
    {
        $conditions = CRUD::getAll('condition');
        $origines = CRUD::getAll('origine');
        $timbre_id = $_GET['timbre_id'];
        $timbre = CRUD::getById('timbre', $timbre_id);
        View::renderTemplate('Timbre/update.html', ["timbre" => $timbre, 'conditions' => $conditions, 'origines' => $origines]);
    }

    /**
     * Show the update page
     *
     * @return void
     */
    public function updateAction()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $timbreId = $_POST['id'];

            if (isset($_SESSION['utilisateur_id'])) {
                $_POST['utilisateur_id'] = $_SESSION['utilisateur_id'];
            }

            $timbreData = [
                'nom' => $_POST['nom'],
                'date_creation' => $_POST['date_creation'],
                'couleurs' => $_POST['couleurs'],
                'tirage' => $_POST['tirage'],
                'dimensions' => $_POST['dimensions'],
                'certifie' => $_POST['certifie'],
                'pays' => $_POST['pays'],
                'condition_id' => $_POST['condition_id'],
                'utilisateur_id' => $_POST['utilisateur_id'],
                'origine_id' => $_POST['origine_id'],
            ];

            \App\Models\CRUD::update('timbre', $timbreData, $timbreId);
            
            header("Location: /stampee/public/timbre/index");
            die();
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

}