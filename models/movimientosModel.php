<?php



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of movimientosModel
 *
 * @author walter
 */
require_once 'Movimiento.php';
require_once 'Socio.php';
require_once 'sociosModel.php';

class movimientosModel extends Model{

    private $_modeloSocios;

    public function __construct() {
        parent::__construct();
        $this->_modeloSocios = new sociosModel();
    }

    /**
     * Devuelve un Movimiento buscando por el parámetro id
     * @param int $id
     * @return Movimiento
     */
    public function getById($id) {

        $listado = $this->_db->query("SELECT * FROM cuotas WHERE id_cuota = " .$id);
        $listado = $listado->fetch(PDO::FETCH_OBJ);

        $movimiento = new Movimiento($listado->id_cuota, $this->_modeloSocios->getById($listado->id_socio_fk), $listado->mes, $listado->anio);
        $movimiento->setFecha($listado->fecha_computo);
        return $movimiento;

    }

    /**
     * Devuelve un arreglo con todos los movimientos
     * @return array Movimiento
     */
    public function getAll() {

    }

    /**
     * Guarda un Socio en la base de datos
     * @param Socio $socio
     */
    public function save(Movimiento $movimiento) {
        $datos = array(

            'id_socio_fk'      => $movimiento->getSocio()->getId(),
            'mes'              => $movimiento->getMes(),
            'anio'             => $movimiento->getAnio(),
            'fecha_computo'    => $movimiento->getFecha(),
            'importe'          => $movimiento->getImporte()

        );
        $sql = 'INSERT INTO cuotas ' . $this->preparaInsert($datos);

        return $this->_db->query($sql);
    }

    /**
     * Actualiza en la base de datos el Socio
     * @param Movimiento $movimiento
     */
    public function update(Movimiento $movimiento) {
        $datos = array(

            'socio'            => $movimiento->getSocio()->getId(),
            'mes'              => $movimiento->getMes(),
            'anio'             => $movimiento->getAnio(),
            'fecha_computo'    => $movimiento->getFecha(),
            'importe'          => $movimiento->getImporte()

        );
        $sql = 'UPDATE cuotas SET ' . $this->preparaUpdate($datos) . ' WHERE id_cuota=' . $movimiento->getId();

        return $this->_db->query($sql);
    }

    /**
     * Borra el Movimiento pasado por parámetro
     * @param Movimiento $movimiento
     */
    public function delete(Movimiento $movimiento) {
        $sql = 'DELETE FROM cuotas WHERE id_cuota = ' . $movimiento->getId();
        return $this->_db->query($sql);
    }


    public function getSaldo(Socio $socio){
        $listado = $this->_db->query("SELECT SUM(importe) AS saldo FROM cuotas "
                . "WHERE id_socio_fk = " .$socio->getId());
        return $listado->fetchall(PDO::FETCH_OBJ);


    }

    public function buildMovimiento() {
        return new Movimiento(0);
    }

    public function getLasts($fecha, $limit) {
        $listado = $this->_db->query("SELECT * FROM cuotas WHERE fecha_computo='$fecha' ORDER BY id_cuota DESC LIMIT 0,$limit");
        return  $listado->fetchall(PDO::FETCH_OBJ);

    }

    public function getMes($anio, $mes, $dire=3) {
        //$fogon = ($dire == 1)? ' LIKE ': ' NOT LIKE ';
        $fogon = '';
        if($dire == 1) {
            $fogon = " AND LIKE '%fogon%'";
        } elseif(!$dire) {
            $fogon = " AND NOT LIKE '%fogon%'";
        }

        $sql = "SELECT c.mes, c.anio, c.importe, s.nombre, s.apellido, s.id_socio, c.id_socio_fk FROM cuotas c JOIN socios s ON 
                s.id_socio = c.id_socio_fk WHERE s.domicilio fecha_computo= '$anio-".$mes."-01' $fogon  AND importe > 0";
        $listado = $this->_db->query($sql);
        return  $listado->fetchall(PDO::FETCH_OBJ);
    }

    public function verificarMes($mes, $anio) {
        $result = $this->_db->query("SELECT * FROM mesesgenerados WHERE mes = $mes AND anio=$anio");
        if($result->fetchall(PDO::FETCH_OBJ)) {
             return false;
         } else {
            $this->_db->query("INSERT INTO mesesgenerados (mes, anio) VALUES($mes, $anio)");
            return true;
         }
    }

    public function generarMes($socios, $mes, $anio) {
        foreach($socios as $s) {
            $result = $this->_db->query("SELECT * FROM adelantos WHERE id_socio_fk"
                    . " = ".$s->getId()." AND desde <= '".$anio."-".$mes."-01' AND hasta >='".$anio."-".$mes."-31'");
            if(!$result->fetchall(PDO::FETCH_OBJ)) {

                $datos = array(
                'id_socio_fk'      => $s->getId(),
                'mes'              => $mes,
                'anio'             => $anio,
                'fecha_computo'    => $anio.'-'.$mes.'-01',
                'importe'          => $s->getCategoria()->getImporte()
                 );
                $sql = 'INSERT INTO cuotas ' . $this->preparaInsert($datos);
                $this->_db->query($sql);
            }
        }
        return true;



    }

    public function getMovimientosSocio(Socio $s) {
        $sql = "SELECT * FROM cuotas WHERE id_socio_fk=".$s->getId()." ORDER BY fecha_computo";
        $listado = $this->_db->query($sql);
        $result = $listado->fetchall(PDO::FETCH_OBJ);
        return $result;

    }

    public function getTotales($fecha) {
        $sql = "SELECT COUNT(*) as cantidad,ABS(SUM(cuotas.importe)) AS importe, categorias.nombre as cat FROM cuotas,socios,categorias WHERE id_categoria=id_categoria_fk
                  and id_socio=id_socio_fk and fecha_computo='$fecha' GROUP BY id_categoria_fk";
        $listado = $this->_db->query($sql);
        return  $listado->fetchall(PDO::FETCH_OBJ);
    }


}
