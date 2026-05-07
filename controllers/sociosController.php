<?php

require_once 'libs/Paginador.php';

class sociosController extends Controller {

    private $_pdf;
    private $_ajax;

    public function __construct() {
        parent::__construct();
        $this->_ajax = $this->loadModel('socios');
        $this->getLibrary('fpdf');
        $this->_pdf = new FPDF();
    }

    public function index(): void {
        $this->requireAuth();
        $this->_view->renderizar('index');
    }

    public function busqueda(): void {
        $this->requireAuth();
        $this->_view->renderizar('busqueda');
    }

    public function getByDoc(): void {
        $documento = filter_input(INPUT_POST, 'search', FILTER_VALIDATE_INT) ?: 0;
        $modelo = $this->loadModel('socios');
        $socio = $modelo->getByDocumento($documento);
        if ($socio) {
            echo json_encode([
                'id' => $socio->getId(),
                'nombre' => $socio->getNombre() . ' ' . $socio->getApellido(),
                'documento' => $socio->getDocumento(),
            ]);
        } else {
            echo json_encode(['error' => 'No encontrado']);
        }
    }

    public function listar(int $pag = 0): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socios = $modelo->getAll();
        $totalRegistros = count($this->_view->socios);
        $desde = $pag * 15;
        $this->_view->socios = $modelo->getPaginados($desde);
        $paginador = new Paginador();
        $paginador->setCantidadRegistros(15);
        $paginador->setClass('primero', 'previous');
        $paginador->setClass('bloqueAnterior', 'previous');
        $paginador->setClass('anterior', 'previous');
        $paginador->setClass('siguiente', 'next');
        $paginador->setClass('bloqueSiguiente', 'next');
        $paginador->setClass('ultimo', 'next');
        $paginador->setClass('numero', '<>');
        $paginador->setClass('actual', 'active');
        $this->_view->datos = $paginador->paginar($pag, $totalRegistros);
        $this->_view->enlaces = $paginador->getHtmlPaginacion('pagina', 'li');
        $this->_view->renderizar('listar');
    }

    public function nuevo(): void {
        $this->requireAuth();
        $modeloCat = $this->loadModel('categorias');
        $this->_view->categorias = $modeloCat->getAll();
        $this->_view->nroSocio = $this->_ajax->getNroNuevo('socios');
        $this->_view->renderizar('nuevo');
    }

    public function nuevoPariente(int $idSocio): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socio = $modelo->getById($idSocio);
        $this->_view->renderizar('nuevo_pariente');
    }

    public function editar(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $modeloCat = $this->loadModel('categorias');
        $this->_view->categorias = $modeloCat->getAll();
        $this->_view->socio = $modelo->getById($id);
        $this->_view->renderizar('edicion');
    }

    public function editarPariente(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->pariente = $modelo->getParienteById($id);
        $this->_view->renderizar('editar-pariente');
    }

    public function guardar(): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $modeloCat = $this->loadModel('categorias');

        if (!isset($_POST['idsoc'])) {
            return;
        }

        $id = filter_input(INPUT_POST, 'idsoc', FILTER_VALIDATE_INT) ?: 0;

        if ($id == 0) {
            $socio = $modelo->buildSocio();
            $socio->setNombre(sanitize((string) $_POST['nombre']));
            $socio->setApellido(sanitize((string) $_POST['apellido']));
            $socio->setDomicilio(sanitize((string) $_POST['domicilio']));
            $socio->setTelefono(sanitize((string) $_POST['telefono']));
            $nro = $modelo->getNroNuevo('socios');
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $dir_subida = dirname(APP_PATH) . '/views/socios/img/';
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $fichero_subido = $dir_subida . $nro . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $fichero_subido)) {
                    $socio->setFoto($nro . '.' . $ext);
                } else {
                    $socio->setFoto('socio.png');
                }
            }
            $socio->setExento(isset($_POST['exento']) ? 1 : 0);
            $ingreso = to_mysql_date((string) $_POST['fecha_ingreso']);
            $socio->setFechaIngreso($ingreso);
            $nacimiento = to_mysql_date((string) $_POST['fecha_nacimiento']);
            $socio->setFechaNacimiento($nacimiento);
            $socio->setDocumento((string) $_POST['documento']);
            $idcat = (string) $_POST['categorias'];
            $id_parts = explode("_", $idcat);
            $socio->setEmail(sanitize((string) $_POST['email']));
            $socio->setCategoria($modeloCat->getById((int) ($id_parts[1] ?? 0)));
            $modelo->save($socio, Session::get('usuario')->idusuario);
            $this->_view->mensaje = "Registro guardado";
        } else {
            $socio = $modelo->buildSocio();
            $socio->setId($id);
            $socio->setNombre(sanitize((string) $_POST['nombre']));
            $socio->setApellido(sanitize((string) $_POST['apellido']));
            $socio->setDocumento((string) $_POST['documento']);
            $socio->setDomicilio(sanitize((string) $_POST['domicilio']));
            $socio->setTelefono(sanitize((string) $_POST['telefono']));
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && $_FILES['foto']['name'] !== '') {
                $dir_subida = dirname(APP_PATH) . '/views/socios/img/';
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $fichero_subido = $dir_subida . $id . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $fichero_subido)) {
                    $socio->setFoto($id . '.' . $ext);
                } else {
                    $socio->setFoto('socio.png');
                }
            }
            $socio->setExento(isset($_POST['exento']) ? 1 : 0);
            $ingreso = to_mysql_date((string) $_POST['fecha_ingreso']);
            $socio->setFechaIngreso($ingreso);
            $nacimiento = to_mysql_date((string) $_POST['fecha_nacimiento']);
            $socio->setFechaNacimiento($nacimiento);
            $idcat = (string) $_POST['categorias'];
            $id_parts = explode("_", $idcat);
            $socio->setEmail(sanitize((string) $_POST['email']));
            $socio->setCategoria($modeloCat->getById((int) ($id_parts[1] ?? 0)));
            $modelo->update($socio, Session::get('usuario')->idusuario);
            $this->_view->mensaje = "Registro guardado";
        }
        $this->_view->renderizar('resultado');
    }

    public function confirmar(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socio = $modelo->getById($id);
        $this->_view->renderizar('confirmar');
    }

    public function eliminar(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        if ($modelo->delete($socio, Session::get('usuario')->idusuario)) {
            $this->_view->mensaje = "Registro eliminado con &eacute;xito";
        } else {
            $this->_view->mensaje = "No se pudo eliminar el socio intente m&aacute;s tarde";
        }
        $this->_view->renderizar('resultado');
    }

    public function atrasados(): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socios = $modelo->getAtrasados();
        $this->_view->renderizar('atrasados');
    }

    public function findSocios(): void {
        $active = $_POST['active'] ?? '';
        $texto = sanitize((string) $_POST['texto']);
        if ($active === 'activo') {
            $retorno = $this->_ajax->getByApellido($texto);
        } else {
            $retorno = $this->_ajax->getByApellidoE($texto);
        }
        if (!$retorno) {
            $retorno = ['nombre' => 'Sin', 'apellido' => 'Resultados'];
        }
        echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
    }

    public function loadSocioAjax(): void {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        if ($id) {
            $retorno = $this->_ajax->getById($id);
            echo json_encode([
                'id' => $retorno->getId(),
                'nombre' => $retorno->getNombre() . ' ' . $retorno->getApellido(),
            ]);
        } else {
            echo json_encode(['nombre' => 'No encontrado']);
        }
    }

    public function getByDocumento(): void {
        $documento = filter_input(INPUT_POST, 'documento', FILTER_VALIDATE_INT) ?: 0;
        if ($documento) {
            $retorno = $this->_ajax->getByDocumento($documento);
            if ($retorno) {
                echo json_encode([
                    'id' => $retorno->getId(),
                    'nombre' => $retorno->getNombre() . ' ' . $retorno->getApellido(),
                    'documento' => $retorno->getDocumento(),
                ]);
            } else {
                echo json_encode(['documento' => 0]);
            }
        } else {
            echo json_encode(['documento' => 0]);
        }
    }

    public function getParentByDocumento(): void {
        $documento = filter_input(INPUT_POST, 'documento', FILTER_VALIDATE_INT) ?: 0;
        if ($documento) {
            $retorno = $this->_ajax->getParentByDocumento($documento);
            if ($retorno) {
                echo json_encode([
                    'id' => $retorno->getId(),
                    'nombre' => $retorno->getNombre() . ' ' . $retorno->getApellido(),
                    'documento' => $retorno->getDocumento(),
                ]);
            } else {
                echo json_encode(['documento' => 0]);
            }
        } else {
            echo json_encode(['documento' => 0]);
        }
    }

    public function eliminados(int $pag = 0): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socios = $modelo->getEliminados();
        $totalRegistros = count($this->_view->socios);
        $desde = $pag * 15;
        $this->_view->socios = $modelo->getPaginadosE($desde);
        $paginador = new Paginador();
        $paginador->setCantidadRegistros(15);
        $paginador->setClass('primero', 'previous');
        $paginador->setClass('bloqueAnterior', 'previous');
        $paginador->setClass('anterior', 'previous');
        $paginador->setClass('siguiente', 'next');
        $paginador->setClass('bloqueSiguiente', 'next');
        $paginador->setClass('ultimo', 'next');
        $paginador->setClass('numero', '<>');
        $paginador->setClass('actual', 'active');
        $this->_view->datos = $paginador->paginar($pag, $totalRegistros);
        $this->_view->enlaces = $paginador->getHtmlPaginacion('pagina', 'li', 'eliminados');
        $this->_view->renderizar('eliminados');
    }

    public function eliminadosAuto(): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $modelMov = $this->loadModel('movimientos');
        $res = $modelMov->lastEmision();
        $emision = $res->fecha_generado;
        $this->_view->socios = $modelo->getEliminadosAuto($emision);
        $this->_view->renderizar('bajas-auto');
    }

    public function listarAdelantos(): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socios = $modelo->getAdelantos();
        $this->_view->renderizar('listar-adelantos');
    }

    public function activar(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->socio = $modelo->getById($id);
        $this->_view->renderizar('activar');
    }

    public function activarf(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $socio = $modelo->getById($id);
        if ($modelo->activar($socio, Session::get('usuario')->idusuario)) {
            $this->_view->mensaje = "Socio activado con &eacute;xito";
        } else {
            $this->_view->mensaje = "No se pudo activar el socio intente m&aacute;s tarde";
        }
        $this->_view->renderizar('resultado');
    }

    public function totales(): void {
        $this->requireAuth();
        $this->_view->renderizar('totales');
    }

    public function listarsocios(): void {
        $this->requireAuth();
        $modelSocios = $this->loadModel('socios');
        $row = $modelSocios->getAll('apel');
        $parientes = $modelSocios->getByParent();
        $conyuges = 0;
        $varones = 0;
        $mujeres = 0;
        foreach ($parientes as $p) {
            if ($p["parentezco"] == 'C') {
                $conyuges += $p["conteo"];
            } else {
                if ($p['sexo'] == 'F') {
                    $mujeres += $p['conteo'];
                } else {
                    $varones += $p['conteo'];
                }
            }
        }
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
        $pdf->Cell(0, 8, 'Listado de Socios', 0, 0, 'C');

        $pos_y = 25;
        $it = 0;
        for ($i = 0; $i < $paginas; $i++) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(20, $pos_y);
            $pdf->Cell(10, 4, 'Nro.', 0, 0);
            $pdf->SetXY(30, $pos_y);
            $pdf->Cell(50, 4, 'Nombre', 0, 0);
            $pdf->SetXY(90, $pos_y);
            $pdf->Cell(65, 4, 'Domicilio', 0, 0);
            $pdf->SetXY(160, $pos_y);
            $pdf->Cell(10, 4, 'Cat', 0, 0);

            $pdf->SetFont('Arial', '', 10);
            $pos_y = 28;
            $pdf->SetY($pos_y);
            for ($j = 0; $j < 45; $j++) {
                if (!($it < $registros)) break;
                $pdf->SetXY(20, $pos_y);
                $pdf->Cell(10, 4, $row[$it]->getId(), 0, 0);
                $pdf->SetXY(30, $pos_y);
                $pdf->Cell(50, 4, $this->iso($row[$it]->getApellido()), 0, 0);
                $pdf->SetXY(90, $pos_y);
                $pdf->Cell(65, 4, $this->iso($row[$it]->getDomicilio()), 0, 0);
                $pdf->SetXY(160, $pos_y);
                $pdf->Cell(10, 4, $this->iso(substr($row[$it]->getCategoria()->__toString(), 0, 1)), 0, 0);
                $pos_y += 5;
                $pdf->SetY($pos_y);
                $it++;
            }

            if ($pdf->PageNo() < $paginas) {
                $pdf->SetY($pos_y + 10);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo() . ' de {nb}', 0, 0, 'C');
                $pos_y = 25;
                $pdf->AddPage();
            } else {
                $pdf->SetY($pos_y + 10);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(20);
                $pdf->Cell(0, 10, 'Cantidad de socios: ' . $registros, 0, 0, 'L');
                $pdf->SetY($pos_y + 15);
                $pdf->SetX(20);
                $pdf->Cell(0, 10, 'Cantidad conyuges: ' . $conyuges, 0, 0, 'L');
                $pdf->SetY($pos_y + 20);
                $pdf->SetX(20);
                $pdf->Cell(0, 10, 'Cantidad hijos varones: ' . $varones, 0, 0, 'L');
                $pdf->SetY($pos_y + 25);
                $pdf->SetX(20);
                $pdf->Cell(0, 10, 'Cantidad hijas: ' . $mujeres, 0, 0, 'L');
                $pdf->SetY($pos_y + 35);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo() . ' de {nb}', 0, 0, 'C');
                $pos_y = 25;
            }
        }
        $pdf->Output();
    }

    public function imprimirparientes(): void {
        $this->requireAuth();
        $modelSocios = $this->loadModel('socios');
        $row = $modelSocios->getAllParents('parentezco, p.apellido, p.nombre');
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
        $pdf->Cell(0, 8, 'Listado de Familiares', 0, 0, 'C');

        $pos_y = 25;
        $it = 0;
        for ($i = 0; $i < $paginas; $i++) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(20, $pos_y);
            $pdf->Cell(10, 4, 'Nro.', 0, 0);
            $pdf->SetXY(30, $pos_y);
            $pdf->Cell(10, 4, 'Parent.', 0, 0);
            $pdf->SetXY(45, $pos_y);
            $pdf->Cell(50, 4, 'Nombre', 0, 0);
            $pdf->SetXY(90, $pos_y);
            $pdf->Cell(65, 4, 'Apellido', 0, 0);
            $pdf->SetXY(160, $pos_y);
            $pdf->Cell(10, 4, 'Documento', 0, 0);

            $pdf->SetFont('Arial', '', 10);
            $pos_y = 30;
            $pdf->SetY($pos_y);
            for ($j = 0; $j < 45; $j++) {
                if (!($it < $registros)) break;
                $pdf->SetXY(20, $pos_y);
                $pdf->Cell(10, 4, $row[$it]->getId(), 0, 0);
                $pdf->SetXY(30, $pos_y);
                $pdf->Cell(10, 4, $row[$it]->getParentezco(), 0, 0);
                $pdf->SetXY(45, $pos_y);
                $pdf->Cell(50, 4, $this->iso($row[$it]->getNombre()), 0, 0);
                $pdf->SetXY(90, $pos_y);
                $pdf->Cell(50, 4, $this->iso($row[$it]->getApellido()), 0, 0);
                $pdf->SetXY(160, $pos_y);
                $pdf->Cell(10, 4, $row[$it]->getDocumento(), 0, 1);
                $pos_y += 5;
                $pdf->SetY($pos_y);
                $it++;
            }

            if ($pdf->PageNo() < $paginas) {
                $pdf->SetY($pos_y + 10);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo() . ' de {nb}', 0, 0, 'C');
                $pos_y = 25;
                $pdf->AddPage();
            } else {
                $pdf->SetY($pos_y + 10);
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->SetX(20);
                $pdf->Cell(0, 10, 'Cantidad de familiares: ' . $registros, 0, 0, 'L');
                $pdf->SetY($pos_y + 30);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(0, 10, 'Pagina ' . $pdf->PageNo() . ' de {nb}', 0, 0, 'C');
                $pos_y = 25;
            }
        }
        $pdf->Output();
    }

    public function nuevoAdelanto(): void {
        $this->requireAuth();
        $this->_view->titulo = 'Titulo';
        $this->_view->renderizar('nuevo-adelanto');
    }

    public function editarAdelanto(int $id): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->adelanto = $modelo->getAdelanto($id);
        $this->_view->renderizar('editar-adelanto');
    }

    public function listarParientes(int $idSocio): void {
        $this->requireAuth();
        $modelo = $this->loadModel('socios');
        $this->_view->idSocio = $idSocio;
        $this->_view->parientes = $modelo->getAllParentsBySocio($idSocio);
        $this->_view->renderizar('parientes');
    }

    public function guardarParientes(): void {
        $modelo = $this->loadModel('socios');
        if (!isset($_POST['idsoc'])) {
            return;
        }

        $idSocio = filter_input(INPUT_POST, 'idsoc', FILTER_VALIDATE_INT) ?: 0;
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;

        if ($id == 0) {
            $socio = $modelo->buildPariente();
            $socioRef = $modelo->getById($idSocio);
            $nombre = $this->capitalizeName((string) $_POST['nombre']);
            $apellido = $this->capitalizeName((string) $_POST['apellido']);
            $socio->setNombre($nombre);
            $socio->setApellido($apellido);
            $socio->setDocumento(filter_input(INPUT_POST, 'documento', FILTER_VALIDATE_INT) ?: 0);
            $socio->setParentezco(sanitize((string) $_POST['parentezco']));
            $socio->setSexo(sanitize((string) $_POST['sexo']));
            $socio->setSocio($socioRef);
            $socio->setFechaNacimiento((string) $_POST['fecha_nacimiento']);
            if ($modelo->savePariente($socio, Session::get('usuario')->idusuario)) {
                echo json_encode(['mensaje' => "Registro guardado", 'color' => 'green']);
            } else {
                echo json_encode(['mensaje' => "Ocurrio un error al guardar, verifique", 'color' => 'red']);
            }
        } else {
            $socio = $modelo->buildPariente();
            $socio->setId($id);
            $socio->setNombre($this->capitalizeName((string) $_POST['nombre']));
            $socio->setApellido($this->capitalizeName((string) $_POST['apellido']));
            $socio->setDocumento(filter_input(INPUT_POST, 'documento', FILTER_VALIDATE_INT) ?: 0);
            $socio->setParentezco(sanitize((string) $_POST['parentezco']));
            $socio->setSexo(sanitize((string) $_POST['sexo']));
            $socio->setFechaNacimiento((string) $_POST['fecha_nacimiento']);
            if ($modelo->savePariente($socio, Session::get('usuario')->idusuario)) {
                echo json_encode(['mensaje' => "Registro guardado", 'color' => 'green']);
            } else {
                echo json_encode(['mensaje' => "Ocurrio un error al guardar, verifique", 'color' => 'red']);
            }
        }
    }

    public function removePariente(): void {
        $id = sanitize((string) $_POST['id']);
        $modelo = $this->loadModel('socios');
        $modelo->removePariente($id);
        echo json_encode(['id' => $id]);
    }

    private function capitalizeName(string $name): string {
        return mb_convert_case(trim($name), MB_CASE_TITLE, "UTF-8");
    }

    private function iso(string $text): string {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }
}
