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
$carrusel = obtenerImagenesCarrusel();
//echo "<pre>";
//print_r($carrusel);
//echo "</pre>";
?>

<!-- Carrusel de imágenes -->
<?php if (!empty($carrusel)): ?>
<?php if (!empty($carrusel)): ?>
<div id="carouselExampleIndicators" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php foreach ($carrusel as $index => $img): ?>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
    <?php endforeach; ?>
  </div>
  <div class="carousel-inner">
    <?php foreach ($carrusel as $index => $img): ?>
      <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
        <img src="<?= htmlspecialchars($img['url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($img['titulo'] ?? 'Imagen del carrusel') ?>">
        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
          <h5><?= htmlspecialchars($img['titulo']) ?></h5>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Anterior</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Siguiente</span>
  </button>
</div>
<?php endif; ?>

<?php else: ?>
  ⚠️ No se cargó el carrusel
<?php endif; ?>

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
          <div class="card-body text-center">
              <a href="detalles.php?id=<?= $producto['id'] ?>" class="btn btn-vermas">>>> Ver más</a>
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
          <div class="card-body text-center">
              <a href="detalles.php?id=<?= $prodprev['id'] ?>" class="btn btn-vermas">>>> Ver más</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

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

<!-- Footer del sitio -->
<?php include('footer.php'); ?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
