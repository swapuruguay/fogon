<ul class="has-sub">
  <li><a href="<?php echo BASE_URL ?>">Inicio</a></li>
  <li><a href="<?php echo BASE_URL . 'socios#' ?>">Socios</a>
    <ul>
      <li><a href="<?php echo BASE_URL . 'socios/nuevo' ?>" >Nuevo</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/listar' ?>">Listar</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/atrasados' ?>">Atrasados</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/eliminados' ?>">Eliminados</a></li>
    </ul>
  </li>
  <li><a href="<?php echo BASE_URL . 'movimientos#' ?>">Movimientos</a>
    <ul>
      <li><a href="<?php echo BASE_URL . 'movimientos/generar' ?>">Generar Mes</a></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/pagar' ?>">Pagar</a></li></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/preprint' ?>">Imprimir</a></li></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/totales' ?>">Totales</a></li></li>
    </ul>
  </li>
  <li><a href="<?php echo BASE_URL . 'usuarios#' ?>">Usuario</a>
    <ul>
      <li><a href="<?php echo BASE_URL . 'usuarios/cambiar'?>" >Cambiar contraseña</a></li>
      <li><a href="<?php echo BASE_URL . 'usuarios/logout'?>">Cerrar sesión</a></li>
    </ul>
  </li>
</ul>
