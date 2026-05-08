<?php

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('APP_PATH', ROOT . 'app' . DS);

require_once APP_PATH . 'config.php';

if (defined('APP_DEBUG') && !APP_DEBUG) {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}

try {
    require_once APP_PATH . 'helpers.php';
    require_once APP_PATH . 'core/Database.php';
    require_once APP_PATH . 'core/Session.php';
    require_once APP_PATH . 'core/Request.php';
    require_once APP_PATH . 'core/View.php';
    require_once APP_PATH . 'core/Controller.php';
    require_once APP_PATH . 'core/Model.php';
    require_once APP_PATH . 'core/Bootstrap.php';

    // Compatibility aliases for legacy controllers
    class_alias('App\Core\Database', 'Database');
    class_alias('App\Core\Session', 'Session');
    class_alias('App\Core\Request', 'Request');
    class_alias('App\Core\View', 'View');
    class_alias('App\Core\Controller', 'Controller');
    class_alias('App\Core\Model', 'Model');
    class_alias('App\Core\Bootstrap', 'Bootstrap');

    App\Core\Session::init();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_token'] ?? '';
        if (!App\Core\Session::csrfVerify($token)) {
            http_response_code(419);
            die('CSRF token mismatch');
        }
    }

    App\Core\Bootstrap::run(new App\Core\Request());
} catch (Exception $exc) {
    if (defined('APP_DEBUG') && !APP_DEBUG) {
        http_response_code(500);
        echo '<h1>Error interno del servidor</h1>';
        echo '<p>Intente m&aacute;s tarde</p>';
    } else {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($exc->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($exc->getTraceAsString()) . '</pre>';
    }
}
