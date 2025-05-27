<!-- Error 404: Pagina no encontrada -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error 404</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php 
      include('navbar.php');
      require __DIR__ . '/../includes/firebase_fetch.php'; 
    ?>

    <div class="error-container text-center mt-5">
        <img src="assets/img/404.jpg" alt="Error 404" class="img-fluid" style="max-height: 300px;">
        <h1 class="display-4 mt-4">¡Ups! Aquí no hay nada.</h1>
        <p class="lead">Parece que esta página se fue a otro universo... o simplemente no existe.</p>
        <a href="index.php" class="btn btn-noresult mt-3">Volver al inicio</a>
    </div>

    <?php
      $productosBusqueda = obtenerProductosParaBusqueda();
      $datosParaJS = array_map(function($p) {
        return [
          'id' => $p['id'],
          'nombre' => $p['nombre'],
          'imagen' => $p['imagenes'][0] ?? 'assets/img/default.png'
        ];
      }, $productosBusqueda);
      ?>
      <script>
        const productoss = <?= json_encode($datosParaJS, JSON_UNESCAPED_UNICODE); ?>;
      </script>
<?php include('footer.php'); ?>
</body>
</html>