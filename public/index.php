<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anime Affinity Store</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- Barra de navegación del sitio -->
<?php include('navbar.php'); ?>

<!-- Carrusel fuera de container -->
<div id="carouselExampleIndicators" class="carousel slide debug-border" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="assets/img/banner1.jpg" class="d-block w-100" alt="Figura destacada 1">
    </div>
    <div class="carousel-item">
      <img src="assets/img/banner2.jpg" class="d-block w-100" alt="Figura destacada 2">
    </div>
    <div class="carousel-item">
      <img src="assets/img/banner3.jpg" class="d-block w-100" alt="Figura destacada 3">
    </div>
  </div>
</div>

<!-- Sección de Productos Recientes -->
<section class="container mb-5">
  <h2 class="text-center mb-4">Productos Recientes</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">

    <!-- Tarjeta de producto ejemplo -->
    <div class="col">
      <div class="card h-100 product-card">
        <img src="assets/img/ejemplo1.png" class="card-img-top" alt="Producto 1">
        <div class="card-body">
          <h5 class="product-title text-center">Zoro Roronoa</h5>
          <p class="product-price text-center">$1,199.00 MXN</p>
        </div>
      </div>
    </div>

    <!-- Más tarjetas de productos recientes -->
    <div class="col">
      <div class="card h-100 product-card">
        <img src="assets/img/ejemplo2.jpg" class="card-img-top" alt="Producto 2">
        <div class="card-body">
          <h5 class="product-title text-center">Gojo Satoru</h5>
          <p class="product-price text-center">$6,199.00 MXN</p>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100 product-card">
        <img src="assets/img/ejemplo3.jpg" class="card-img-top" alt="Producto 3">
        <div class="card-body">
          <h5 class="product-title text-center">Tanjiro Kamado</h5>
          <p class="product-price text-center">$2,499.00 MXN</p>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- Sección de Productos en Preventa -->
<section class="container mb-5">
  <h2 class="text-center mb-4">Productos en Preventa</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">

    <!-- Tarjeta de preventa ejemplo -->
    <div class="col">
      <div class="card h-100 product-card">
        <img src="assets/img/ejemplo2.jpg" class="card-img-top" alt="Preventa 1">
        <div class="card-body">
          <h5 class="product-title text-center">Gojo Satoru (Preventa)</h5>
          <p class="product-price text-center">$6,999.00 MXN</p>
        </div>
      </div>
    </div>

    <!-- Más tarjetas de preventa -->
    <div class="col">
      <div class="card h-100 product-card">
        <img src="assets/img/ejemplo1.png" class="card-img-top" alt="Preventa 2">
        <div class="card-body">
          <h5 class="product-title text-center">Luffy Gear 5 (Preventa)</h5>
          <p class="product-price text-center">$8,499.00 MXN</p>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- Footer del sitio -->
<?php include('footer.php'); ?>

</body>
</html>
