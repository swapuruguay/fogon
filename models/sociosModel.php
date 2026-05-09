<?php

require_once 'Socio.php';
require_once 'Pariente.php';
require_once 'categoriasModel.php';

class sociosModel extends Model {

    private $_modeloCategorias;

    public function __construct() {
        parent::__construct();
        $this->_modeloCategorias = new categoriasModel();
    }

    private function validateOrder(string $orden, array $allowed = ['id_socio', 'nombre', 'apellido', 'apel']): string {
        return in_array($orden, $allowed, true) ? $orden : 'id_socio';
    }

    public function getAll(string $orden = 'id_socio'): array {
        $orden = $this->validateOrder($orden, ['id_socio', 'nombre', 'apellido', 'apel']);
        $listado = $this->_db->query("SELECT CONCAT(IFNULL(CONCAT(apellido, ' '),''),nombre) as apel,
            nombre, apellido, id_socio, domicilio, id_categoria_fk FROM socios WHERE estado='A' ORDER BY $orden")->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $socio = new Socio($valor->id_socio, $valor->nombre, $valor->apel);
            $socio->setCategoria($this->_modeloCategorias->getById($valor->id_categoria_fk));
            $socio->setDomicilio($valor->domicilio);
            $arreglo[] = $socio;
        }
        return $arreglo;
    }

