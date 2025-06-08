<!-- Página de Términos y Condiciones -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Términos y Condiciones</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Barra de navegación -->
<?php 
  include('navbar.php');
  require __DIR__ . '/../includes/firebase_fetch.php'; 
?>

<div class="container py-5">
  <h2 class="text-center mb-5">Términos y Condiciones</h2>

  <div class="mb-5">
    <h4 class="mb-3">Alcance del Servicio</h4>
    <p>Anime Affinity Store ofrece sus productos únicamente dentro del territorio nacional de México. Los pedidos que se realicen deben ser entregados dentro de este país.</p>
  </div>

  <div class="mb-5">
    <h4 class="mb-3">Formas de Envío y Costos</h4>
    <p>El envío se realiza generalmente a través de <strong>Correos de México</strong>. Sin embargo, el cliente puede solicitar el uso de otro servicio de paquetería con el que tenga mejor o mayor accesibilidad en su localidad. En caso de usar otro servicio de paquetería, los costos y tiempos de entrega podrán variar de acuerdo con las tarifas y disponibilidad de la empresa seleccionada.</p>
    <p>Los tiempos de entrega estimados van de una semana en adelante, dependiendo de la paquetería y la ubicación de entrega.</p>
  </div>

  <div class="mb-5">
    <h4 class="mb-3">Preventas</h4>
    <p>Para los productos en preventa, el cliente puede reservar su pedido abonando un porcentaje del precio total. Este porcentaje puede ir desde el <strong>20%</strong> hasta el <strong>50%</strong> del costo del producto, más los gastos de envío que correspondan. El resto del pago se realizará una vez que el producto esté disponible para envío.</p>
  </div>

  <div class="mb-5">
    <h4 class="mb-3">Política de Devoluciones y Cambios</h4>
    <p>Las devoluciones, cambios o reembolsos están sujetos a las siguientes condiciones:</p>
    <ul>
      <li>El cliente debe notificar cualquier incidencia dentro de un plazo de <strong>[10 días]</strong> a partir de la recepción del producto.</li>
      <li>El producto debe encontrarse en su empaque original y en las mismas condiciones en que se recibió.</li>
      <li>No se aceptan devoluciones de productos que presenten daños por mal uso o desgaste normal.</li>
      <li>Los costos de envío por devolución corren a cargo del cliente, salvo en casos de error o defecto por parte de Anime Affinity Store.</li>
    </ul>
  </div>

  <div class="mb-5">
    <h4 class="mb-3">Garantía de los Productos</h4>
    <p>La garantía cubre defectos de fabricación, pero no daños ocasionados por el uso indebido o desgaste natural. La duración de la garantía depende de cada producto, por lo que el cliente debe consultar la información específica del artículo.</p>
  </div>

  <div class="mb-5">
    <h4 class="mb-3">Responsabilidad del Cliente</h4>
    <p>Es responsabilidad del cliente:</p>
    <ul>
      <li>Proporcionar datos de contacto y de entrega correctos.</li>
      <li>Revisar la descripción y las especificaciones del producto antes de realizar la compra.</li>
      <li>Dar seguimiento a su pedido y notificar cualquier problema o inquietud de manera oportuna.</li>
    </ul>
  </div>

  <div>
    <h4 class="mb-3">Contacto</h4>
    <p>Para cualquier duda, aclaración o solicitud relacionada con estos Términos y Condiciones, puedes ponerte en contacto con nosotros a través de nuestros medios oficiales de comunicación.</p>
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

<!-- Footer -->
<?php include('footer.php'); ?>

</body>
</html>