<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

// Obtener las colecciones relacionadas para los select
$categorias = obtenerCategorias();
$marcas = obtenerMarcas();
$series = obtenerSeries();
$escalas = obtenerEscalas();
$estados = [
    ['id' => 'inventario', 'nombre' => 'Disponible'],
    ['id' => 'preventa', 'nombre' => 'Preventa'],
    ['id' => 'camino', 'nombre' => 'En camino']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Agregar Nuevo Producto</h2>
  <form action="procesar_agregar_producto.php" method="POST" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label>Precio (MXN):</label>
        <input type="number" step="0.01" name="precio" class="form-control" required>
      </div>

      <div class="col-md-12 mb-3">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
      </div>

      <div class="col-md-4 mb-3">
        <label>Categoría:</label>
        <select name="categoria" class="form-control" required>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label>Marca:</label>
        <select name="marca" class="form-control" required>
          <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['id'] ?>"><?= $m['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label>Serie:</label>
        <select name="serie" class="form-control">
          <?php foreach ($series as $s): ?>
            <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Escala:</label>
        <select name="escala" class="form-control">
          <?php foreach ($escalas as $e): ?>
            <option value="<?= $e['id'] ?>"><?= $e['nombre'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control" required>
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id'] ?>"> <?= $e['nombre'] ?> </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label>Fecha de lanzamiento:</label>
        <input type="date" name="fecha_lanzamiento" class="form-control">
      </div>

      <div class="col-md-6 mb-3">
        <label>Imágenes del producto:</label>
        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple required>
      </div>
    </div>

    <button type="submit" class="btn btn-success">Guardar producto</button>
    <a href="productos.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
