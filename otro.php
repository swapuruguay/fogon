<?php
    $cadena = 'el FOGÓN';
    $cadena = mb_strtolower($cadena, 'UTF-8');
    echo $cadena;
    //replace 'ó' by 'o' and Ó by O
    $cadena = str_replace('ó', 'o', $cadena);
    if(strpos($cadena, 'fogon') !== false){
        echo 'La cadena contiene la palabra fogon';
    } else {
        echo 'La cadena NO contiene la palabra fogon';
    }
?>