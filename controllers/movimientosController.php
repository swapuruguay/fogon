<?php

use App\Core\Controller;
use App\Core\Session;

class movimientosController extends Controller
{

    private $_ajax;

    public function __construct()
    {
        parent::__construct();
        $this->_ajax = $this->loadModel('movimientos');
    }

    public function index(): void
    {
        $this->requireAuth();
        $this->_view->titulo = 'Movimientos';
        $this->_view->renderizar('index');
    }

    public function adelantos(): void
    {
        $this->requireAuth();
        $this->_view->titulo = "Ingresar pagos adelantados";
        $this->_view->renderizar('nuevo-adelanto');
    }

    public function categorias(): void
    {
        $this->requireAuth();
        $modelCategorias = $this->loadModel('categorias');
        $this->_view->categorias = $modelCategorias->getAll();
        $this->_view->renderizar('lista-categorias');
    }

    public function editarCategoria(int $id): void
    {
        $this->requireAuth();
        $modelCategorias = $this->loadModel('categorias');
        $this->_view->categoria = $modelCategorias->getById($id);
        $this->_view->renderizar('editar-categoria');
    }

    public function guardarCategoria(): void
    {
        $modelo = $this->loadModel('categorias');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $importe = filter_input(INPUT_POST, 'importe', FILTER_VALIDATE_FLOAT) ?: 0.0;
        $nombre = sanitize((string) $_POST['nombre']);
        $datos = ['id' => $id, 'nombre' => $nombre, 'importe' => $importe];
        $categoria = $modelo->buildCategoria($datos);
        $result = $modelo->update($categoria);
        echo json_encode(['result' => $result]);
    }

