<?php

require_once 'Ajuste.php';

class ajustesModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function get(): Ajuste {
        $resultado = $this->_db->query("SELECT * FROM ajustes")->fetch(PDO::FETCH_OBJ);
        $izquierda = $resultado->izquierda ?? 0;
        return new Ajuste($resultado->margen, $resultado->espacio, $izquierda);
    }

    public function set(Ajuste $ajuste, int $usuario): bool {
        return $this->_db->update(
            'ajustes',
            [
                'margen' => $ajuste->getMargen(),
                'espacio' => $ajuste->getEspacio(),
                'izquierda' => $ajuste->getLeft(),
            ],
            '1=1'
        );
    }
}
