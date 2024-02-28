<?php

namespace App\Controllers;

use \Core\View;

/**
 * Compte controller
 *
 * PHP version 7.0
 */
class Compte extends \Core\Controller {
    
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(){
        View::renderTemplate('Compte/index.html');
    }
    
}

?>