<?php



class View
{
    private $_controlador;

    private $_js;

    public function __construct(Request $peticion) {
        $this->_controlador = $peticion->getControlador();
    }

    public function renderizar($vista, $item = false)
    {
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

        $js = array();

        if(count($this->_js)) {
            $js = $this->_js;
        }
        $arbolMenu = $this->generarMenu($menu);

        $_layoutParams = array(
            'ruta_css' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/css/',
            'ruta_img' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/img/',
            'ruta_js' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/js/',
            'menu' => $arbolMenu,
            'js'   => $js
        );

        $rutaView = ROOT . 'views' . DS . $this->_controlador . DS . $vista . '.phtml';

        if(is_readable($rutaView)){
            if($l) {
              include_once ROOT . 'views'. DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'header.php';
              include_once $rutaView;
              include_once ROOT . 'views'. DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'footer.php';
            } else {
              include_once $rutaView;
            }
        }
        else {
            throw new Exception('Error de vista');
        }
    }

    public function setJs(array $js) {
        if(is_array($js) && count($js)) {
            for($i=0; $i < count($js);$i++) {
                $this->_js[] = BASE_URL . '/views/' . $this->_controlador . '/js/' . $js[$i] . '.js';
            }
        }
    }

    public function generarMenu($menu_array, $is_sub=FALSE) {

	/*
	 * If the supplied array is part of a sub-menu, add the
	 * sub-menu class instead of the menu ID for CSS styling
	 */
	$attr = (!$is_sub) ? ' class="has-sub"' : '';
	$menu = "<ul$attr>"; // Open the menu container

	/*
	 * Loop through the array to extract element values
	 */
	foreach($menu_array as $id => $properties) {

		/*
		 * Because each page element is another array, we
		 * need to loop again. This time, we save individual
		 * array elements as variables, using the array key
		 * as the variable name.
		 */
		foreach($properties as $key => $val) {

			/*
			 * If the array element contains another array,
			 * call the buildMenu() function recursively to
			 * build the sub-menu and store it in $sub
			 */
			if(is_array($val))
			{
				$sub = $this->generarMenu($val, TRUE);
			}

			/*
			 * Otherwise, set $sub to NULL and store the
			 * element's value in a variable
			 */
			else
			{
				$sub = NULL;
				$$key = $val;
			}
		}

		/*
		 * If no array element had the key 'url', set the
		 * $url variable equal to the containing element's ID
		 */
		if(!isset($url)) {
			$url = $id;
		}

		/*
		 * Use the created variables to output HTML
		 */
		$menu .= "<li><a href='".$url."'>".$display."</a>".$sub."</li>";

		/*
		 * Destroy the variables to ensure they're reset
		 * on each iteration
		 */
		unset($url, $display, $sub);

	}

	/*
	 * Close the menu container and return the markup for output
	 */
	return $menu . "</ul>";
    }
}
