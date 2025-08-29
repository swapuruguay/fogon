<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indexController
 *
 * @author walter
 */
class ajustesController extends Controller
{
    private $_ajustesModel;

    public function __construct()
    {
        parent::__construct();
        $this->_ajustesModel = $this->loadModel('ajustes');
    }

    public function index()
    {
        if (!Session::get('autenticado')) {
            $this->redireccionar('login');
        }

        $ajustes = $this->_ajustesModel->get();
        $this->_view->margen = $ajustes->getMargen();
        $this->_view->espacio = $ajustes->getEspacio();
        $this->_view->left = $ajustes->getLeft(); // Nuevo campo
        $this->_view->renderizar('medidas');
    }

    public function setajustes()
    {
        if (!Session::get('autenticado')) {
            $this->redireccionar('login');
        }

        $margen = filter_input(INPUT_POST, 'margen', FILTER_SANITIZE_NUMBER_INT);
        $espacio = filter_input(INPUT_POST, 'espacio', FILTER_SANITIZE_NUMBER_INT);
        $left = filter_input(INPUT_POST, 'left', FILTER_SANITIZE_NUMBER_INT); // Nuevo campo

        $result = $this->_ajustesModel->update([
            'margen' => $margen,
            'espacio' => $espacio,
            'left' => $left // Nuevo campo
        ]);

        if ($result) {
            echo json_encode(['mensaje' => 'Ajustes guardados correctamente', 'color' => 'green']);
        } else {
            echo json_encode(['mensaje' => 'Error al guardar los ajustes', 'color' => 'red']);
        }
    }
}}