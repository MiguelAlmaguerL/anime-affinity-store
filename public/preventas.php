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
<?php
require __DIR__ . '/../includes/firebase_fetch.php';

// Número de productos por página
$limite = 21;

// Obtener el parámetro 'after' desde la URL si existe
$startAfter = $_GET['after'] ?? null;

// Obtener productos con paginación
$productos = obtenerProductosPreventaPaginados($limite, $startAfter);

// Obtener el valor de fecha_subida del último producto para la próxima página
$nextStart = end($productos)['fecha_subida'] ?? null;

$mostrarBotonVerMas = count($productos) >= $limite;
?>

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
        <?php foreach ($productos as $producto): ?>
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

        <!-- Si no hay preventas -->
        <!-- <div class="col-12 text-center my-5 ps-4">
          <img src="assets/img/not-found.png" alt="No encontrado">
          <h3 class="mt-4">Ups! No hay coincidencias</h3>
          <p>Intenta nuevamente :D</p>
          <a href="productos.php" class="btn btn-noresult mt-3">Volver a todos los productos</a>
        </div> -->
      </div>
      <div class="text-center my-5 d-flex justify-content-center gap-3 flex-wrap">
        <!-- Botón para volver al principio (recarga limpia) -->
        <a href="preventas.php" class="btn btn-outline-secondary">
          ⟨ Volver al Principio
        </a>

        <!-- Botón para cargar el siguiente grupo de productos -->
        <?php if ($mostrarBotonVerMas && $nextStart): ?>
          <a href="preventas.php?after=<?= urlencode($nextStart) ?>" class="btn btn-primary">
            Ver más ⟩
          </a>
        <?php endif; ?>
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
<?php
$productosBusqueda = obtenerProductosParaBusqueda();
$datosParaJS = array_map(function($p) {
  return [
    'id' => $p['id'],
    'nombre' => $p['nombre']
  ];
}, $productosBusqueda);
?>
<script>
  const productoss = <?= json_encode($datosParaJS, JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- Botón flotante para ir al principio -->
<button id="btnIrArriba" class="btn btn-dark rounded-circle" 
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
        title="Ir arriba">
  ↑
</button>
<?php include('footer.php'); ?>

</body>
</html>