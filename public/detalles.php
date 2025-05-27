<?php
require __DIR__ . '/../includes/firebase_fetch.php';

// Verifica si se proporcionó un ID por la URL
$idProducto = $_GET['id'] ?? null;
if (!$idProducto) {
  echo "Producto no especificado.";
  exit;
}

// Obtener el producto por ID
$producto = obtenerProductoPorID($idProducto);
if (!$producto) {
  echo "Producto no encontrado.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalles del Producto</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container py-4">
  <div class="row">
    <!-- Carrusel de imágenes -->
    <div class="carousel-container col-lg-4 col-md-6 d-flex flex-column align-items-center">
      <div id="carouselProducto" class="carousel slide custom-carousel" data-bs-ride="false">
        <div class="carousel-inner">
          <?php foreach ($producto['imagenes'] as $index => $img): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Imagen del producto">
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselProducto" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselProducto" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>

      <!-- Miniaturas -->
      <div class="carousel-thumbnails" style="display: flex; overflow-x: auto; gap: 8px; padding: 8px 0;">
        <?php foreach ($producto['imagenes'] as $index => $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" class="thumb-img <?= $index === 0 ? 'active-thumb' : '' ?>" data-bs-target="#carouselProducto" data-bs-slide-to="<?= $index ?>" style="flex: 0 0 auto; width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Detalles del producto -->
    <div class="col-md-7">
      <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
      <p class="text-muted"><?= htmlspecialchars($producto['estado'] ?? 'Sin estado') ?></p>
      <h4 class="text-danger">$<?= number_format($producto['precio'], 2) ?> MXN</h4>
      <p><?= htmlspecialchars($producto['descripcion']) ?></p>
      <ul>
        <li><strong>Categoría:</strong> <?= htmlspecialchars($producto['categoria'] ?? 'Sin categoría') ?></li>
        <li><strong>Marca:</strong> <?= htmlspecialchars($producto['marca'] ?? 'Sin marca') ?></li>
        <li><strong>Escala:</strong> <?= htmlspecialchars($producto['escala'] ?? 'No especificada') ?></li>
        <li><strong>Serie:</strong> <?= htmlspecialchars($producto['serie'] ?? 'No especificada') ?></li>
        <li><strong>Fecha de lanzamiento:</strong> <?= date('d-m-Y', strtotime($producto['fecha_lanzamiento'])) ?></li>
      </ul>

      <div class="mt-5">
        <h5>¿Te interesa?</h5>
        <p>Ponte en contacto con nosotros y te brindaremos la información que requieras para adquirir el producto :D</p>
      </div>

      <div class="link-terminos-container">
        <a href="terminos.php" class="link-terminos" title="Consulta los Términos y Condiciones antes de Comprar">Consulta nuestros Términos y Condiciones antes de realizar un pedido</a>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <a href="https://wa.me/521XXXXXXXXXX" class="btn btn-whatsapp d-flex align-items-center">
          <img src="assets/icons/whatsapp01.png" alt="WhatsApp" class="icon-btn me-2">
          Consultar por WhatsApp
        </a>
        <a href="https://m.me/usuario_facebook" class="btn btn-messenger d-flex align-items-center">
          <img src="assets/icons/messenger01.png" alt="Messenger" class="icon-btn me-2">
          Consultar por Messenger
        </a>
      </div>
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
<?php include('footer.php'); ?>

<!-- Lightbox para mostrar imagen en grande -->
<div id="lightbox-overlay" class="lightbox-overlay">
  <span id="lightbox-close" class="lightbox-close">&times;</span>
  <img id="lightbox-img" class="lightbox-img" src="" alt="Imagen ampliada">
</div>

</body>
</html>
