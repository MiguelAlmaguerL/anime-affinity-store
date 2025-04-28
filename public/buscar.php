<!-- buscar.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados de Búsqueda</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<?php
$productosEncontrados = [
  [
    "nombre" => "Zoro Roronoa",
    "descripcion" => "Figura de acción detallada con katana. Altura: 20 cm.",
    "precio" => "$1,199.00 MXN",
    "imagen" => "assets/img/ejemplo1.png",
    "id" => 1
  ],
  [
    "nombre" => "Gojo Satoru",
    "descripcion" => "Figura edición limitada con base personalizada. Altura: 25 cm.",
    "precio" => "$6,199.00 MXN",
    "imagen" => "assets/img/ejemplo2.jpg",
    "id" => 2
  ]
];
$terminoBusqueda = isset($_GET['query']) ? trim($_GET['query']) : '';
?>

<div class="container py-4">
  <h2 class="text-center mb-5">
<!-- Botón para abrir filtros en modo móvil -->
<div class="d-block d-md-none text-center mb-4">
  <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros">
    Filtrar Productos
  </button>
</div>


    <?php
      if (!empty($terminoBusqueda)) {
          echo 'Resultados para: <span style="color: var(--primary-red);">' . htmlspecialchars($terminoBusqueda) . '</span>';
      } else {
          echo 'Resultados de tu Búsqueda';
      }
    ?>
  </h2>

  <div class="row">
    <?php include('filtros-sidebar.php'); ?>

    <div class="col-md-9">
      <?php if (!empty($productosEncontrados)) : ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
          <?php foreach ($productosEncontrados as $producto) : ?>
            <div class="col">
              <div class="card h-100 product-card">
                <img src="<?= $producto['imagen']; ?>" class="card-img-top" alt="<?= $producto['nombre']; ?>">
                <div class="card-body">
                  <h5 class="product-title"><a href="detalles.php?id=<?= $producto['id']; ?>" class="text-decoration-none text-dark"><?= $producto['nombre']; ?></a></h5>
                  <p class="card-text text-center"><?= $producto['descripcion']; ?></p>
                  <p class="product-price"><?= $producto['precio']; ?> MXN</p>
                </div>
                <div class="card-body text-center">
                  <a href="detalles.php?id=<?= $producto['id']; ?>" class="btn btn-vermas">>>>Ver más</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="d-flex flex-column justify-content-center align-items-center text-center w-100" style="min-height: 400px;">
          <img src="assets/img/not-found.png" alt="No encontrado" class="mb-4" style="max-width: 200px;">
          <h2 class="text-danger mb-3">Ups! No hay coincidencias</h2>
          <p class="mb-4">Intenta ajustar tus filtros o realizar otra búsqueda.</p>
          <a href="productos.php" class="btn btn-noresult">Volver a todos los productos</a>
        </div>
      <?php endif; ?>
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