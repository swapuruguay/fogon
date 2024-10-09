<?php
$menu = array(
    'inicio' => array('display' => 'Inicio', 'url' => BASE_URL),
    'socios' => array(
                       'display' => 'Socios',
                       'url'     => BASE_URL .'socios#',
                       'sub'     => array(
                                'nuevo' => array(
                                            'display' => 'Nuevo',
                                            'url'     => BASE_URL .'socios/nuevo'
                                        ),
                                'listar' => array(
                                            'display' => 'Listar',
                                            'url'     => BASE_URL .'socios/listar'
                                        ),
                                'atrasados' => array('display' => 'Atrasados', 'url' => BASE_URL .'socios/atrasados'),
                                'eliminados' => array('display' => 'Eliminados', 'url' => BASE_URL .'socios/eliminados')

                       )
        ),
    'movimientos' => array(
                        'display' => 'Movimientos',
                        'url'     => BASE_URL .'movimientos',
                        'sub'     => array(
                                'generar'   => array('display' => 'Generar Mes', 'url' => BASE_URL .'movimientos/generar'),
                                'pagar'     => array('display' => 'Pagar', 'url' => BASE_URL . 'movimientos/pagar'),
                                'imprimir'  => array('display'  => 'Imprimir', 'url'    => BASE_URL . 'movimientos/preprint'),
                                'totales'  => array('display'  => 'Totales', 'url'    => BASE_URL . 'movimientos/totales')
                        )
    )
);

?>
