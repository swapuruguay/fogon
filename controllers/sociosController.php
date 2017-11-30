<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'libs/Paginador.php';


/**
 * Description of sociosController
 *
 * @author walter
 */
class sociosController extends Controller{

    private $_pdf;

    private $_ajax;

    public function __construct() {
        parent::__construct();
        $this->_ajax  = $this->loadModel('socios');
        $this->getLibrary('fpdf');
        $this->_pdf = new FPDF();
    }

    public function index() {
        $modelo  = $this->loadModel('socios');
        //$modelo->getNroNuevo('socios');
        $this->_view->renderizar('index');

    }

    public function listar($pag=0) {

        $modelo  = $this->loadModel('socios');
        $this->_view->socios = $modelo->getAll();
        $totalRegistros = count($this->_view->socios);
        $desde = $pag*15;
        $this->_view->socios = $modelo->getPaginados($desde);
        $paginador = new Paginador();
        $paginador->setCantidadRegistros(15);
        $paginador->setClass('primero',         'previous');
        $paginador->setClass('bloqueAnterior',  'previous');
        $paginador->setClass('anterior',        'previous');
        $paginador->setClass('siguiente',       'next');
        $paginador->setClass('bloqueSiguiente', 'next');
        $paginador->setClass('ultimo',          'next');
        $paginador->setClass('numero',          '<>');
        $paginador->setClass('actual',          'active');
        $pagina = $pag;
        $this->_view->datos      = $paginador->paginar($pagina, $totalRegistros);
        $this->_view->enlaces    = $paginador->getHtmlPaginacion('pagina', 'li');
        $this->_view->renderizar('listar');

    }

    public function nuevo() {
        $modeloCat = $this->loadModel('categorias');
        $this->_view->categorias = $modeloCat->getAll();
        $this->_view->nroSocio = $this->_ajax->getNroNuevo('socios');
        $this->_view->renderizar('nuevo');
    }

    public function editar($id) {
        $modelo  = $this->loadModel('socios');
        $modeloCat = $this->loadModel('categorias');
        $socio = $modelo->getById($id);
        $this->_view->categorias = $modeloCat->getAll();
        $this->_view->socio = $socio;
        $this->_view->renderizar('edicion');


    }

