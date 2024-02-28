<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Session
 */
session_write_close();
session_start();

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('catalogue', ['controller' => 'Catalogue', 'action' => 'index']);
$router->add('enchere', ['controller' => 'Enchere', 'action' => 'index']);
$router->add('timbre', ['controller' => 'Timbre', 'action' => 'index']);
$router->add('utilisateur', ['controller' => 'Utilisateur', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'index']);
$router->add('compte', ['controller' => 'Compte', 'action' => 'index']);
$router->add('mise', ['controller' => 'Mise', 'action' => 'index']);
$router->add('favoris', ['controller' => 'Favoris', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');
$router->dispatch($_SERVER['QUERY_STRING']);

?>