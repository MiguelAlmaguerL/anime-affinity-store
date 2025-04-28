<!-- preventas.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos en Preventa</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-4">
  <h2 class="text-center mb-5">Productos en Preventa</h2>

  <!-- Botón para abrir filtros en modo móvil -->
<div class="d-block d-md-none text-center mb-4">
  <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros">
    Filtrar Productos
  </button>
</div>


  <div class="row">
    <?php include('filtros-sidebar.php'); ?>

    <div class="col-md-9">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <div class="col">
          <div class="card h-100 product-card">
            <img src="assets/img/ejemplo_preventa.jpg" class="card-img-top" alt="Producto Preventa">
            <div class="card-body">
              <h5 class="product-title"><a href="detalles.php?id=101" class="text-decoration-none text-dark">Figura Especial</a></h5>
              <p class="card-text text-center">Edición limitada de colección. Altura: 30 cm.</p>
              <p class="product-price">$2,999.00 MXN</p>
            </div>
            <div class="card-body text-center">
              <a href="detalles.php?id=101" class="btn btn-vermas">>>>Ver más</a>
            </div>
          </div>
        </div>

        <!-- Si no hay preventas -->
        <!-- <div class="col-12 text-center my-5 ps-4">
          <img src="assets/img/not-found.png" alt="No encontrado">
          <h3 class="mt-4">Ups! No hay coincidencias</h3>
          <p>Intenta nuevamente :D</p>
          <a href="productos.php" class="btn btn-noresult mt-3">Volver a todos los productos</a>
        </div> -->
      </div>
    </div>
  </div>
  <!-- Offcanvas de Filtros para móviles -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offcanvasFiltrosLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasFiltrosLabel">Filtrar Productos</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body">
   <?php $desdeOffcanvas = true; include('filtros-sidebar.php'); ?>
  </div>
</div>

</div>

<?php include('footer.php'); ?>

</body>
</html>