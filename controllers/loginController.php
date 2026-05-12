<?php

use App\Core\Controller;
use App\Core\Session;

class loginController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(): void {
        $this->_view->renderizar('index', false);
    }

    public function loguear(): void {
        $modelo = $this->loadModel('login');
        $username = sanitize((string) $_POST['username']);
        $password = md5((string) $_POST['password']);
        $user = $modelo->getUser($username, $password);
        if ($user) {
            Session::set('autenticado', true);
            Session::set('usuario', $user);
            $this->redireccionar('index');
        } else {
            $this->redireccionar('login');
        }
    }

    public function cerrar(): void {
        Session::destroy();
        $this->redireccionar('login');
    }
}
