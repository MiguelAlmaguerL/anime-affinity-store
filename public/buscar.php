<!-- buscar.php -->
<?php
require __DIR__ . '/../includes/firebase_fetch.php';
// 1. Capturar búsqueda y filtros desde la URL
$terminoBusqueda   = isset($_GET['query']) ? trim($_GET['query']) : '';
$categoriasFiltro  = $_GET['categorias'] ?? [];
$marcasFiltro      = $_GET['marcas'] ?? [];
$seriesFiltro      = $_GET['series'] ?? [];
$escalasFiltro     = $_GET['escalas'] ?? [];
$preciosFiltro     = $_GET['precio'] ?? [];
$ordenFiltro       = $_GET['orden'] ?? '';

// 2. Traer productos con todos los campos necesarios
$productos = obtenerProductosParaBusqueda(250);

// 3. Extraer los IDs de las referencias
foreach ($productos as &$p) {
    $p['categoria_id'] = isset($p['categoria']) ? basename($p['categoria']) : '';
    $p['marca_id']     = isset($p['marca'])     ? basename($p['marca'])     : '';
    $p['serie_id']     = isset($p['serie'])     ? basename($p['serie'])     : '';
    $p['escala_id']    = isset($p['escala'])    ? basename($p['escala'])    : '';
}
unset($p);

// 4. Filtrar por coincidencia de nombre
$productosEncontrados = [];
if (!empty($terminoBusqueda)) {
    foreach ($productos as $producto) {
        if (stripos($producto['nombre'], $terminoBusqueda) !== false) {
            $productosEncontrados[] = $producto;
        }
    }
} else {
    $productosEncontrados = $productos;
}

// 5. Aplicar filtros adicionales sobre los resultados encontrados
$productosEncontrados = array_filter($productosEncontrados, function ($p) use ($categoriasFiltro, $marcasFiltro, $seriesFiltro, $escalasFiltro, $preciosFiltro) {
    $matchCategoria = empty($categoriasFiltro) || in_array($p['categoria_id'], $categoriasFiltro);
    $matchMarca     = empty($marcasFiltro)     || in_array($p['marca_id'], $marcasFiltro);
    $matchSerie     = empty($seriesFiltro)     || in_array($p['serie_id'], $seriesFiltro);
    $matchEscala    = empty($escalasFiltro)    || in_array($p['escala_id'], $escalasFiltro);

    $precio = $p['precio'];
    $matchPrecio =
        empty($preciosFiltro) ||
        (in_array('menos1000', $preciosFiltro) && $precio < 1000) ||
        (in_array('1000a5000', $preciosFiltro) && $precio >= 1000 && $precio <= 5000) ||
        (in_array('mas5000', $preciosFiltro) && $precio > 5000);

    return $matchCategoria && $matchMarca && $matchSerie && $matchEscala && $matchPrecio;
});

// 6. Ordenar resultados
if ($ordenFiltro === 'az') {
    usort($productosEncontrados, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
} elseif ($ordenFiltro === 'za') {
    usort($productosEncontrados, fn($a, $b) => strcmp($b['nombre'], $a['nombre']));
}

// 7. Traer info para filtros
$categorias = obtenerCategorias();
$marcas     = obtenerMarcas();
$series     = obtenerSeries();
$escalas    = obtenerEscalas();
?>

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

<div class="container py-4">
  <h2 class="text-center mb-5">
<!-- Botón para abrir filtros en modo móvil -->
    <?php
      if (!empty($terminoBusqueda)) {
          echo 'Resultados para: <span style="color: var(--primary-red);">' . htmlspecialchars($terminoBusqueda) . '</span>';
      } else {
          echo 'Resultados de tu Búsqueda';
      }
    ?>
  </h2>
  <div class="d-block d-lg-none text-center mb-4">
  <button class="btn btn-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros">
    Filtrar Productos
  </button>
    </div>

  <div class="row">
    <?php include('filtros-sidebar.php'); ?>

    <div class="col-12 col-lg-9">
      <?php if (!empty($productosEncontrados)) : ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 justify-content-center">
          <?php foreach ($productosEncontrados as $producto) : ?>
            <div class="col">
              <div class="card h-100 product-card">
                <img src="<?= htmlspecialchars($producto['imagenes'][0] ?? 'assets/img/default.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>">
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

<!-- Botón flotante para ir al principio -->
<button id="btnIrArriba" class="btn btn-dark rounded-circle" 
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
        title="Ir arriba">
  ↑
</button>

<?php include('footer.php'); ?>

</body>
</html>