<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$idProducto = $_GET['id'] ?? null;
if (!$idProducto) {
    echo "ID de producto no proporcionado.";
    exit;
}

$producto = obtenerProductoPorId($idProducto);
if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

$categorias = obtenerCategorias();
$marcas = obtenerMarcas();
$series = obtenerSeries();
$escalas = obtenerEscalas();
$estados = [
    ['id' => 'inventario', 'nombre' => 'Disponible'],
    ['id' => 'preventa', 'nombre' => 'Preventa'],
    ['id' => 'en-camino', 'nombre' => 'En camino'],
    ['id'=> 'sin-existencia', 'nombre'=> 'Sin existencia']
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="mb-4">Editar Producto</h2>
  <form action="procesar_editar_producto.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id']) ?>">

    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label>Precio (MXN):</label>
        <input type="number" step="0.01" name="precio" class="form-control" value="<?= htmlspecialchars($producto['precio']) ?>" required>
      </div>

      <div class="col-md-12 mb-3">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
      </div>

      <div class="col-md-4 mb-3">
        <label>Categoría:</label>
        <select name="categoria" class="form-control" required>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($producto['categoria'] === $c['nombre']) ? 'selected' : '' ?>>
              <?= $c['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label>Marca:</label>
        <select name="marca" class="form-control" required>
          <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['id'] ?>" <?= ($producto['marca'] === $m['nombre']) ? 'selected' : '' ?>>
              <?= $m['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label>Serie:</label>
        <select name="serie" class="form-control">
          <?php foreach ($series as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($producto['serie'] === $s['nombre']) ? 'selected' : '' ?>>
              <?= $s['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Escala:</label>
        <select name="escala" class="form-control">
          <?php foreach ($escalas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($producto['escala'] === $e['nombre']) ? 'selected' : '' ?>>
              <?= $e['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control" required>
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($producto['estado'] === $e['nombre']) ? 'selected' : '' ?>>
              <?= $e['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Fecha de lanzamiento:</label>
        <input type="date" name="fecha_lanzamiento" class="form-control"
          value="<?= $producto['fecha_lanzamiento'] ? date('Y-m-d', strtotime($producto['fecha_lanzamiento'])) : '' ?>">
      </div>
      
      <div class="mb-3">
        <label>Imágenes actuales:</label>
        <div id="imagenes-actuales" class="d-flex flex-wrap gap-2">
            <?php foreach ($producto['imagenes'] as $index => $url): ?>
            <div class="imagen-previa position-relative" data-url="<?= $url ?>">
                <img src="<?= $url ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 eliminar-imagen" title="Eliminar imagen">&times;</button>
                <input type="hidden" name="imagenes_anteriores[]" value="<?= $url ?>">
            </div>
            <?php endforeach; ?>
        </div>
        </div>

      <div class="col-md-12 mb-3">
        <label>Nuevas imágenes (opcional):</label>
        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="productos.php" class="btn btn-secondary">Cancelar</a>

  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Elimina la imagen visualmente y su input hidden al hacer clic en la "X"
  document.querySelectorAll('.eliminar-imagen').forEach(boton => {
    boton.addEventListener('click', function () {
      const imagen = this.closest('.imagen-previa');
      imagen.remove(); // Elimina el div completo con la imagen y el input
    });
  });
</script>

</body>
</html>
