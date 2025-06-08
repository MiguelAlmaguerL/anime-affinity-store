<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anime Affinity Store</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .carousel-item img {
      width: 100%;
      height: 500px;
      object-fit: cover; /* Se recomienda usar imagenes de 1600x500 px o algo proporcional, como 1920x600, 1200x400, etc. */
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      transition: transform 0.5s ease;
    }

    .carousel-item:hover img {
      transform: scale(1.02);
    }

    .carousel-caption {
      background: rgba(0, 0, 0, 0.6);
      border-radius: 10px;
      padding: 15px;
      animation: fadeInUp 0.7s ease-in-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .carousel-indicators [data-bs-target] {
      width: 30px;
      height: 5px;
      border-radius: 10px;
      background-color: #ccc;
      margin: 0 4px;
      transition: all 0.3s;
    }

    .carousel-indicators .active {
      background-color: #ff4081;
      width: 40px;
    }

    @media (max-width: 576px) {
      .carousel-caption {
        font-size: 0.9rem;
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        padding: 0.5rem;
      }

      .carousel-caption h5 {
        font-size: 1rem;
      }
    }

    #toggleCarousel {
      z-index: 10;
    }

    .icono-ajustado {
      width: 80px; /* Ajusta el tamaño que prefieras */
      height: 80px; /* Igual para todos */
      object-fit: contain; /* Mantiene proporción sin recortar */
      
      @media (max-width: 767px) {
        .bloques-beneficio {
        flex-direction: column !important; /* Se ponen en vertical */
        align-items: center !important; /* Centrar horizontalmente */
        gap: 6rem; /* Espacio vertical entre los bloques */

  }
}
}
  </style>
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
?>

<!-- Carrusel de imágenes -->
<?php if (!empty($carrusel)): ?>
  <div id="carouselExampleIndicators" class="carousel slide carousel-fade mb-5 position-relative" data-bs-ride="carousel">
    
    <!-- Botón de pausa/play con íconos -->
    <button id="toggleCarousel" class="btn btn-sm btn-light position-absolute top-0 end-0 m-3">
      <i id="toggleIcon" class="bi bi-pause-fill"></i>
    </button>

    <!-- Indicadores -->
    <div class="carousel-indicators">
      <?php foreach ($carrusel as $index => $img): ?>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
      <?php endforeach; ?>
    </div>

    <!-- Imágenes -->
    <div class="carousel-inner">
      <?php foreach ($carrusel as $index => $img): ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
          <img src="<?= htmlspecialchars($img['url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($img['titulo'] ?? 'Imagen del carrusel') ?>">
          <div class="carousel-caption d-block">
            <h5><?= htmlspecialchars($img['titulo']) ?></h5>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Controles prev/next -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon d-none"></span>
      <i class="bi bi-chevron-left fs-1 text-light"></i>
      <span class="visually-hidden">Anterior</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon d-none"></span>
      <i class="bi bi-chevron-right fs-1 text-light"></i>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
<?php else: ?>
  ⚠️ No se cargó el carrusel
<?php endif; ?>

<!-- Sección de envío nacional-->
<section class="container my-5">
  <div class="d-flex justify-content-center align-items-center flex-column flex-md-row bloques-beneficio">
    
    <!-- Bloque de Envíos -->
    <div class="d-flex flex-column align-items-center text-center mb-5 mb-md-0">
      <img src="assets/icons/envio.png" alt="Envíos a todo México" class="mb-2 icono-ajustado">
      <h4 class="m-0">¡Envíos a todo México!</h4>
    </div>
    
    <!-- Bloque de Pagos Seguros -->
    <div class="d-flex flex-column align-items-center text-center mx-4">
      <img src="assets/icons/pago.png" alt="Pagos Seguros" class="mb-2 icono-ajustado">
      <h4 class="m-0">¡Pagos Seguros UuU!</h4>
    </div>

  </div>
</section>

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

<!-- Botón flotante para ir al principio -->
<button id="btnIrArriba" class="btn btn-dark rounded-circle" 
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
        title="Ir arriba">
  ↑
</button>

<!-- Footer del sitio -->
<?php include('footer.php'); ?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script de pausa/play -->
<script>
  const carousel = document.querySelector('#carouselExampleIndicators');
  const toggleBtn = document.querySelector('#toggleCarousel');
  const toggleIcon = document.querySelector('#toggleIcon');
  let isPaused = false;
  const bsCarousel = new bootstrap.Carousel(carousel);

  toggleBtn.addEventListener('click', () => {
    if (isPaused) {
      bsCarousel.cycle();
      toggleIcon.className = 'bi bi-pause-fill';
    } else {
      bsCarousel.pause();
      toggleIcon.className = 'bi bi-play-fill';
    }
    isPaused = !isPaused;
  });
</script>

</body>
</html>
