
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <form id="form" action="otro.php" method="POST">
    <input type="text" name="socio">
    <input type="text" name="mes">
    <input type="text" name="anio">
    <button id="boton" type="button">Agrega</button>
    <button id="boton2" type="submit">Enviar</button>
  </form>

  <script>
    $form = document.querySelector('#form')
    $boton = document.querySelector('#boton')
    $boton.addEventListener('click', evt =>{
      if($form.socio.value && $form.mes.value && $form.anio.value) {
        const elsocio = document.createElement('input')
        elsocio.setAttribute('type', 'hidden')
        elsocio.setAttribute('name', 'socio[]')
        elsocio.value = $form.socio.value
        form.appendChild(elsocio)
        
        const elmes = document.createElement('input')
        elmes.setAttribute('type', 'hidden')
        elmes.setAttribute('name', 'mes[]')
        elmes.value = $form.mes.value
        form.appendChild(elmes)
        
        const elanio = document.createElement('input')
        elanio.setAttribute('type', 'hidden')
        elanio.setAttribute('name', 'anio[]')
        elanio.value = $form.anio.value
        form.appendChild(elanio)
      } else {
        document.body.append('Hay campos vacios')
      }
    })
      
  </script>
</body>
</html>