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
        <label for="selectCategoria" class="form-label">Categoría:</label>
        <div class="input-group">
          <select name="categoria" id="selectCategoria" class="form-control" required>
            <?php foreach ($categorias as $c): ?>
              <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarCategoria" title="Agregar categoría">+</button>
        </div>
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

  <!-- Modal Agregar Categoría -->
  <div class="modal fade" id="modalAgregarCategoria" tabindex="-1" aria-labelledby="modalAgregarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="formAgregarCategoria" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarCategoriaLabel">Agregar nueva categoría</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" placeholder="Nombre de la nueva categoría" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

<!-- VALIDACIÓN de formulario -->
<script>
  document.querySelector("form").addEventListener("submit", function(e) {
    const form = e.target;
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

    if (!confirm("¿Estás seguro de que deseas subir este producto?")) {
      e.preventDefault();
    }
  });
</script>

<!-- PREVISUALIZACIÓN de imágenes -->
<script>
  const inputImagenes = document.getElementById("input-imagenes");
  const previewContainer = document.getElementById("preview-imagenes");

  inputImagenes.addEventListener("change", function () {
    previewContainer.innerHTML = "";
    const files = inputImagenes.files;
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

<!-- AGREGAR CATEGORÍA -->
<script>
  document.getElementById('formAgregarCategoria').addEventListener('submit', function (e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre_categoria').value.trim();
    if (nombre === '') {
      alert("Por favor, escribe un nombre.");
      return;
    }

    console.log("ENVIANDO:", JSON.stringify({ nombre }));

    fetch('ajax/agregar_categoria.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const select = document.getElementById('selectCategoria');
        const option = new Option(data.nombre, data.id, true, true);
        select.add(option);
        document.getElementById('nombre_categoria').value = '';
        bootstrap.Modal.getInstance(document.getElementById('modalAgregarCategoria')).hide();
      } else {
        console.error("Respuesta completa del servidor:", data);
        alert("❌ Error: " + (data.error || "No se pudo guardar la categoría."));

      }
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("🔥 Error inesperado al conectar con el servidor. Revisa la consola.");
    });
  });
</script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>