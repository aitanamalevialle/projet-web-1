<?php

namespace Core\Library;

use \Core\View;

class CheckSession {

    static public function sessionAuth(){
        if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'])){
            return true;
        }else{
            View::renderTemplate('Login/index.html');
            exit();
        }
    }

    static public function sessionExists()
    {
        return isset($_SESSION['fingerPrint']);
    }
    
}
