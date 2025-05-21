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

<!-- Obtener productos recientes -->
<?php
require __DIR__ . '/../includes/firebase_fetch.php';
$recientes = obtenerProductosInventario();
$preventas = obtenerProductosPreventa();
?>

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
  <h2 class="text-center mb-5">Productos en Inventario Recientes</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
    <?php foreach ($recientes as $producto): ?>
      <div class="col">
        <div class="card h-100 product-card">
          <img src="<?= htmlspecialchars($producto['imagenes'][0]) ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>">
          <div class="card-body">
            <h5 class="product-title text-center"><?= htmlspecialchars($producto['nombre']) ?></h5>
            <p class="product-price text-center">$<?= number_format($producto['precio'], 2) ?> MXN</p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Sección de Productos Recientes -->
<section class="container mb-5">
  <h2 class="text-center mb-5">Productos de Preventa Recientes</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
    <?php foreach ($preventas as $prodprev): ?>
      <div class="col">
        <div class="card h-100 product-card">
          <img src="<?= htmlspecialchars($prodprev['imagenes'][0]) ?>" class="card-img-top" alt="<?= htmlspecialchars($prodprev['nombre']) ?>">
          <div class="card-body">
            <h5 class="product-title text-center"><?= htmlspecialchars($prodprev['nombre']) ?></h5>
            <p class="product-price text-center">$<?= number_format($prodprev['precio'], 2) ?> MXN</p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Footer del sitio -->
<?php include('footer.php'); ?>

</body>
</html>
