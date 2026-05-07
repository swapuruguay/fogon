<?php

class usuariosController extends Controller {

    private $_ajax;

    public function __construct() {
        parent::__construct();
        $this->_ajax = $this->loadModel('usuarios');
    }

    public function index(): void {
        $this->requireAuth();
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('index');
    }

    public function cambiar(): void {
        $this->requireAuth();
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('cambiar');
    }

    public function change(): void {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $password = trim((string) $_POST['password']);
        if ($this->_ajax->cambiarPass($id, $password)) {
            echo "Password cambiado correctamente";
        } else {
            echo "Ocurrió un error, intente más tarde";
        }
    }

    public function logout(): void {
        Session::destroy();
        $this->redireccionar('index');
    }
}
