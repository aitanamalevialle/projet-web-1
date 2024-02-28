<?php

namespace App\Controllers;

use \Core\View;

/**
 * Catalogue controller
 *
 * PHP version 7.0
 */
class Catalogue extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $encheres = \App\Models\Enchere::joinEnchereWithTimbre();
        View::renderTemplate('Catalogue/index.html', ["encheres" => $encheres]);
    }

}

?>