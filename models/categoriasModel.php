<?php

require_once 'Categoria.php';

class categoriasModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $consulta = $this->_db->query("SELECT * FROM categorias ORDER BY nombre");
        $retorno = [];
        foreach ($consulta->fetchAll(PDO::FETCH_OBJ) as $valor) {
            $retorno[] = new Categoria($valor->id_categoria, $valor->nombre, $valor->importe);
        }
        return $retorno;
    }

    public function getById(int $id): Categoria {
        $sql = "SELECT * FROM categorias WHERE id_categoria = ?";
        $resultado = $this->_db->select($sql, [$id])->fetch(PDO::FETCH_OBJ);
        return new Categoria($resultado->id_categoria, $resultado->nombre, $resultado->importe);
    }

    public function update(Categoria $categoria): bool {
        return $this->_db->update(
            'categorias',
            ['nombre' => $categoria->getNombre(), 'importe' => $categoria->getImporte()],
            'id_categoria = ?',
            [$categoria->getId()]
        );
    }

    public function buildCategoria(array $datos): Categoria {
        return new Categoria($datos['id'], $datos['nombre'], $datos['importe']);
    }
}
