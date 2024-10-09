<!Doctype html>
<html>

<head>
  <meta charset="UTF-8">
  <title></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="<?php echo $_layoutParams['ruta_css'] ?>digg.css">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'public/js/menu/' ?>styles.css">
  <link rel="stylesheet" href="<?php echo BASE_URL ?>public/css/bootstrap.css">
  <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/jquery.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo BASE_URL . 'views/layout/default/js/funciones2.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/menu/script.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/bootstrap.min.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo BASE_URL . 'views/layout/default/js/md5.min.js' ?>"></script>

  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body style="font-family: 'Lato', system-ui;">
  <section id="main">
    <div id="app"></div>
    <header>
      <div id="img" style="display: flex;">
        <div class="titulo" style="color: #1c1c63;">
          <img src="<?php echo BASE_URL . 'public/img/logo.min.svg' ?>" style="margin: 1em; border-radius: unset; width: 100px; max-width: 100%;" alt="Encabezado" class="img-responsive img-circle">
        </div>
        <div class="container">
          <div class="titulo">
            <h2 style="font-family: 'Anton'; color:#1c1c63;"><?php echo NOMBRE ?></h2>
          </div>
          <div class="text-right titulo">
            <h2>Bienvenid@ <?php echo Session::get('usuario')->nombre; ?></h2>
          </div>
        </div>
      </div>
      <nav>
        <div id="cssmenu" style="z-index: 200;">

          <?php
          if (Session::get('usuario')->perfil == 1) {
            include APP_PATH . 'menus/menuAdmin.php';
          } elseif (Session::get('usuario')->perfil == 2) {
            include APP_PATH . 'menus/menuUsuario.php';
          } else {
            include APP_PATH . 'menus/menuCobrador.php';
          }
          ?>


        </div>
      </nav>

    </header>
    <article id="contenido">