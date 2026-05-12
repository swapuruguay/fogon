<?php

use App\Core\Model;

require_once 'Movimiento.php';
require_once 'Socio.php';
require_once 'sociosModel.php';

class movimientosModel extends Model {

    private $_modeloSocios;

    public function __construct() {
        parent::__construct();
        $this->_modeloSocios = new sociosModel();
    }

    public function getById(int $id): Movimiento {
        $sql = "SELECT * FROM cuotas WHERE id_cuota = ?";
        $listado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        return new Movimiento(
            $listado->id_cuota,
            $this->_modeloSocios->getById($listado->id_socio_fk),
            $listado->mes,
            $listado->anio
        );
    }

    private function fechaHasta(int $mes, int $anio): string {
        $date = new DateTime("$anio-$mes-01");
        return $date->format('Y-m-t');
    }

    public function getAll(): void {}

    public function save(Movimiento $movimiento, string $tipo = 'S'): bool {
        $datos = [
            'id_socio_fk' => $movimiento->getSocio()->getId(),
            'mes' => $movimiento->getMes(),
            'anio' => $movimiento->getAnio(),
            'fecha_computo' => $movimiento->getFecha(),
            'importe' => $movimiento->getImporte(),
            'tipo' => $tipo,
        ];
        return $this->_db->insert('cuotas', $datos);
    }

    public function saveAdelanto(array $adelanto): bool {
        $datos = [
            'id_socio_fk' => $adelanto['id'],
            'desde' => $adelanto['desde'],
            'hasta' => $adelanto['hasta'],
        ];
        return $this->_db->insert('adelantos', $datos);
    }

    public function update(Movimiento $movimiento): bool {
        $datos = [
            'id_socio_fk' => $movimiento->getSocio()->getId(),
            'mes' => $movimiento->getMes(),
            'anio' => $movimiento->getAnio(),
            'fecha_computo' => $movimiento->getFecha(),
            'importe' => $movimiento->getImporte(),
        ];
        return $this->_db->update('cuotas', $datos, 'id_cuota = ?', [$movimiento->getId()]);
    }

    public function delete(Movimiento $movimiento): bool {
        return $this->_db->delete('cuotas', 'id_cuota = ?', [$movimiento->getId()]);
    }

    public function getSaldo(Socio $socio): array {
        $sql = "SELECT SUM(importe) AS saldo FROM cuotas WHERE id_socio_fk = ?";
        return $this->_db->select($sql, [$socio->getId()])->fetchAll(PDO::FETCH_OBJ);
    }

    public function buildMovimiento(): Movimiento {
        return new Movimiento(0);
    }

    public function getLasts(string $fecha, int $limit = 0): array {
        if ($limit > 0) {
            $sql = "SELECT * FROM cuotas WHERE fecha_computo = ? ORDER BY id_cuota DESC LIMIT ?";
            return $this->_db->select($sql, [$fecha, $limit])->fetchAll(PDO::FETCH_OBJ);
        }
        $sql = "SELECT * FROM cuotas WHERE fecha_computo = ? ORDER BY id_cuota DESC";
        return $this->_db->select($sql, [$fecha])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUltimaCuota(): ?object {
        $sql = "SELECT * FROM cuotas WHERE importe < 0 AND DAY(fecha_computo) > 24 ORDER BY id_cuota DESC LIMIT 1";
        return $this->_db->query($sql)->fetch(PDO::FETCH_OBJ);
    }

    public function getMes(int $anio, int $mes, int $dire = 3): array {
        $fogon = '';
        if ($dire == 1) {
            $fogon = " AND c.cobrado = 'F'";
        } elseif ($dire == 2) {
            $fogon = " AND c.cobrado = 'C'";
        }

        $sql = "SELECT c.mes, c.anio, c.importe, c.estado, s.nombre, s.apellido, s.id_socio, c.id_socio_fk
                FROM cuotas c JOIN socios s ON s.id_socio = c.id_socio_fk
                WHERE fecha_computo = ? $fogon AND importe > 0";
        return $this->_db->select($sql, ["$anio-$mes-01"])->fetchAll(PDO::FETCH_OBJ);
    }

    public function verificarMes(int $mes, int $anio): bool {
        $sql = "SELECT * FROM mesesgenerados WHERE mes = ? AND anio = ?";
        $result = $this->_db->select($sql, [$mes, $anio])->fetchAll(PDO::FETCH_OBJ);
        if ($result) {
            return false;
        }
        $sql = "INSERT INTO mesesgenerados (mes, anio, fecha_generado) VALUES (?, ?, DATE(NOW()))";
        $this->_db->select($sql, [$mes, $anio]);
        return true;
    }

    public function lastEmision(): ?object {
        $sql = "SELECT * FROM mesesgenerados ORDER BY idmes DESC LIMIT 1";
        return $this->_db->query($sql)->fetch(PDO::FETCH_OBJ);
    }

    public function generarMes(array $socios, int $mes, int $anio): bool {
        foreach ($socios as $s) {
            $sql = "SELECT * FROM adelantos WHERE id_socio_fk = ? AND desde <= ? AND hasta >= ?";
            $result = $this->_db->select(
                $sql,
                [$s->getId(), "$anio-$mes-01", $this->fechaHasta($mes, $anio)]
            )->fetchAll(PDO::FETCH_OBJ);

            $cobrado = mb_strtolower($s->getDomicilio(), 'UTF-8');
            $cobrado = strpos($cobrado, 'fog') !== false ? 'F' : 'C';

            if (!$result) {
                $datos = [
                    'id_socio_fk' => $s->getId(),
                    'mes' => $mes,
                    'anio' => $anio,
                    'fecha_computo' => "$anio-$mes-01",
                    'importe' => $s->getCategoria()->getImporte(),
                    'cobrado' => $cobrado,
                ];
                $this->_db->insert('cuotas', $datos);
            } else {
                $datos = [
                    'id_socio_fk' => $s->getId(),
                    'mes' => $mes,
                    'anio' => $anio,
                    'fecha_computo' => "$anio-$mes-01",
                    'importe' => 0,
                    'estado' => 'P',
                ];
                $this->_db->insert('cuotas', $datos);
            }
        }
        return true;
    }

    public function getMovimientosSocio(Socio $s, int $limit = 0): array {
        if ($limit === 0) {
            $sql = "SELECT * FROM cuotas WHERE id_socio_fk = ? ORDER BY fecha_computo";
            return $this->_db->select($sql, [$s->getId()])->fetchAll(PDO::FETCH_OBJ);
        }
        $sql = "SELECT * FROM cuotas WHERE id_socio_fk = ? AND DAY(fecha_computo) = '01' ORDER BY fecha_computo DESC LIMIT ?";
        return $this->_db->select($sql, [$s->getId(), $limit])->fetchAll(PDO::FETCH_OBJ);
    }

    public function getTotales(string $fecha, int $dire = 3): array {
        $fogon = '';
        if ($dire == 1) {
            $fogon = " AND cuotas.cobrado = 'F'";
        } elseif ($dire == 2) {
            $fogon = " AND cuotas.cobrado = 'C'";
        }
        $sql = "SELECT COUNT(*) as cantidad, ABS(SUM(cuotas.importe)) AS importe, categorias.nombre as cat
                FROM cuotas JOIN socios ON id_socio = id_socio_fk
                JOIN categorias ON id_categoria = id_categoria_fk
                WHERE fecha_computo = ? $fogon GROUP BY id_categoria_fk";
        return $this->_db->select($sql, [$fecha])->fetchAll(PDO::FETCH_OBJ);
    }
}