    public function getAllParents(string $orden = 'id_socio'): array {
        $orden = $this->validateOrder($orden, ['id_socio', 'nombre', 'apellido', 'parentezco']);
        $sql = "SELECT parentezco, id_pariente, p.nombre, p.apellido, s.telefono, p.documento, p.id_socio
                FROM parientes p JOIN socios s ON p.id_socio = s.id_socio WHERE s.estado = 'a' ORDER BY $orden";
        $listado = $this->_db->query($sql)->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $pariente = new Pariente($valor->id_pariente, $valor->nombre, $valor->apellido);
            $pariente->setDocumento($valor->documento);
            $pariente->setParentezco($valor->parentezco);
            $pariente->setSocio($this->getById($valor->id_socio));
            $arreglo[] = $pariente;
        }
        return $arreglo;
    }

    public function getAllParentsBySocio(int $id, string $orden = 'parentezco'): array {
        $orden = $this->validateOrder($orden, ['id_pariente', 'nombre', 'apellido', 'parentezco']);
        $sql = "SELECT * FROM parientes WHERE id_socio = ? ORDER BY $orden";
        $listado = $this->_db->select($sql, [$id])->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $pariente = new Pariente($valor->id_pariente, $valor->nombre, $valor->apellido);
            $pariente->setSocio($this->getById($valor->id_socio));
            $pariente->setDocumento($valor->documento);
            $pariente->setSexo($valor->sexo);
            $pariente->setParentezco($valor->parentezco);
            $pariente->setFechaNacimiento($valor->fecha_nacimiento);
            $arreglo[] = $pariente;
        }
        return $arreglo;
    }

    public function getByParent(): array {
        $listado = $this->_db->query("SELECT sexo, parentezco, COUNT(parentezco) as conteo FROM parientes GROUP BY parentezco, sexo")->fetchAll(PDO::FETCH_ASSOC);
        if (count($listado) < 4) {
            $listado[] = ["sexo" => 'M', "parentezco" => 'C', "conteo" => 0];
        }
        return $listado;
    }

    public function getAdelantos(string $orden = 'id_socio_fk'): array {
        $orden = $this->validateOrder($orden, ['id_socio_fk', 'desde', 'hasta', 'nombre']);
        $sql = "SELECT a.*, s.nombre, s.apellido FROM adelantos a JOIN socios s ON s.id_socio = a.id_socio_fk ORDER BY $orden";
        $listado = $this->_db->query($sql)->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $arreglo[] = [
                'nombre' => $valor->nombre,
                'id' => $valor->idadelanto,
                'apellido' => $valor->apellido,
                'id_socio_fk' => $valor->id_socio_fk,
                'desde' => $valor->desde,
                'hasta' => $valor->hasta,
            ];
        }
        return $arreglo;
    }

    public function getAdelanto(int $id): array {
        $sql = "SELECT a.*, s.nombre, s.apellido FROM adelantos a JOIN socios s ON s.id_socio = a.id_socio_fk WHERE idadelanto = ?";
        $listado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        return [
            'nombre' => $listado->nombre,
            'id' => $listado->idadelanto,
            'apellido' => $listado->apellido,
            'id_socio_fk' => $listado->id_socio_fk,
            'desde' => $listado->desde,
            'hasta' => $listado->hasta,
        ];
    }

    public function getEliminados(): array {
        $listado = $this->_db->query("SELECT * FROM socios WHERE estado='B' ORDER BY nombre")->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $arreglo[] = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
        }
        return $arreglo;
    }

    public function getEliminadosAuto(string $emision): array {
        $sql = "SELECT * FROM socios s JOIN bajas b ON s.id_socio = b.id_socio_fk WHERE estado='B' AND b.fecha_baja = ? ORDER BY nombre";
        $listado = $this->_db->select($sql, [$emision])->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $arreglo[] = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
        }
        return $arreglo;
    }

    public function getPaginados(int $desde): array {
        $sql = "SELECT * FROM socios WHERE estado='A' ORDER BY id_socio LIMIT ?, 15";
        $listado = $this->_db->select($sql, [$desde])->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $arreglo[] = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
        }
        return $arreglo;
    }

    public function getPaginadosE(int $desde): array {
        $sql = "SELECT * FROM socios WHERE estado='B' ORDER BY nombre LIMIT ?, 15";
        $listado = $this->_db->select($sql, [$desde])->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $arreglo[] = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
        }
        return $arreglo;
    }

    public function getById(int $id): Socio {
        $sql = "SELECT * FROM socios WHERE id_socio = ?";
        $listado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        $socio = new Socio($listado->id_socio, $listado->nombre, $listado->apellido);
        $socio->setDocumento($listado->documento);
        $socio->setCategoria($this->_modeloCategorias->getById($listado->id_categoria_fk));
        $socio->setDomicilio($listado->domicilio);
        $socio->setEstado($listado->estado);
        $socio->setFechaIngreso($listado->fecha_ingreso);
        $socio->setFechaNacimiento($listado->fecha_nacimiento);
        $socio->setTelefono($listado->telefono);
        $socio->setEmail($listado->email);
        $socio->setExento($listado->exento == 1);
        $socio->setFoto($listado->foto);
        return $socio;
    }

    public function getParienteById(int $id): Pariente {
        $sql = "SELECT * FROM parientes WHERE id_pariente = ?";
        $listado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        $pariente = new Pariente($listado->id_pariente, $listado->nombre, $listado->apellido);
        $pariente->setDocumento($listado->documento);
        $pariente->setFechaNacimiento($listado->fecha_nacimiento);
        $pariente->setParentezco($listado->parentezco);
        $pariente->setSexo($listado->sexo);
        $pariente->setSocio($this->getById($listado->id_socio));
        return $pariente;
    }

    public function getByDocumento(int $id): ?Socio {
        $sql = "SELECT id_socio, nombre, apellido, documento FROM socios WHERE documento = ?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([$id]);
        $listado = $stmt->fetch(PDO::FETCH_OBJ);
        if ($listado) {
            $socio = new Socio($listado->id_socio, $listado->nombre, $listado->apellido);
            $socio->setDocumento($listado->documento);
            return $socio;
        }
        return null;
    }

    public function getParentByDocumento(int $id): ?Pariente {
        $sql = "SELECT * FROM parientes WHERE documento = ?";
        $listado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        if ($listado) {
            $pariente = new Pariente($listado->id_pariente, $listado->nombre, $listado->apellido);
            $pariente->setDocumento($listado->documento);
            return $pariente;
        }
        return null;
    }

    public function getByApellido(string $texto): array {
        $sql = "SELECT id_socio, nombre, apellido FROM socios
                WHERE (nombre LIKE ? OR apellido LIKE ?) AND estado='A'";
        return $this->_db->select($sql, ["%$texto%", "%$texto%"])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getByApellidoE(string $texto): array {
        $sql = "SELECT id_socio, nombre, apellido FROM socios
                WHERE (nombre LIKE ? OR apellido LIKE ?) AND estado='B'";
        return $this->_db->select($sql, ["$texto%", "$texto%"])->fetchAll(PDO::FETCH_OBJ);
    }

    public function save(Socio $socio, int $usuario): bool {
        $datos = [
            'nombre' => $socio->getNombre(),
            'apellido' => $socio->getApellido(),
            'documento' => $socio->getDocumento(),
            'domicilio' => $socio->getDomicilio(),
            'telefono' => $socio->getTelefono(),
            'fecha_nacimiento' => $socio->getFechaNacimiento(),
            'fecha_ingreso' => $socio->getFechaIngreso(),
            'email' => $socio->getEmail(),
            'foto' => $socio->getFoto(),
            'exento' => $socio->getExento() ? 1 : 0,
            'estado' => 'A',
            'id_categoria_fk' => $socio->getCategoria()->getId(),
            'usuario' => $usuario,
        ];
        return $this->_db->insert('socios', $datos);
    }

    public function savePariente(Pariente $pariente, int $usuario): bool {
        $datos = [
            'nombre' => $pariente->getNombre(),
            'apellido' => $pariente->getApellido(),
            'documento' => $pariente->getDocumento(),
            'parentezco' => $pariente->getParentezco(),
            'sexo' => $pariente->getSexo(),
            'fecha_nacimiento' => $pariente->getFechaNacimiento(),
            'id_socio' => $pariente->getSocio()->getId(),
            'usuario' => $usuario,
        ];
        return $this->_db->insert('parientes', $datos);
    }

    public function removePariente(int $id): bool {
        return $this->_db->delete('parientes', 'id_pariente = ?', [$id]);
    }

    public function update(Socio $socio, int $usuario): bool {
        $datos = [
            'nombre' => $socio->getNombre(),
            'apellido' => $socio->getApellido(),
            'documento' => $socio->getDocumento(),
            'domicilio' => $socio->getDomicilio(),
            'telefono' => $socio->getTelefono(),
            'fecha_nacimiento' => $socio->getFechaNacimiento(),
            'fecha_ingreso' => $socio->getFechaIngreso(),
            'email' => $socio->getEmail(),
            'exento' => $socio->getExento() ? 1 : 0,
            'id_categoria_fk' => $socio->getCategoria()->getId(),
            'usuario' => $usuario,
        ];
        if ($socio->getFoto()) {
            $datos['foto'] = $socio->getFoto();
        }
        return $this->_db->update('socios', $datos, 'id_socio = ?', [$socio->getId()]);
    }

    public function updatePariente(Pariente $pariente, int $usuario): bool {
        $datos = [
            'nombre' => $pariente->getNombre(),
            'apellido' => $pariente->getApellido(),
            'documento' => $pariente->getDocumento(),
            'fecha_nacimiento' => $pariente->getFechaNacimiento(),
            'parentezco' => $pariente->getParentezco(),
            'sexo' => $pariente->getSexo(),
            'usuario' => $usuario,
        ];
        return $this->_db->update('parientes', $datos, 'id_pariente = ?', [$pariente->getId()]);
    }

    public function buildSocio(): Socio {
        return new Socio(0, 'Nuevo', 'nuevo');
    }

    public function buildPariente(): Pariente {
        return new Pariente(0, 'Nuevo', 'nuevo');
    }

    public function delete(Socio $socio, int $usuario, string $tipo = 'C'): bool {
        $sql = "INSERT INTO bajas (id_socio_fk, fecha_baja, tipo) VALUES (?, DATE(NOW()), ?)";
        $flag = $this->_db->select($sql, [$socio->getId(), $tipo])->rowCount() > 0;
        if ($flag) {
            $sql = "UPDATE socios SET estado='B', usuario=? WHERE id_socio = ?";
            return $this->_db->select($sql, [$usuario, $socio->getId()])->rowCount() > 0;
        }
        return false;
    }

    public function getAtrasados(): array {
        $sql = "SELECT socios.id_socio, socios.nombre, socios.apellido, socios.id_categoria_fk,
                SUM(importe) as importe FROM socios JOIN cuotas ON cuotas.id_socio_fk = socios.id_socio
                WHERE socios.estado='A' GROUP BY id_socio ORDER BY importe DESC, apellido";
        $listado = $this->_db->query($sql)->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $socio = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
            $socio->setSaldo($valor->importe);
            $socio->setCategoria($this->_modeloCategorias->getById($valor->id_categoria_fk));
            $arreglo[] = $socio;
        }
        return $arreglo;
    }

    public function getHabilitados(): array {
        $listado = $this->_db->query("SELECT * FROM socios WHERE estado='A' AND exento=0")->fetchAll(PDO::FETCH_OBJ);
        $arreglo = [];
        foreach ($listado as $valor) {
            $socio = new Socio($valor->id_socio, $valor->nombre, $valor->apellido);
            $socio->setCategoria($this->_modeloCategorias->getById($valor->id_categoria_fk));
            $socio->setDomicilio($valor->domicilio);
            $arreglo[] = $socio;
        }
        return $arreglo;
    }

    public function activar(Socio $socio, int $usuario): bool {
        $sql = "UPDATE socios SET estado='A', usuario = ? WHERE id_socio = ?";
        return $this->_db->select($sql, [$usuario, $socio->getId()])->rowCount() > 0;
    }
}
