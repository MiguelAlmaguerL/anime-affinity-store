<!-- productos.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ver todos los Productos</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-4">
  <h2 class="text-center mb-5">Todos los Productos</h2>

  <!-- Botón para abrir filtros en modo móvil -->
<div class="d-block d-md-none text-center mb-4">
  <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros">
    Filtrar Productos
  </button>
</div>


  <div class="row">
    <!-- Columna de Filtros -->
    <?php include('filtros-sidebar.php'); ?>

    <!-- Columna de productos -->
    <div class="col-md-9">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <!-- Ejemplos de productos -->
        <div class="col">
          <div class="card h-100 product-card">
            <img src="assets/img/ejemplo1.png" class="card-img-top" alt="Figura 1">
            <div class="card-body">
              <h5 class="product-title"><a href="detalles.php?id=1" class="text-decoration-none text-dark">Zoro Roronoa</a></h5>
              <p class="card-text text-center">Figura de acción detallada con katana. Altura: 20 cm.</p>
              <p class="product-price">$1,199.00 MXN</p>
            </div>
            <div class="card-body text-center">
              <a href="detalles.php?id=1" class="btn btn-vermas">>>>Ver más</a>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card h-100 product-card">
            <img src="assets/img/ejemplo2.jpg" class="card-img-top" alt="Figura 2">
            <div class="card-body">
              <h5 class="product-title"><a href="detalles.php?id=2" class="text-decoration-none text-dark">Gojo Satoru</a></h5>
              <p class="card-text text-center">Figura edición limitada con base personalizada. Altura: 25 cm.</p>
              <p class="product-price">$6,199.00 MXN</p>
            </div>
            <div class="card-body text-center">
              <a href="detalles.php?id=2" class="btn btn-vermas">>>>Ver más</a>
            </div>
          </div>
        </div>

        <!-- Más productos... -->
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