    public function getSaldo(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $socioModel = $this->loadModel('socios');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $socio = $socioModel->getById($id);
        $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT) ?: 0;
        $retorno = $this->_ajax->getMovimientosSocio($socio, $limit);
        foreach ($retorno as $valor) {
            $valor->fecha_computo = date('d/m/Y', strtotime($valor->fecha_computo));
        }
        if (!$retorno) {
            $retorno = ['fecha_computo' => '', 'importe' => 0];
        }
        echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
    }

    public function getMovimientosSocio(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $socioModel = $this->loadModel('socios');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $socio = $socioModel->getById($id);
        if (!$socio) {
            echo json_encode(['error' => 'Socio no encontrado', 'id' => $id]);
            return;
        }
        $retorno = $this->_ajax->getMovimientosSocio($socio, 0);
        foreach ($retorno as $valor) {
            $fechaOriginal = $valor->fecha_computo;
            $valor->fecha_computo = date('d/m/Y', strtotime($fechaOriginal));
            $valor->fecha_iso = date('Y-m-d', strtotime($fechaOriginal));
            $valor->anio = (int) date('Y', strtotime($fechaOriginal));
        }
        echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
    }

    public function pagar(): void
    {
        $this->requireAuth();
        $this->_view->titulo = 'Pagar';
        $this->_view->renderizar('pagar');
    }

    public function manual(): void
    {
        $this->requireAuth();
        $this->_view->titulo = 'Cobros manuales';
        $this->_view->renderizar('manual');
    }

    public function getLasts(string $fecha): void
    {
        $this->requireAuth();
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $result = $modelo->getLasts($fecha, 0);
        $listado = [];
        for ($i = 0; $i < count($result); $i++) {
            $socio = $modelSocios->getById($result[$i]->id_socio_fk);
            $listado[$i] = $result[$i];
            $listado[$i]->socio = $socio;
        }
        $this->_view->fechaMy = $fecha;
        $this->_view->fecha = $this->cambiarfecha_vista($fecha);
        $this->_view->pagos = $listado;
        $this->_view->renderizar('lista-pagos');
    }

    public function ingresarPago(string $tipo = 'S'): void
    {
        $mov = $this->_ajax->buildMovimiento();
        $modeloSocio = $this->loadModel('socios');
        $socioId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $mov->setSocio($modeloSocio->getById($socioId));
        $mov->setImporte(- (filter_input(INPUT_POST, 'importe', FILTER_VALIDATE_FLOAT) ?: 0));
        $ingreso = (string) $_POST['fecha'];
        $mes = (int) date('m', strtotime($ingreso));
        $anio = (int) date('Y', strtotime($ingreso));
        $mov->setFecha($ingreso);
        $mov->setMes($mes);
        $mov->setAnio($anio);
        $this->_ajax->save($mov, $tipo);
        $consulta = $this->_ajax->getLasts($mov->getFecha(), 6);

        $retorno = [];
        if (!$consulta) {
            $retorno[] = ['nombre' => 'Sin', 'apellido' => 'Resultados'];
        } else {
            foreach ($consulta as $valor) {
                $socio = $modeloSocio->getById($valor->id_socio_fk);
                $retorno[] = [
                    'id' => $socio->getId(),
                    'nombre' => $socio->__toString(),
                    'importe' => abs($valor->importe),
                ];
            }
        }
        echo json_encode($retorno);
    }

    public function ingresarAdelanto(): void
    {
        $adelanto = [];
        $adelanto['id'] = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $adelanto['desde'] = (string) $_POST['desde'];
        $adelanto['hasta'] = (string) $_POST['hasta'];
        $this->_ajax->saveAdelanto($adelanto);
        echo json_encode(['texto' => 'Registro Ingresado']);
    }

    public function getLastEmitido(): void
    {
        $model = $this->loadModel('movimientos');
        $row = $model->lastEmision();
        echo json_encode($row);
    }

    public function preprint(): void
    {
        $this->requireAuth();
        $model = $this->loadModel('movimientos');
        $row = $model->lastEmision();
        $this->_view->mes = $row->mes;
        $this->_view->anio = $row->anio;
        $this->_view->titulo = 'Imprimir';
        $this->_view->renderizar('formprint');
    }

    public function choose(): void
    {
        $this->requireAuth();
        $this->_view->titulo = 'Elegir';
        $this->_view->renderizar('frmchoose');
    }

    public function imprimir(): void
    {
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT) ?: 0;
        $anio = filter_input(INPUT_POST, 'anio', FILTER_VALIDATE_INT) ?: 0;

        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $row = $modelo->getMes($anio, $mes);
        $registros = count($row);
        $paginas = $registros / 4;
        $this->getLibrary('fpdf');
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial', '', 8);
        $pos_y = 11;
        $it = 0;
        for ($i = 0; $i < $paginas; $i++) {
            $pdf->AddPage();
            for ($j = 0; $j < 4; $j++) {
                if (!($it < $registros)) break;
                $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                $pdf->SetXY(25, $pos_y);
                $pdf->Cell(50, 4, $socio->getId(), 0, 0);
                $pdf->SetXY(60, $pos_y);
                $pdf->Cell(50, 4, $row[$it]->mes . '/' . $row[$it]->anio, 0, 0);
                $pdf->SetXY(85, $pos_y);
                $pdf->Cell(50, 4, $socio->getId(), 0, 0);
                $pdf->SetXY(120, $pos_y);
                $pdf->Cell(50, 4, $row[$it]->mes . '/' . $row[$it]->anio, 0, 0);
                $pdf->SetXY(10, $pos_y + 9);
                $pdf->Cell(80, 4, $this->iso($socio->__toString()), 0, 0, 'C');
                $pdf->SetXY(85, $pos_y + 9);
                $pdf->Cell(90, 4, $this->iso($socio->__toString()), 0, 0, 'C');
                $pdf->SetXY(10, $pos_y + 18);
                $pdf->Cell(80, 4, $this->iso($socio->getDomicilio()), 0, 0, 'C');
                $pdf->SetXY(85, $pos_y + 18);
                $pdf->Cell(90, 4, $this->iso($socio->getDomicilio()), 0, 0, 'C');
                $pdf->SetXY(175, $pos_y + 18);
                $pdf->Cell(50, 4, $this->iso(substr($socio->getCategoria()->getNombre(), 0, 1)), 0, 0);
                $pdf->SetXY(25, $pos_y + 27);
                $pdf->Cell(50, 4, $this->iso(substr($socio->getCategoria()->getNombre(), 0, 1)), 0, 0);
                $pdf->SetXY(60, $pos_y + 27);
                $pdf->Cell(50, 4, $row[$it]->importe, 0, 0);
                $pdf->SetXY(105, $pos_y + 27);
                $pdf->Cell(50, 4, $row[$it]->importe, 0, 0);
                $pos_y += 70;
                $pdf->SetY($pos_y);
                $it++;
            }
            $pos_y = 11;
        }
        $pdf->Output();
    }

    public function imprimir140(array $lista = []): void
    {
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT) ?: 0;
        $anio = filter_input(INPUT_POST, 'anio', FILTER_VALIDATE_INT) ?: 0;
        $dire = filter_input(INPUT_POST, 'dire', FILTER_VALIDATE_INT) ?: 3;
        $message = htmlspecialchars((string) ($_POST['message'] ?? ''), ENT_QUOTES);
        $message = mb_convert_encoding($message, 'ISO-8859-1', 'UTF-8');
        $saltos = explode("\n", $message);
        if (count($saltos) > 3) {
            $message = $saltos[0] . "\n" . $saltos[1] . "\n" . $saltos[2];
        }
        if (strlen($message) > 150) {
            $message = substr($message, 0, 150);
        }
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $modelAjustes = $this->loadModel('ajustes');
        $ajustes = $modelAjustes->get();
        $row = $modelo->getMes($anio, $mes, $dire);
        $registros = count($lista) > 0 ? count($lista) : count($row);
        $paginas = $registros / 4;
        $this->getLibrary('fpdf');
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial', '', 8);
        $pos_y = $ajustes->getMargen();
        $posX = 5 + $ajustes->getLeft();
        $it = 0;
        for ($i = 0; $i < $paginas; $i++) {
            $pdf->AddPage();
            for ($j = 0; $j < 4; $j++) {
                if (!($it < $registros)) break;
                if (count($lista) === 0) {
                    $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                } else {
                    $socio = $modelSocios->getById($lista[$it]->id);
                }
                $pdf->SetXY($posX + 21, $pos_y);
                $pdf->Cell(50, 4, $socio->getId(), 0, 0);
                $pdf->SetXY($posX + 53, $pos_y);
                if (count($lista) === 0) {
                    $pdf->Cell(50, 4, $row[$it]->mes . '/' . $row[$it]->anio, 0, 0);
                } else {
                    $pdf->Cell(50, 4, $lista[$it]->mes . '/' . $lista[$it]->anio, 0, 0);
                }
                $pdf->SetXY($posX + 80, $pos_y);
                $pdf->Cell(50, 4, $socio->getId(), 0, 0);
                $pdf->SetXY($posX + 115, $pos_y);
                if (count($lista) === 0) {
                    $pdf->Cell(50, 4, $row[$it]->mes . '/' . $row[$it]->anio, 0, 0);
                } else {
                    $pdf->Cell(50, 4, $lista[$it]->mes . '/' . $lista[$it]->anio, 0, 0);
                }
                $pdf->SetXY($posX + 5, $pos_y + 9);
                $pdf->Cell(80, 4, $this->iso($socio->__toString()), 0, 0, 'C');
                $pdf->SetXY($posX + 80, $pos_y + 9);
                $pdf->Cell(90, 4, $this->iso($socio->__toString()), 0, 0, 'C');
                $pdf->SetXY($posX + 5, $pos_y + 18);
                $pdf->Cell(80, 4, $this->iso($socio->getDomicilio()), 0, 0, 'C');
                $pdf->SetXY($posX + 80, $pos_y + 18);
                $pdf->Cell(90, 4, $this->iso($socio->getDomicilio()), 0, 0, 'C');
                $pdf->SetXY($posX + 160, $pos_y + 20);
                $pdf->Cell(50, 4, $this->iso(substr($socio->getCategoria()->getNombre(), 0, 1)), 0, 0);
                $pdf->SetXY($posX + 21, $pos_y + 27);
                $pdf->Cell(50, 4, $this->iso(substr($socio->getCategoria()->getNombre(), 0, 1)), 0, 0);
                $pdf->SetXY($posX + 53, $pos_y + 27);
                if (count($lista) === 0) {
                    $pdf->Cell(50, 4, $row[$it]->importe, 0, 0);
                    $pdf->SetXY($posX + 103, $pos_y + 27);
                    $pdf->Cell(50, 4, $row[$it]->importe, 0, 0);
                    $pdf->SetXY($posX + 83, $pos_y + 34);
                    if ($row[$it]->estado === 'P') {
                        $pdf->SetFont('Arial', 'B', 14);
                        $pdf->Cell(50, 4, "PAGO", 0, 0);
                        $pdf->SetFont('Arial', '', 8);
                    }
                    if ($message) {
                        $pdf->SetXY($posX + 80, $pos_y + 33);
                        $pdf->MultiCell(130, 3, $message, 0, 0);
                    }
                } else {
                    $pdf->Cell(50, 4, $lista[$it]->importe, 0, 0);
                    $pdf->SetXY($posX + 103, $pos_y + 27);
                    $pdf->Cell(50, 4, $lista[$it]->importe, 0, 0);
                }
                $pos_y += $ajustes->getEspacio();
                $pdf->SetY($pos_y);
                $it++;
            }
            $pos_y = $ajustes->getMargen();
        }
        $pdf->Output();
    }

    public function listarec(int $mes = 0, int $anio = 0, int $dire = 3): void
    {
        $this->requireAuth();
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $row = $modelo->getMes($anio, $mes, $dire);
        $registros = count($row);
        $paginas = $registros / 45;
        $this->getLibrary('fpdf');
        $pdf = new FPDF();
        $pdf->AliasNbPages();
        $pdf->SetTopMargin(5);
        $pdf->SetFont('Arial', 'B', 14);
        $pos_y = 13;
        $pdf->AddPage();
        $pdf->SetXY(20, $pos_y);
        $titulo = utf8_decode('Listado de emisión de recibos ') . $mes . '/' . $anio;
        $pdf->Cell(0, 8, $titulo, 0, 0, 'C');
        $pos_y = 25;
        $it = 0;
        for ($i = 0; $i < $paginas; $i++) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(20, $pos_y);
            $pdf->Cell(10, 4, 'Nro.', 0, 0);
            $pdf->SetXY(30, $pos_y);
            $pdf->Cell(50, 4, 'Nombre', 0, 0);
            $pdf->SetXY(90, $pos_y);
            $pdf->Cell(65, 4, 'Domicilio', 0, 0);
            $pdf->SetXY(160, $pos_y);
            $pdf->Cell(10, 4, 'Cat', 0, 0);
            $pdf->SetXY(170, $pos_y);
            $pdf->Cell(50, 4, 'Importe', 0, 0);
            $pdf->SetFont('Arial', '', 8);
            $pos_y = 28;
            $pdf->SetY($pos_y);
            for ($j = 0; $j < 45; $j++) {
                if (!($it < $registros)) break;
                $socio = $modelSocios->getById($row[$it]->id_socio_fk);
                $apellidoLow = strtolower($this->iso($socio->__toString()));
                $apellidos = explode(' ', $apellidoLow);
                $apellidosCap = [];
                foreach ($apellidos as $a) {
                    $apellidosCap[] = ucfirst($a);
                }
                $pdf->SetXY(20, $pos_y);
                $pdf->Cell(10, 4, $socio->getId(), 0, 0);
                $pdf->SetXY(30, $pos_y);
                $pdf->Cell(50, 4, implode(' ', $apellidosCap), 0, 0);
                $pdf->SetXY(90, $pos_y);
                $pdf->Cell(65, 4, $this->iso($socio->getDomicilio()), 0, 0);
                $pdf->SetXY(160, $pos_y);
                $pdf->Cell(10, 4, $this->iso(substr($socio->getCategoria()->__toString(), 0, 1)), 0, 0);
                $pdf->SetXY(170, $pos_y);
                $pdf->Cell(50, 4, $row[$it]->importe, 0, 0);
                $pos_y += 5;
                $pdf->SetY($pos_y);
                $it++;
            }
            $pdf->SetY($pos_y + 10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo() . ' de {nb}', 0, 0, 'C');
            if ($pdf->PageNo() < $paginas) {
                $pos_y = 25;
                $pdf->AddPage();
            } else {
                $pdf->SetY($pos_y + 20);
                $tot = json_decode($this->getTotalesE($mes, $anio, $dire));
                $totales = ($tot[0]->importe ?? 0) + ($tot[1]->importe ?? 0) + ($tot[2]->importe ?? 0);
                $pdf->Cell(0, 10, 'Total Activos: $' . ($tot[0]->importe ?? 0), 0, 0, 'C');
                $pdf->SetY($pos_y + 25);
                $pdf->Cell(0, 10, 'Total Cadetes: $' . ($tot[1]->importe ?? 0), 0, 0, 'C');
                $pdf->SetY($pos_y + 30);
                $pdf->Cell(0, 10, 'Total Jubilados: $' . ($tot[2]->importe ?? 0), 0, 0, 'C');
                $pdf->SetY($pos_y + 35);
                $pdf->Cell(0, 10, 'Total General: $' . $totales, 0, 0, 'C');
            }
        }
        $pdf->Output();
    }

    private function iso(string $text): string
    {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    public function generar(): void
    {
        $this->requireAuth();
        $this->_view->titulo = 'Generar';
        $this->_view->renderizar('generar');
    }

    public function generacuota(): void
    {
        $this->requireAuth();
        $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT) ?: 0;
        $anio = filter_input(INPUT_POST, 'anio', FILTER_VALIDATE_INT) ?: 0;
        $this->_view->mes = $mes;
        $this->_view->anio = $anio;
        $this->_view->renderizar('generacuota');
    }

    public function confirmar(int $mes, int $anio): void
    {
        $this->requireAuth();
        $model = $this->loadModel('movimientos');
        $result = $model->verificarMes($mes, $anio);
        if ($result) {
            $modelSocios = $this->loadModel('socios');
            $listado = $modelSocios->getHabilitados();
            foreach ($listado as $s) {
                $res = $model->getSaldo($s);
                $saldo = (int) $res[0]->saldo;
                $s->setSaldo($saldo);
                $aux = $saldo / (int) $s->getCategoria()->getImporte();
                $deuda = ceil($aux);
                if ($deuda >= 6) {
                    $modelSocios->delete($s, Session::get('usuario')->idusuario, 'A');
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

    public function eliminar(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $mov = $this->_ajax->getById($id);
        $this->_ajax->delete($mov);
        $retorno = $this->_ajax->getMovimientosSocio($mov->getSocio());
        foreach ($retorno as $valor) {
            $valor->fecha_computo = date('d/m/Y', strtotime($valor->fecha_computo));
        }
        if (!$retorno) {
            $retorno = ['fecha_computo' => '', 'importe' => 0];
        }
        echo json_encode($retorno);
    }

    public function eliminarPago(int $id, string $fecha): void
    {
        $this->requireAuth();
        $modelo = $this->loadModel('movimientos');
        $modelSocios = $this->loadModel('socios');
        $mov = $modelo->getById($id);
        $modelo->delete($mov);
        $result = $modelo->getLasts($fecha, 0);
        $listado = [];
        for ($i = 0; $i < count($result); $i++) {
            $socio = $modelSocios->getById($result[$i]->id_socio_fk);
            $listado[$i] = $result[$i];
            $listado[$i]->socio = $socio;
        }
        $this->_view->fechaMy = $fecha;
        $this->_view->fecha = $this->cambiarfecha_vista($fecha);
        $this->_view->pagos = $listado;
        $this->_view->renderizar('lista-pagos');
    }

    public function totales(): void
    {
        $this->requireAuth();
        $this->_view->renderizar('totales');
    }

    public function getTotales(): void
    {
        $fecha = to_mysql_date((string) $_POST['fecha']);
        $retorno = $this->_ajax->getTotales($fecha);
        echo json_encode($retorno);
    }

    private function getTotalesE(int $mes, int $anio, int $dire): string
    {
        $retorno = $this->_ajax->getTotales("$anio-$mes-01", $dire);
        return json_encode($retorno);
    }

    public function guardarAdelanto(int $id): void
    {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        if (!isset($_POST['idsoc'])) {
            return;
        }
        $this->_view->renderizar('resultado');
    }

    public function prueba(): void
    {
        $datos = json_decode((string) $_POST['datos']);
        $this->imprimir140($datos);
    }
}
