<?php

namespace App\Controllers;

use \Core\View;
use \Core\Library\Validation;
use \App\Models\CRUD;

/**
 * Enchere controller
 *
 * PHP version 7.0
 */
class Enchere extends \Core\Controller
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
            $encheres = \App\Models\Enchere::getAll($utilisateur_id);
            View::renderTemplate('Enchere/index.html', ["encheres" => $encheres]);
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    }

    /**
     * Show the create page
     *
     * @return void
     */
    public function createAction()
    {

        $timbres = CRUD::getAllForUser('timbre', $_SESSION['utilisateur_id']);
        
        if (!empty($_POST)) {

            if (isset($_SESSION['utilisateur_id'])) {
                $_POST['utilisateur_id'] = $_SESSION['utilisateur_id'];
            }

            $validation = new Validation();
            $validation->name('timbre_id')->value($_POST['timbre_id'])->required()->pattern('int');
            $validation->name('debut_enchère')->value($_POST['date_debut'])->required()->pattern('date_ymd');
            $validation->name('fin enchère')->value($_POST['date_fin'])->required()->pattern('date_ymd');
            $validation->name('prix plancher')->value($_POST['prix_plancher'])->required()->pattern('int');
            $validation->name('quantité')->value($_POST['quantite'])->required()->pattern('int');
            $validation->name('coup de coeur')->value($_POST['coup_de_coeur'])->required()->pattern('int');

            if (!$validation->isSuccess()) {
                $errors = $validation->displayErrors();
                View::renderTemplate('Enchere/create.html', ['errors' => $errors, 'timbres' => $timbres]);
                die();
            }            

            $encheres = \App\Models\CRUD::insert('enchere', $_POST);
            header("location:/stampee/public/enchere/index");
            die();
        }

        View::renderTemplate('Enchere/create.html',['timbres' => $timbres]);
    }

    /**
     * Show the auction/stamp page
     *
     * @return void
     */
    public function showAction()
    {

        if (isset($_GET['enchere_id'])) {
            $enchere_id = $_GET['enchere_id'];
    
            $enchere = CRUD::getById('enchere', $enchere_id);

        $encheres = \App\Models\Enchere::joinEnchereWithTimbre();

        $enchere = null;
        foreach ($encheres as $enchereData) {
            if ($enchereData['id'] == $enchere_id) {
                $enchere = $enchereData;
                break;
            }
        }

            if ($enchere) {
                $timbre_id = $enchere['timbre_id'];
                $timbre = CRUD::getById('timbre', $timbre_id);
        
                if ($timbre) {
                    $condition_id = $timbre['condition_id'];
        
                    if ($condition_id !== null) {
                        $condition = CRUD::getById('condition', $condition_id);
        
                        if ($condition) {
                            $utilisateurAcreerEnchere = isset($_SESSION['utilisateur_id']) ? $this->auteurEnchere($_SESSION['utilisateur_id'], $enchere_id) : false;
        
                            $derniereMise = \App\Models\Mise::getLastMiseForEnchere($enchere_id);
                            $minMise = $derniereMise !== false ? $derniereMise['prix_actuel'] : $enchere['prix_plancher'];
        
                            View::renderTemplate('Enchere/show.html', [
                                "enchere" => $enchere,
                                "timbre" => $timbre,
                                "condition" => $condition,
                                "derniereMise" => $derniereMise['prix_actuel'] ?? null,
                                "utilisateurAcreerEnchere" => $utilisateurAcreerEnchere,
                                "minMise" => $minMise 
                            ]);
                        } else {
                            echo "Condition non trouvée.";
                        }
                    }
                } else {
                    echo "Timbre non trouvé.";
                }
            } else {
                echo "Enchère non trouvée.";
            }
        
        } else {
            echo "ID de l'enchère non spécifié.";
        }
    }

    /**
     * Check if the user is the owner of the auction
     */

    private function auteurEnchere($utilisateur_id, $enchere_id)
    {
        $enchere = CRUD::getById('enchere', $enchere_id);
        return ($enchere && $enchere['utilisateur_id'] == $utilisateur_id);
    }
    
    /**
     * Bid
     */
    public function miseAction()
    {
        if (isset($_SESSION['utilisateur_id'])) {
            $utilisateur_id = $_SESSION['utilisateur_id'];
            $enchere_id = $_POST['enchere_id'];
    
            $data = [
                'enchere_id' => $enchere_id,
                'utilisateur_id' => $utilisateur_id,
                'prix_actuel' => $_POST['prix_actuel'],
                'date' => date('Y-m-d H:i:s')
            ];
    
            if ($this->prixActuelSuperieurAuxMises($enchere_id, $_POST['prix_actuel'])) {
                \App\Models\CRUD::insert('mise', $data);

                header("Location: /stampee/public/enchere/show?enchere_id=" . $enchere_id);
                exit();
            }
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    }

    /**
     * Add an auction to favorites
     */
    public function favorisAction()
    {
        if (isset($_SESSION['utilisateur_id'])) {
            $utilisateur_id = $_SESSION['utilisateur_id'];
            $enchere_id = $_POST['enchere_id'];
    
            $data = [
                'enchere_id' => $enchere_id,
                'utilisateur_id' => $utilisateur_id,
            ];

            \App\Models\CRUD::insert('favoris', $data);
            header("Location: /stampee/public/enchere/show?enchere_id=" . $enchere_id);
            exit();
        } else {
            header("Location: /stampee/public/login");
            exit();
        }
    } 

    /**
     * Check if the user has already bid
     */
    private function utilisateurAdejaMise($utilisateur_id, $enchere_id)
    {
        $mises = \App\Models\Mise::getMisesUtilisateurEnchere($utilisateur_id, $enchere_id);
        return !empty($mises);
    }
    
    /**
     * Compare the prices of the bids
     */
    private function prixActuelSuperieurAuxMises($enchere_id, $prix_actuel)
    {
        $mises = \App\Models\Mise::getMisesEnchere($enchere_id);
    
        foreach ($mises as $mise) {
            if ($mise['prix_actuel'] >= $prix_actuel) {
                return false;
            }
        }
    
        return true;
    }

    /**
     * Delete the auction
     */
    public function deleteAction()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $enchereId = $_POST['id'];

            if (\App\Models\CRUD::delete('enchere', $enchereId)) {
                header("Location: /stampee/public/enchere/index");
                exit();
            } else {
                echo "La suppression a échoué.";
            }
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

    /**
     * Edit the form
     */
    public function editAction()
    {
        $timbres = CRUD::getAll('timbre');
        $enchere_id = $_GET['enchere_id'];
        $enchere = CRUD::getById('enchere', $enchere_id);
        View::renderTemplate('Enchere/update.html', ["enchere" => $enchere, 'timbres' => $timbres]);
    }

    /**
     * Show the update page
     *
     * @return void
     */
    public function updateAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $enchereId = $_POST['id'];

            if (isset($_SESSION['utilisateur_id'])) {
                $_POST['utilisateur_id'] = $_SESSION['utilisateur_id'];
            }

            $validation = new Validation();
            $validation->name('timbre_id')->value($_POST['timbre_id'])->required()->pattern('int');
            $validation->name('debut_enchere')->value($_POST['date_debut'])->required()->pattern('date_ymd');
            $validation->name('fin_enchere')->value($_POST['date_fin'])->required()->pattern('date_ymd');
            $validation->name('prix_plancher')->value($_POST['prix_plancher'])->required()->pattern('int');
            $validation->name('quantite')->value($_POST['quantite'])->required()->pattern('int');
            $validation->name('coup_de_coeur')->value($_POST['coup_de_coeur'])->required()->pattern('int');

            if (!$validation->isSuccess()) {
                $errors = $validation->displayErrors();
                View::renderTemplate('Enchere/create.html', ['errors' => $errors, 'timbres' => $timbres]);
                die();
            }   

            $enchereData = [
                'timbre_id' => $_POST['timbre_id'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'prix_plancher' => $_POST['prix_plancher'],
                'quantite' => $_POST['quantite'],
                'coup_de_coeur' => $_POST['coup_de_coeur'],
                'utilisateur_id' => $_POST['utilisateur_id']
            ];

            \App\Models\CRUD::update('enchere', $enchereData, $enchereId);
            header("Location: /stampee/public/enchere/index");
            die();
        } else {
            echo "ID non fourni ou méthode non autorisée.";
        }
    }

}

?>