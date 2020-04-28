<!Doctype html>
<html>
    <head>
        <meta  charset="UTF-8">
        <title></title>

        <link rel="stylesheet" type="text/css" href="<?php echo $_layoutParams['ruta_css']?>digg.css">
        <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'public/js/menu/'?>styles.css">
        <link rel="stylesheet" href="<?php echo BASE_URL ?>public/css/bootstrap.css">
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/jquery.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'views/layout/default/js/funciones2.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/menu/script.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/bootstrap.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'views/layout/default/js/md5.min.js'?>"></script>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body>
        <section id="main">
            <header>
                <div id="img">
                    <div class="titulo"><img src="<?php echo BASE_URL . 'public/img/encabezado.redimensionado.png' ?>" alt="Encabezado" class="img-responsive img-circle" ></div>
              <div class="container">
                <div class="titulo"><h2><?php echo NOMBRE ?></h2></div>
                <div class="text-right titulo">
                  <h2>Bienvenid@ <?php echo Session::get('usuario')->nombre; ?></h2>
                </div>
              </div>
                </div>

                <nav>
                    <div id="cssmenu" style="z-index: 200;">

                        <?php
                            if(Session::get('usuario')->perfil == 1) {
                              include APP_PATH . 'menus/menuAdmin.php';
                            } elseif(Session::get('usuario')->perfil == 2) {
                              include APP_PATH . 'menus/menuUsuario.php';
                            } else {
                              include APP_PATH . 'menus/menuCobrador.php';
                            }
                        ?>


                    </div>
                </nav>

            </header>
            <article id="contenido">
