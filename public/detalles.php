<?php
  // Simulación de datos que serían traídos desde la base de datos
  $producto = [
    "nombre" => "Gojo Satoru",
    "descripcion" => "Figura edición limitada con base personalizada. Altura: 25 cm.",
    "precio" => "$6,199.00 MXN",
    "imagenes" => [
      "assets/img/ejemplo1.png",
      "assets/img/ejemplo2.jpg",
      "assets/img/ejemplo3.jpg"
    ],
    "material" => "PVC y ABS",
    "altura" => "25 cm",
    "disponibilidad" => "Disponible",
    "categoria" => "Figuras de colección",
    "estudio" => "Jujutsu Studio",
  ];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Producto</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Barra de navegación del sitio -->
<?php include('navbar.php'); ?>

<div class="container py-4">
  <div class="row">
    <!-- Carrusel de imágenes -->
    <div class="carousel-container col-lg-4 col-md-6 d-flex flex-column align-items-center">
      <div id="carouselProducto" class="carousel slide custom-carousel" data-bs-ride="false">
        <div class="carousel-inner">
          <?php foreach ($producto['imagenes'] as $index => $img): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <img src="<?= $img ?>" class="d-block w-100" alt="Imagen del producto">
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
      <div class="carousel-thumbnails">
        <?php foreach ($producto['imagenes'] as $index => $img): ?>
          <img src="<?= $img ?>" class="thumb-img <?= $index === 0 ? 'active-thumb' : '' ?>" data-bs-target="#carouselProducto" data-bs-slide-to="<?= $index ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Detalles del producto -->
    <div class="col-md-7">
      <h2><?= $producto["nombre"] ?></h2>
      <p class="text-muted"><?= $producto["disponibilidad"] ?></p>
      <h4 class="text-danger"><?= $producto["precio"] ?></h4>
      <p><?= $producto["descripcion"] ?></p>
      <ul>
        <li><strong>Altura:</strong> <?= $producto["altura"] ?></li>
        <li><strong>Material:</strong> <?= $producto["material"] ?></li>
        <li><strong>Categoría:</strong> <?= $producto["categoria"] ?></li>
        <li><strong>Estudio:</strong> <?= $producto["estudio"] ?></li>
      </ul>

      <!-- Descripción extendida -->
      <div class="mt-5">
        <h4>Descripción detallada</h4>
        <p>Esta figura está elaborada con materiales de alta calidad y presenta un acabado excepcional. Perfecta para coleccionistas de anime que desean lo mejor en su vitrina...</p>
      </div>

      <div class="mt-5">
        <h5>¿Te interesa?</h5>
        <p>Ponte en contacto con nosotros y te brindaremos la información que requieras para adquirir el producto :D</p>
      </div>

      <div class="link-terminos-container">
        <a href="terminos.php" class="link-terminos" title="Consulta los Términos y Condiciones antes de Comprar">Consulta nuestros Términos y Condiciones antes de realizar un pedido</a>
      </div>


      <div class="d-flex flex-wrap gap-2">
        <!-- Botón llamada a la acción: Chat por WhatsApp -->
        <a href="https://wa.me/521XXXXXXXXXX" class="btn btn-whatsapp d-flex align-items-center">
          <img src="assets/icons/whatsapp01.png" alt="WhatsApp" class="icon-btn me-2">
          Consultar por WhatsApp
        </a>
        <!-- Botón llamada a la acción: Chat por Facebook/Messenger -->
        <a href="https://m.me/usuario_facebook" class="btn btn-messenger d-flex align-items-center">
          <img src="assets/icons/messenger01.png" alt="Messenger" class="icon-btn me-2">
          Consultar por Messenger
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<?php include('footer.php'); ?>

</body>
</html>
