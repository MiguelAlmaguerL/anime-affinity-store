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

<?php
require __DIR__ . '/../includes/firebase_fetch.php';

//Capturar filtros desde URL
$categoriasFiltro = $_GET['categorias'] ?? [];
$marcasFiltro     = $_GET['marcas'] ?? [];
$seriesFiltro     = $_GET['series'] ?? [];
$escalasFiltro    = $_GET['escalas'] ?? [];
$preciosFiltro    = $_GET['precio'] ?? [];
$ordenFiltro      = $_GET['orden'] ?? '';

//Detectar si hay filtros activos
$hayFiltros = !empty($categoriasFiltro) || !empty($marcasFiltro) || !empty($seriesFiltro) || !empty($escalasFiltro) || !empty($preciosFiltro);

// Número de productos por página
$limite = 21;
// Obtener el parámetro 'after' desde la URL si existe
$startAfter = $_GET['after'] ?? null;

// Si hay filtros, traemos un lote grande. Si no, usamos paginación.
if ($hayFiltros) {
    $productos = obtenerProductosParaBusqueda(150);
} else {
    $productos = obtenerProductosPaginados($limite, $startAfter);
}

//var_dump($productos[0]);

//Extraer IDs de las referencias
foreach ($productos as &$p) {
    $p['categoria_id'] = $p['categoria'] ? basename($p['categoria']) : '';
    $p['marca_id']     = $p['marca']     ? basename($p['marca'])     : '';
    $p['serie_id']     = $p['serie']     ? basename($p['serie'])     : '';
    $p['escala_id']    = $p['escala']    ? basename($p['escala'])    : '';
}
unset($p); // buena práctica

// Aplicar filtros en PHP
$productos = array_filter($productos, function ($p) use ($categoriasFiltro, $marcasFiltro, $seriesFiltro, $escalasFiltro, $preciosFiltro) {
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

// Ordenar si es necesario
if ($ordenFiltro === 'az') {
    usort($productos, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
} elseif ($ordenFiltro === 'za') {
    usort($productos, fn($a, $b) => strcmp($b['nombre'], $a['nombre']));
}

// Reindexar
$productos = array_values($productos);

// Paginación solo si no hay filtros
$nextStart = !$hayFiltros ? ($productos[$limite - 1]['fecha_subida'] ?? null) : null;
$mostrarBotonVerMas = !$hayFiltros && count($productos) >= $limite;

// Obtener filtros para la vista
$categorias = obtenerCategorias();
$marcas     = obtenerMarcas();
$series     = obtenerSeries();
$escalas    = obtenerEscalas();
?>

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
      <?php if (!empty($productos)) : ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
          <!-- Más productos... -->
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
      <?php else : ?>
          <div class="d-flex flex-column justify-content-center align-items-center text-center w-100" style="min-height: 400px;">
            <img src="assets/img/not-found.png" alt="No encontrado" class="mb-4" style="max-width: 200px;">
            <h2 class="text-danger mb-3">Ups! No hay coincidencias</h2>
            <p class="mb-4">Intenta ajustar tus filtros o realizar otra búsqueda.</p>
            <a href="productos.php" class="btn btn-noresult">Volver a todos los productos</a>
          </div>
      <?php endif; ?>
    </div>

      <div class="text-center my-5 d-flex justify-content-center gap-3 flex-wrap">
        <!-- Botón para volver al principio (recarga limpia) -->
        <a href="productos.php" class="btn btn-outline-secondary">
          ⟨ Volver al Principio
        </a>

        <!-- Botón para cargar el siguiente grupo de productos -->
        <?php if ($mostrarBotonVerMas && $nextStart): ?>
          <a href="productos.php?after=<?= urlencode($nextStart) ?>" class="btn btn-primary">
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