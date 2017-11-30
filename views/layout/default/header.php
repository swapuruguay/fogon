<!Doctype html>
<html>
    <head>
        <meta  charset="UTF-8">
        <title></title>

        <link rel="stylesheet" type="text/css" href="<?php echo $_layoutParams['ruta_css']?>digg.css">
        <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'public/js/menu/'?>styles.css">
        <link rel="stylesheet" href="<?php echo BASE_URL ?>public/css/bootstrap.css">
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/jquery.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'views/layout/default/js/funciones.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/menu/script.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo BASE_URL . 'public/js/bootstrap.min.js'; ?>"></script>
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body>
        <section id="main">
            <header>
                <div id="img">
                    <div class="titulo"><img src="<?php echo BASE_URL . 'public/img/encabezado.redimensionado.png' ?>" alt="Encabezado" class="img-responsive img-circle" ></div>
                <div class="titulo"><h2><?php echo NOMBRE ?></h2></div>
                </div>

                <nav>
                    <div id="cssmenu" style="z-index: 200;">

                        <?php echo $_layoutParams['menu']; ?>


                    </div>
                </nav>

            </header>
            <article id="contenido">
