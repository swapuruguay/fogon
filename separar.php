<?php
  $mysqli = new mysqli('localhost', 'root', 'ws7', 'fogon');
  $sql = "SELECT nombre FROM socios WHERE apellido IS NULL";
  $result = $mysqli->query($sql);
  if($row = $result->mysqli_fetch_object()) {
    print_r($row);
  }

 ?>
