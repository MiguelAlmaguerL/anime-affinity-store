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
    ['id' => 'en-camino', 'nombre' => 'En camino'],
    ['id'=> 'sin-exitencia', 'nombre'=> 'Sin existencia']
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
        <input type="file" name="imagenes[]" id="input-imagenes" class="form-control" accept="image/*" multiple required>
        <div id="preview-imagenes" class="mt-3 d-flex flex-wrap gap-2"></div>
      </div>
    </div>

    <button type="submit" class="btn btn-success">Guardar producto</button>
    <a href="productos.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>

<script>
  // Verifica que los campos obligatorios estén llenos y muestra un cuadro de confirmación antes de enviar el formulario
  document.querySelector("form").addEventListener("submit", function(e) {
    const form = e.target;

    // Verificar que todos los campos obligatorios estén llenos manualmente
    const nombre = form.nombre.value.trim();
    const precio = form.precio.value.trim();
    const descripcion = form.descripcion.value.trim();
    const categoria = form.categoria.value;
    const marca = form.marca.value;
    const estado = form.estado.value;
    const imagenes = form['imagenes[]'].files;

    if (!nombre || !precio || !descripcion || !categoria || !marca || !estado || imagenes.length === 0) {
      alert("Por favor, llena todos los campos obligatorios e incluye al menos una imagen.");
      e.preventDefault();
      return;
    }

    // Mostrar cuadro de confirmación
    const confirmar = confirm("¿Estás seguro de que deseas subir este producto?");
    if (!confirmar) {
      e.preventDefault(); // Detener envío
    }
  });
</script>

<script>
  // Muestra una vista previa de las imágenes seleccionadas antes de enviarlas
  const inputImagenes = document.getElementById("input-imagenes");
  const previewContainer = document.getElementById("preview-imagenes");

  inputImagenes.addEventListener("change", function () {
    // Limpiar vista previa anterior
    previewContainer.innerHTML = "";

    const files = inputImagenes.files;

    if (files.length === 0) return;

    for (const file of files) {
      if (!file.type.startsWith("image/")) continue;

      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.alt = "Vista previa";
        img.style.width = "100px";
        img.style.height = "100px";
        img.style.objectFit = "cover";
        img.classList.add("rounded", "shadow");

        previewContainer.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
</script>
