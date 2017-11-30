<?php
ini_set('display_errors', E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)).DS);
define('APP_PATH', ROOT . 'app'. DS);


   
require_once APP_PATH . 'Bootstrap.php';
require_once APP_PATH . 'config.php';
require_once APP_PATH . 'Request.php';
require_once APP_PATH . 'Controller.php';
require_once APP_PATH . 'Model.php';
require_once APP_PATH . 'View.php';
require_once APP_PATH . 'Database.php';
require_once APP_PATH . 'Session.php';
    
try {
    Bootstrap::run(new Request());
} catch (Exception $exc) {
    echo $exc->getMessage();
}


    
    
    






