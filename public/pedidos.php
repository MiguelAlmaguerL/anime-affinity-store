<!-- Página informativa sobre Pedidos Especiales -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedidos Especiales - Anime Affinity Store</title>

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Barra de navegación -->
<?php 
  include('navbar.php');
  require __DIR__ . '/../includes/firebase_fetch.php'; 
?>

<div class="container py-5 text-center">
  <h2 class="pedidos-titulo">¿Buscas una Figura en Especial?</h2>
  <p class="pedidos-texto">
    Si no encuentras el producto que deseas en nuestro catálogo, ¡no te preocupes! También realizamos pedidos especiales para ti.
    Sólo cuéntanos qué figura o coleccionable quieres, y haremos lo posible por conseguirlo.
  </p>

  <h3 class="pasos-titulo">¿Cómo funciona?</h3>
  <ul class="lista-pasos">
    <li><strong>Contacto:</strong> Escríbenos por WhatsApp o Messenger.</li>
    <li><strong>Búsqueda:</strong> Buscamos disponibilidad en distribuidores confiables.</li>
    <li><strong>Confirmación:</strong> Te informamos precio, tiempo de llegada y condiciones.</li>
    <li><strong>Reserva:</strong> En algunos casos se solicitará un anticipo.</li>
  </ul>

      <div class="d-flex flex-wrap justify-content-center gap-3 my-4">
         <!-- Botón WhatsApp --> 
        <a href="https://wa.me/521XXXXXXXXXX" class="btn btn-whatsapp d-flex align-items-center">
          <img src="assets/icons/whatsapp01.png" alt="WhatsApp" class="icon-btn me-2">
          Consultar por WhatsApp
        </a>

         <!-- Botón Messenger --> 
        <a href="https://m.me/usuario_facebook" class="btn btn-messenger d-flex align-items-center">
          <img src="assets/icons/messenger01.png" alt="Messenger" class="icon-btn me-2">
          Consultar por Messenger
        </a>
      </div>

  <p class="pedidos-nota mt-4">
    *Recuerda que los pedidos especiales están sujetos a disponibilidad de nuestros proveedores.
  </p>
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

<!-- Footer del sitio -->
<?php include('footer.php'); ?>

</body>
</html>