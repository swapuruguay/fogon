<?php

class indexController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(): void {
        $this->requireAuth();
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('index');
    }
}
