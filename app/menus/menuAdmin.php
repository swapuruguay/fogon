<ul class="has-sub">
  <li><a href="<?php echo BASE_URL ?>">Inicio</a></li>
  <li><a href="<?php echo BASE_URL . 'socios#' ?>">Socios</a>
    <ul>
      <li><a href="<?php echo BASE_URL . 'socios/nuevo' ?>" >Nuevo</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/listar' ?>">Listar</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/atrasados' ?>">Atrasados</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/eliminados' ?>">Eliminados</a></li>
      <li><a href="<?php echo BASE_URL . 'socios/listarsocios' ?>">Imprimir listado socios</a></li>
      
    </ul>
  </li>
  <li><a href="<?php echo BASE_URL . 'movimientos#' ?>">Movimientos</a>
    <ul>
      <li><a href="<?php echo BASE_URL . 'movimientos/generar' ?>">Generar Mes</a></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/pagar' ?>">Pagar</a></li></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/adelantos' ?>">Pagos Adelantados</a></li></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/manual' ?>">Cobros Manuales</a></li></li>
      
      <li><a href="<?php echo BASE_URL . 'movimientos/preprint' ?>">Imprimir</a></li></li>
      <li><a id="menu_recibos1" href="<?php echo BASE_URL . 'movimientos/listarec' ?>">Listado recibos fogón</a></li></li>
      <li><a id="menu_recibos2" href="<?php echo BASE_URL . 'movimientos/listarec' ?>">Listado recibos cobrador</a></li></li>
      <li><a href="<?php echo BASE_URL . 'movimientos/choose' ?>">Imprimir Seleccionados</a></li></li>
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
<script type="text/javascript">
  let menu = document.getElementById('menu_recibos1')
  menu.addEventListener('click', async (evt) => {
    evt.preventDefault();
    let url = evt.target.href
    let respuesta = await fetch(`/movimientos/getLastEmitido`, {
      method: 'post',
      
    })
    let res = await respuesta.json()
    url = `${url}/${res.mes}/${res.anio}/1`
    window.location.href = url
    
  })
  let menu2 = document.getElementById('menu_recibos2')
  menu2.addEventListener('click', async (evt) => {
    evt.preventDefault();
    let url = evt.target.href
    let respuesta = await fetch(`/movimientos/getLastEmitido`, {
      method: 'post',
      
    })
    let res = await respuesta.json()
    url = `${url}/${res.mes}/${res.anio}/2`
    window.location.href = url
    
  })
</script>