    public function guardar() {
        $modelo  = $this->loadModel('socios');
        $modeloCat = $this->loadModel('categorias');
        if(isset($_POST['idsoc'])) {
            $id = filter_input(INPUT_POST ,'idsoc', FILTER_SANITIZE_NUMBER_INT);
            if($id == 0) {
                $socio = $modelo->buildSocio();
                $socio->setNombre(filter_input(INPUT_POST ,'nombre', FILTER_SANITIZE_STRING));
                $socio->setApellido(filter_input(INPUT_POST ,'apellido', FILTER_SANITIZE_STRING));
                $socio->setDomicilio(filter_input(INPUT_POST ,'domicilio', FILTER_SANITIZE_STRING));
                $socio->setTelefono(filter_input(INPUT_POST ,'telefono', FILTER_SANITIZE_STRING));
                $nro = $modelo->getNroNuevo('socios');
                if(isset($_FILES['foto'])) {
                    $dir_subida = dirname(APP_PATH) . '/views/socios/img/';
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $fichero_subido = $dir_subida . $nro.'.'.$ext;

                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $fichero_subido)) {
                        $socio->setFoto($nro.'.'.$ext);
                    } else {
                        $socio->setFoto('socio.png');
                    }
                }

                $socio->setExento(isset($_POST['exento'])? 1 : 0);
                $ingreso = filter_input(INPUT_POST ,'fecha_ingreso', FILTER_SANITIZE_STRING);
                $ingreso = $this->cambiarfecha_mysql($ingreso);
                $socio->setFechaIngreso($ingreso);
                $nacimiento = filter_input(INPUT_POST ,'fecha_nacimiento', FILTER_SANITIZE_STRING);
                $nacimiento = $this->cambiarfecha_mysql($nacimiento);
                $socio->setFechaNacimiento($nacimiento);
                $socio->setDocumento($_POST['documento']);
                $idcat = filter_input(INPUT_POST ,'categorias', FILTER_SANITIZE_STRING);
                $id = explode("_", $idcat);
                $id1 = $id['1'];
                $socio->setEmail($_POST['email']);
                $socio->setCategoria($modeloCat->getById($id1));
                if($modelo->save($socio)) {
                    $this->_view->mensaje = "Registro guardado";
                }

                $this->_view->renderizar('resultado');

            } else {
                $socio = $modelo->buildSocio();
                $socio->setId($id);
                $socio->setNombre(filter_input(INPUT_POST ,'nombre', FILTER_SANITIZE_STRING));
                $socio->setApellido(filter_input(INPUT_POST ,'apellido', FILTER_SANITIZE_STRING));
                $socio->setDocumento($_POST['documento']);
                $socio->setDomicilio(filter_input(INPUT_POST ,'domicilio', FILTER_SANITIZE_STRING));
                $socio->setTelefono(filter_input(INPUT_POST ,'telefono', FILTER_SANITIZE_STRING));
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                if($_FILES['foto']['name'] != '') {
                    $dir_subida = dirname(APP_PATH) . '/views/socios/img/';
                    $fichero_subido = $dir_subida . $id.'.'.$ext;

                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $fichero_subido)) {
                        $socio->setFoto($id.'.'.$ext);
                    } else {
                        $socio->setFoto('socio.png');
                    }
                } else {

                }
                $socio->setExento(isset($_POST['exento'])? 1 : 0);
                $ingreso = filter_input(INPUT_POST ,'fecha_ingreso', FILTER_SANITIZE_STRING);
                $ingreso = $this->cambiarfecha_mysql($ingreso);
                $socio->setFechaIngreso($ingreso);
                $nacimiento = filter_input(INPUT_POST ,'fecha_nacimiento', FILTER_SANITIZE_STRING);
                $nacimiento = $this->cambiarfecha_mysql($nacimiento);
                $socio->setFechaNacimiento($nacimiento);
                $idcat = filter_input(INPUT_POST ,'categorias', FILTER_SANITIZE_STRING);
                $id = explode("_", $idcat);
                $id1 = $id['1'];
                $socio->setEmail($_POST['email']);
                $socio->setCategoria($modeloCat->getById($id1));
                if($modelo->update($socio)) {
                    $this->_view->mensaje = "Registro guardado";
                }
            }

            $this->_view->renderizar('resultado');
        }
    }

    public function confirmar($id) {
        $modelo  = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        $this->_view->socio = $socio;
        $this->_view->renderizar('confirmar');
    }

    public function eliminar($id) {
        $modelo  = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        if($modelo->delete($socio)) {
            $this->_view->mensaje = "Registro eliminado con &eacute;xito";
        } else {
            $this->_view->mensaje = "No se pudo eliminar el socio intente m&aacute;s tarde";
        }
        $this->_view->renderizar('resultado');


    }

    public function atrasados() {
        $modelo = $this->loadModel('socios');
        $this->_view->socios = $modelo->getAtrasados();
        $this->_view->renderizar('atrasados');
    }

    public function findSocios() {
        $active = $_POST['active'];
        if($active == 'activo') {
            $retorno = $this->_ajax->getByApellido($this->getTexto('texto'));
        } else {
            $retorno = $this->_ajax->getByApellidoE($this->getTexto('texto'));
        }

      if(!$retorno) {


          $retorno = array('nombre' => 'Sin', 'apellido' => 'Resultados');
      }
      echo json_encode($retorno, JSON_UNESCAPED_UNICODE);



    }

    public function loadSocioAjax() {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if($id) {
            $retorno = $this->_ajax->getById($id);
            $objeto = array(
                'id'        => $retorno->getId(),
                'nombre'    => $retorno->getNombre(). ' ' . $retorno->getApellido()
            );
        } else {
            $objeto = array('nombre' => 'No encontrado');
        }
        echo json_encode($objeto);
    }

    public function eliminados($pag=0) {

        $modelo  = $this->loadModel('socios');
        $this->_view->socios = $modelo->getEliminados();
        $totalRegistros = count($this->_view->socios);
        $desde = $pag*15;
        $this->_view->socios = $modelo->getPaginadosE($desde);
        $paginador = new Paginador();
        $paginador->setCantidadRegistros(15);
        $paginador->setClass('primero',         'previous');
        $paginador->setClass('bloqueAnterior',  'previous');
        $paginador->setClass('anterior',        'previous');
        $paginador->setClass('siguiente',       'next');
        $paginador->setClass('bloqueSiguiente', 'next');
        $paginador->setClass('ultimo',          'next');
        $paginador->setClass('numero',          '<>');
        $paginador->setClass('actual',          'active');
        $pagina = $pag;
        $this->_view->datos      = $paginador->paginar($pagina, $totalRegistros);
        $this->_view->enlaces    = $paginador->getHtmlPaginacion('pagina', 'li', 'eliminados');
        $this->_view->renderizar('eliminados');

    }

    public function activar($id) {
        $modelo  = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        $this->_view->socio = $socio;
        $this->_view->renderizar('activar');
    }

    public function activarf($id) {
        $modelo  = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        if($modelo->activar($socio)) {
            $this->_view->mensaje = "Socio activado con &eacute;xito";
        } else {
            $this->_view->mensaje = "No se pudo activar el socio intente m&aacute;s tarde";
        }
        $this->_view->renderizar('resultado');


    }

    public function totales() {
        $this->_view->renderizar('totales');
    }

    public function listarsocios() {

        $modelSocios = $this->loadModel('socios');
        $row = $modelSocios->getAll('apel');
        $registros = count($row);
        $paginas = $registros / 45;
        //echo $paginas . ' ' . $registros;
        $this->getLibrary('fpdf');
        $pdf=new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial','B',14);
        $pos_y  =   13;
        $pdf->AddPage();
        $pdf->SetXY(20,$pos_y);
        $pdf->Cell(0,8,utf8_decode('Listado de Socios'),0,0,'C');

        $pos_y = 25;

        $it = 0;
        for($i = 0; $i < $paginas; $i++) {
            $pdf->SetFont('Arial','B',10);
            $pdf->SetXY(20,$pos_y);
            $pdf->Cell(10,4,'Nro.',0,0);
            $pdf->SetXY(30,$pos_y);
            $pdf->Cell(50,4,'Nombre',0,0);
            $pdf->SetXY(90,$pos_y);
            $pdf->Cell(65,4,'Domicilio',0,0);
            $pdf->SetXY(160,$pos_y);
            $pdf->Cell(10,4,'Cat',0,0);

            //$pos_y+=5;
            $pdf->SetFont('Arial','',10);
            $pos_y = 28;
            $pdf->SetY($pos_y);
            for($j = 0; $j < 45; $j++) {
                    if(!($it < $registros))
                        break;
                        $pdf->SetXY(20,$pos_y);
                        $pdf->Cell(10,4,$row[$it]->getId(),0,0);
                        $pdf->SetXY(30,$pos_y);
                        $pdf->Cell(50,4,utf8_decode(utf8_decode($row[$it]->getApellido())),0,0);
                        $pdf->SetXY(90,$pos_y);
                        $pdf->Cell(65,4,utf8_decode(utf8_decode($row[$it]->getDomicilio())),0,0);
                        $pdf->SetXY(160,$pos_y);
                        $pdf->Cell(10,4,substr($row[$it]->getCategoria()->__toString(),0,1),0,0);
                        $pos_y+=5;
                        $pdf->SetY($pos_y);

                        $it++;

                }

                if($pdf->PageNo() < $paginas) {
                    $pdf->SetY($pos_y + 10);
                    $pdf->SetFont('Arial','I',8);
                    $pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');
                    $pos_y = 25;
                    $pdf->AddPage();
                } else {
                    $pdf->SetY($pos_y + 10);
                    $pdf->SetFont('Arial','B',12);
                    $pdf->Cell(0,10,'Cantidad de socios: '.$registros, 0,0,'C');
                    $pdf->SetY($pos_y + 20);
                    $pdf->SetFont('Arial','I',8);
                    $pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');
                    $pos_y = 25;
                }



            //$pdf->SetFillColor(236,235,236);



            }

            $pdf->Output();

    }


}
