<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Movimiento
 *
 * @author walter
 */
class movimientosController extends Controller{

    private $_ajax;

    public function __construct() {
        parent::__construct();
         $this->_ajax  = $this->loadModel('movimientos');
    }

    public function index() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('index');
    }

    public function adelantos() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        //$modelo->loadModel('socios');
        $this->_view->titulo = "Ingresar pagos adelantados";
        $this->_view->renderizar('nuevo-adelanto');
    }

    public function getSaldo() {
        $socioModel = $this->loadModel('socios');
        $socio = $socioModel->getById($_POST['id']);
        $limit = 0;
        if(isset($_POST['limit'])) {
            $limit = $_POST['limit'];
        }
        $retorno = $this->_ajax->getMovimientosSocio($socio, $limit);
        foreach($retorno as $valor) {
            $valor->fecha_computo = date('d/m/Y', strtotime($valor->fecha_computo));
        }

        if(!$retorno) {
           $retorno = array('fecha_computo' => '', 'importe' => 0);
        }
        echo json_encode($retorno);
    }

    public function guardar() {

    }

    public function pagar() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('pagar');
    }

    public function manual() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = 'Cobros manuales';
        $this->_view->renderizar('manual');
    }
    
    public function getLasts($fecha) {
         if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $result = $modelo->getLasts($fecha, 0);
        $listado;
        for($i = 0; $i < count($result); $i++) {
            $socio = $modelSocios->getById($result[$i]->id_socio_fk);
            $listado[$i] = $result[$i];
            $listado[$i]->socio = $socio;
        }
        $this->_view->fechaMy = $fecha;
        $this->_view->fecha = $this->cambiarfecha_vista($fecha);
        $this->_view->pagos = $listado;
        $this->_view->renderizar('lista-pagos');
    }

    public function ingresarPago($tipo = 'S') {

        $mov = $this->_ajax->buildMovimiento();
        $modeloSocio = $this->loadModel('socios');
        $mov->setSocio($modeloSocio->getById(filter_input(INPUT_POST ,'id', FILTER_SANITIZE_STRING)));
        $mov->setImporte(-filter_input(INPUT_POST ,'importe', FILTER_SANITIZE_NUMBER_FLOAT));
        $ingreso = filter_input(INPUT_POST ,'fecha', FILTER_SANITIZE_STRING);
        //$ingreso = $this->cambiarfecha_mysql($ingreso);
        $mes = date('m',  strtotime($ingreso));
        $anio = date('Y', strtotime($ingreso));
        $mov->setFecha($ingreso);
        $mov->setMes($mes);
        $mov->setAnio($anio);
        $retorno = array();
        $this->_ajax->save($mov, $tipo);
        $consulta = $this->_ajax->getLasts($mov->getFecha(),6);

        if(!$consulta) {
            $retorno [] = array('nombre' => 'Sin', 'apellido' => 'Resultados');
        } else {
            foreach($consulta as $valor) {
                $socio = $modeloSocio->getById($valor->id_socio_fk);
                $retorno[] = array('id' => $socio->getId()  , 'nombre' => $socio->__toString(),
                    'importe' => abs($valor->importe));
            }


        }
        echo json_encode($retorno);

    }
    public function ingresarAdelanto() {

        // $mov = $this->_ajax->buildMovimiento();
        $modeloSocio = $this->loadModel('socios');
        $adelanto = [];
        $adelanto['id'] = filter_input(INPUT_POST ,'id', FILTER_SANITIZE_NUMBER_INT);
        $desde = filter_input(INPUT_POST ,'desde', FILTER_SANITIZE_STRING);
        //$desde = $this->cambiarfecha_mysql($desde);
        $adelanto['desde'] = $desde;
        $hasta = filter_input(INPUT_POST ,'hasta', FILTER_SANITIZE_STRING);
        //$hasta = $this->cambiarfecha_mysql($hasta);
        $adelanto['hasta'] =  $hasta;
        $this->_ajax->saveAdelanto($adelanto);
        //$consulta = $this->_ajax->getLasts($mov->getFecha(),6);

        // if(!$consulta) {
        $retorno = array('texto' => 'Registro Ingresado');
        
        echo json_encode($retorno);

    }
    
    public function getLastEmitido() {
        $model = $this->loadModel('movimientos');
        $row = $model->lastEmision();
        echo json_encode($row);
    }

    public function preprint() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        
        $model = $this->loadModel('movimientos');
        $row = $model->lastEmision();
        $mes = $row->mes;
        $anio = $row->anio;
        
        $this->_view->mes = $mes;
        $this->_view->anio = $anio;
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('formprint');
    }
    public function choose() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('frmchoose');
    }

    public function imprimir() {
        $mes = filter_input(INPUT_POST, 'mes', FILTER_SANITIZE_NUMBER_INT);
        $anio = filter_input(INPUT_POST, 'anio', FILTER_SANITIZE_NUMBER_INT);

        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $row = $modelo->getMes($anio, $mes);
        $registros = count($row);
        $paginas = $registros / 4;
        //echo $paginas . ' ' . $registros;
        $this->getLibrary('fpdf');
        $pdf=new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial','',8);

        $pos_y  =   11;
        $it = 0;
        for($i = 0; $i < $paginas; $i++) {
            $pdf->AddPage();
            for($j = 0; $j < 4; $j++) {
                    if(!($it < $registros))
                        break;
                    $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                    $pdf->SetXY(25,$pos_y);
                    $pdf->Cell(50,4,$socio->getId(),0,0);
                    $pdf->SetXY(60,$pos_y);
                    $pdf->Cell(50,4,$row[$it]->mes.'/'.$row[$it]->anio,0,0);
                    $pdf->SetXY(85,$pos_y);
                    $pdf->Cell(50,4,$socio->getId(),0,0);
                    $pdf->SetXY(120,$pos_y);
                    $pdf->Cell(50,4,$row[$it]->mes.'/'.$row[$it]->anio,0,0);
                    $pdf->SetXY(10,$pos_y + 9);
                    $pdf->Cell(80,4,  utf8_decode(utf8_decode($socio->__toString())),0,0,'C');
                    $pdf->SetXY(85,$pos_y + 9);
                    $pdf->Cell(90,4,  utf8_decode(utf8_decode($socio->__toString())),0,0,'C');
                    $pdf->SetXY(10,$pos_y + 18);
                    $pdf->Cell(80,4, utf8_decode(utf8_decode($socio->getDomicilio())),0,0,'C');
                    $pdf->SetXY(85,$pos_y + 18);
                    $pdf->Cell(90,4,  utf8_decode(utf8_decode($socio->getDomicilio())),0,0,'C');
                    $pdf->SetXY(175,$pos_y + 18);
                    $pdf->Cell(50,4,  substr($socio->getCategoria()->getNombre(),0,1),0,0);
                    $pdf->SetXY(25,$pos_y + 27);
                    $pdf->Cell(50,4,  substr($socio->getCategoria()->getNombre(),0,1),0,0);
                    $pdf->SetXY(60,$pos_y + 27);
                    $pdf->Cell(50,4, $row[$it]->importe, 0,0);
                    $pdf->SetXY(105,$pos_y + 27);
                    $pdf->Cell(50,4, $row[$it]->importe, 0,0);
                    $pos_y+=70;
                    $pdf->SetY($pos_y);

                    $it++;

            }

            $pos_y = 11;

        //$pdf->SetFillColor(236,235,236);



        }

        $pdf->Output();

    }

   public function imprimir140($lista = []) {
        $mes = filter_input(INPUT_POST, 'mes', FILTER_SANITIZE_NUMBER_INT);
        $anio = filter_input(INPUT_POST, 'anio', FILTER_SANITIZE_NUMBER_INT);
        $dire = filter_input(INPUT_POST, 'dire', FILTER_SANITIZE_NUMBER_INT);
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $modelAjustes = $this->loadModel('ajustes');
        $ajustes = $modelAjustes->get();
        $row = $modelo->getMes($anio, $mes, $dire);
        $registros = count($lista) > 0 ? count($lista) : count($row);
        $paginas = $registros / 4;
        //echo $paginas . ' ' . $registros;
        $this->getLibrary('fpdf');
        $pdf=new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial','',8);

        $pos_y  =   $ajustes->getMargen();
        //$pos_y  = 8;
       // $pos_y  =   13;
       $posX = 5;
        $it = 0;
        for($i = 0; $i < $paginas; $i++) {
            $pdf->AddPage();
            for($j = 0; $j < 4; $j++) {
                    if(!($it < $registros))
                        break;
                    if(count($lista) === 0) {
                        $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                    } else {
                        $socio = $modelSocios->getById($lista[$it]->id);
                    }
                    $pdf->SetXY($posX + 21,$pos_y);
                    $pdf->Cell(50,4,$socio->getId(),0,0);
                    $pdf->SetXY($posX + 53,$pos_y);
                    if(count($lista) === 0) {
                        $pdf->Cell(50,4,$row[$it]->mes.'/'.$row[$it]->anio,0,0);
                    }
                    else {
                        $pdf->Cell(50,4,$lista[$it]->mes.'/'.$lista[$it]->anio,0,0);    
                    }
                    $pdf->SetXY($posX + 83,$pos_y);
                    $pdf->Cell(50,4,$socio->getId(),0,0);
                    $pdf->SetXY($posX + 115,$pos_y);
                    if(count($lista) === 0) {
                        $pdf->Cell(50,4,$row[$it]->mes.'/'.$row[$it]->anio,0,0);
                    } else {
                        $pdf->Cell(50,4,$lista[$it]->mes.'/'.$lista[$it]->anio,0,0);
                    }
                    $pdf->SetXY($posX + 5,$pos_y + 9);
                    $pdf->Cell(80,4,  utf8_decode(utf8_decode($socio->__toString())),0,0,'C');
                    $pdf->SetXY($posX + 80,$pos_y + 9);
                    $pdf->Cell(90,4,  utf8_decode(utf8_decode($socio->__toString())),0,0,'C');
                    $pdf->SetXY($posX + 5,$pos_y + 18);
                    $pdf->Cell(80,4, utf8_decode(utf8_decode($socio->getDomicilio())),0,0,'C');
                    $pdf->SetXY($posX + 80,$pos_y + 18);
                    $pdf->Cell(90,4,  utf8_decode(utf8_decode($socio->getDomicilio())),0,0,'C');
                    $pdf->SetXY($posX + 10 + 170,$pos_y + 18);
                    $pdf->Cell(50,4,  substr($socio->getCategoria()->getNombre(),0,1),0,0);
                    $pdf->SetXY($posX + 21,$pos_y + 27);
                    $pdf->Cell(50,4,  substr($socio->getCategoria()->getNombre(),0,1),0,0);
                    $pdf->SetXY($posX + 53,$pos_y + 27);
                    if(count($lista) === 0) {
                        $pdf->Cell(50,4, $row[$it]->importe, 0,0);
                        $pdf->SetXY($posX + 103,$pos_y + 27);
                        $pdf->Cell(50,4, $row[$it]->importe, 0,0);
                        $pdf->SetXY($posX + 83,$pos_y + 34);
                        if($row[$it]->estado === 'P') {
                            $pdf->SetFont('Arial','B',14);
                            $pdf->Cell(50,4, "PAGO", 0, 0);  
                            $pdf->SetFont('Arial','',8);
                        }
                    } else {
                        $pdf->Cell(50,4, $lista[$it]->importe, 0,0);
                        $pdf->SetXY($posX + 103,$pos_y + 27);
                        $pdf->Cell(50,4, $lista[$it]->importe, 0,0);
                        $pdf->SetXY($posX + 83,$pos_y + 34);  
                        //$pdf->SetFont('Arial','B',14);
                       // $pdf->Cell(50,4, $row[$it]->estado, 0, 0);
                    }
                    
                    
                    //$pos_y+=73;
                    //$pos_y+=75;
                    $pos_y+= $ajustes->getEspacio();
                    $pdf->SetY($pos_y);

                    $it++;

            }
            //$pos_y = 8;
            //$pos_y = 13;
            $pos_y  =   $ajustes->getMargen();
        //$pdf->SetFillColor(236,235,236);



        }

        $pdf->Output();

    }

    public function listarec($mes, $anio, $dire) {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $row = $modelo->getMes($anio, $mes, $dire);
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
        $pdf->Cell(0,8,utf8_decode('Listado de emisión de recibos ' .$mes . '/' . $anio),0,0,'C');

        $pos_y = 25;

        $it = 0;
        for($i = 0; $i < $paginas; $i++) {
            $pdf->SetFont('Arial','B',8);
            $pdf->SetXY(20,$pos_y);
            $pdf->Cell(10,4,'Nro.',0,0);
            $pdf->SetXY(30,$pos_y);
            $pdf->Cell(50,4,'Nombre',0,0);
            $pdf->SetXY(90,$pos_y);
            $pdf->Cell(65,4,'Domicilio',0,0);
            $pdf->SetXY(160,$pos_y);
            $pdf->Cell(10,4,'Cat',0,0);
            $pdf->SetXY(170,$pos_y);
            $pdf->Cell(50,4,'Importe',0,0);
            //$pos_y+=5;
            $pdf->SetFont('Arial','',8);
            $pos_y = 28;
            $pdf->SetY($pos_y);
            for($j = 0; $j < 45; $j++) {
                    if(!($it < $registros))
                        break;
                        $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                        $pdf->SetXY(20,$pos_y);
                        $pdf->Cell(10,4,$socio->getId(),0,0);
                        $pdf->SetXY(30,$pos_y);
                        $pdf->Cell(50,4,utf8_decode(utf8_decode($socio->__toString())),0,0);
                        $pdf->SetXY(90,$pos_y);
                        $pdf->Cell(65,4,utf8_decode(utf8_decode($socio->getDomicilio())),0,0);
                        $pdf->SetXY(160,$pos_y);
                        $pdf->Cell(10,4,substr($socio->getCategoria()->__toString(),0,1),0,0);
                        $pdf->SetXY(170,$pos_y);
                        $pdf->Cell(50,4,$row[$it]->importe,0,0);
                        $pos_y+=5;
                        $pdf->SetY($pos_y);

                        $it++;

                }
                $pdf->SetY($pos_y + 10);
                $pdf->SetFont('Arial','I',8);
                $pdf->Cell(0,10,utf8_decode('Página ').$pdf->PageNo().' de {nb}',0,0,'C');

                if($pdf->PageNo() < $paginas) {
                      $pos_y = 25;
                        $pdf->AddPage();
                } else {
                    $pdf->SetY($pos_y + 20);
                    $tot = json_decode($this->getTotalesE($mes, $anio, $dire));
                    $totales = $tot[0]->importe+$tot[1]->importe+$tot[2]->importe;

                    $pdf->Cell(0,10, utf8_decode('Total Activos: $').$tot[0]->importe, 0, 0, 'C');
                    $pdf->SetY($pos_y + 25);
                    $pdf->Cell(0,10, utf8_decode('Total Cadetes: $').$tot[1]->importe, 0, 0, 'C');
                    $pdf->SetY($pos_y + 30);
                    $pdf->Cell(0,10, utf8_decode('Total Jubilados: $').$tot[2]->importe, 0, 0, 'C');
                    $pdf->SetY($pos_y + 35);
                    $pdf->Cell(0,10, utf8_decode('Total General: $').$totales, 0, 0, 'C');

                }


            //$pdf->SetFillColor(236,235,236);



            }

            $pdf->Output();



    }

    public function imprimirSocios($mes, $anio) {

    }

    public function generar() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('generar');
    }

    public function generacuota() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $mes = filter_input(INPUT_POST, 'mes', FILTER_SANITIZE_NUMBER_INT);
        $anio = filter_input(INPUT_POST, 'anio', FILTER_SANITIZE_NUMBER_INT);
        $this->_view->mes = $mes;
        $this->_view->anio = $anio;

        $this->_view->renderizar('generacuota');
    }

    public function confirmar($mes, $anio) {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $model = $this->loadModel('movimientos');
        $result = $model->verificarMes($mes, $anio);
        if($result) {
            $modelSocios = $this->loadModel('socios');
            $listado = $modelSocios->getHabilitados();
            $listadoAux = [];
            foreach ($listado as $s) {
                $res = $model->getSaldo($s);
                $saldo = (int) $res[0]->saldo;
                $s->setSaldo($saldo);
                $aux = $saldo / (int) $s->getCategoria()->getImporte();
                $deuda = ceil($aux);
                if($deuda >= 6) {
                   $modelSocios->delete($s, Session::get('usuario')->idusuario, 'A');
                   $clave = array_search($s, $listado);
                   unset($listado[$clave]);
                }
                
            }
            
            $model->generarMes($listado, $mes, $anio);
            $this->_view->mensaje = 'Mes generado con &eacute;xito';
            $this->_view->renderizar('generado');
        } else {
            $this->_view->mensaje = 'El mes ya est&aacute; generado';
            $this->_view->renderizar('generado');
        }


    }

    public function eliminar() {
        $mov = $this->_ajax->getById($_POST['id']);
        $this->_ajax->delete($mov);

        $retorno = $this->_ajax->getMovimientosSocio($mov->getSocio());
        foreach($retorno as $valor) {
            $valor->fecha_computo = date('d/m/Y', strtotime($valor->fecha_computo));
        }

        if(!$retorno) {
           $retorno = array('fecha_computo' => '', 'importe' => 0);
        }
        echo json_encode($retorno);
    }
    
    public function eliminarPago($id, $fecha) {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $mov = $modelo->getById($id);
        $modelo->delete($mov);
        //$row = $modelo->getUltimaCuota();
        //$fecha = $row->fecha_computo;
        $result = $modelo->getLasts($fecha, 0);
        $listado = [];
        for($i = 0; $i < count($result); $i++) {
            $socio = $modelSocios->getById($result[$i]->id_socio_fk);
            $listado[$i] = $result[$i];
            $listado[$i]->socio = $socio;
        }
     
        $this->_view->fechaMy = $fecha;
        $this->_view->fecha = $this->cambiarfecha_vista($fecha);
        $this->_view->pagos = $listado;
        $this->_view->renderizar('lista-pagos');
    }

    public function totales() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->renderizar('totales');
    }

    public function getTotales() {
        $fecha = $this->cambiarfecha_mysql($_POST['fecha']);
        $retorno = $this->_ajax->getTotales($fecha);
        echo json_encode($retorno);
    }

    private function getTotalesE($mes, $anio, $dire) {

        $retorno = $this->_ajax->getTotales($anio.'-'.$mes.'-01', $dire);
        return json_encode($retorno);
    }

    public function guardarAdelanto($id) {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $modelo  = $this->loadModel('socios');
        if(isset($_POST['idsoc'])) {
            $idSocio = filter_input(INPUT_POST ,'idsoc', FILTER_SANITIZE_NUMBER_INT);
            $id      = filter_input(INPUT_POST ,'id', FILTER_SANITIZE_NUMBER_INT);
            if($id == 0) {
                
                //$nro = $modelo->getNroNuevo('socios');

                $nacimiento = filter_input(INPUT_POST ,'fecha_nacimiento', FILTER_SANITIZE_STRING);
                //$nacimiento = $this->cambiarfecha_mysql($nacimiento);
                $socio->setFechaNacimiento($nacimiento);
                if($modelo->savePariente($socio, Session::get('usuario')->idusuario)) {
                    $this->_view->mensaje = "Registro guardado";
                }

                $this->_view->renderizar('resultado');

            } else {
                
                $socio->setDocumento(filter_input(INPUT_POST ,'documento', FILTER_SANITIZE_NUMBER_INT));
                $socio->setParentezco(filter_input(INPUT_POST ,'parentezco', FILTER_SANITIZE_STRING));
                $socio->setSexo(filter_input(INPUT_POST ,'sexo', FILTER_SANITIZE_STRING));
                $nacimiento = filter_input(INPUT_POST ,'fecha_nacimiento', FILTER_SANITIZE_STRING);
                //$nacimiento = $this->cambiarfecha_mysql($nacimiento);
                $socio->setFechaNacimiento($nacimiento);
                if($modelo->updatePariente($socio, Session::get('usuario')->idusuario)) {
                    $this->_view->mensaje = "Registro guardado";
                }
            }

            $this->_view->renderizar('resultado');
        }
    }

    function prueba() {
        $datos = json_decode($_POST['datos']);
        $this->imprimir140($datos);
    }
} 
