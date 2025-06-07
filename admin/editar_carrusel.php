<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$idCarrusel = $_GET['id'] ?? null;
if (!$idCarrusel) {
    echo "ID de imagen del carrusel no proporcionado.";
    exit;
}

$carrusel = obtenerCarruselPorId($idCarrusel);
if (!$carrusel) {
    echo "Imagen del carrusel no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Imagen del Carrusel</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    .img-wrapper {
    max-width: 250px;
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  #preview-container {
    display: none;
  }

  #remove-img-btn {
    background-color: rgba(220, 53, 69, 1);
    border: none;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #fff;
  }
  </style>
</head>

<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Editar Imagen del Carrusel</h2>

  <?php if (isset($_SESSION['error_carrusel'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error_carrusel']; unset($_SESSION['error_carrusel']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>

  <form id="formCarrusel" class="needs-validation" novalidate action="procesar_editar_carrusel.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($carrusel['id']) ?>">

    <div class="row">
      <!-- Título -->
      <div class="col-md-6 mb-3">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" class="form-control" value="<?= htmlspecialchars($carrusel['titulo']) ?>" required>
        <div class="invalid-feedback">El título es obligatorio.</div>
      </div>

      <!-- Orden -->	
      <div class="col-md-6 mb-3">
        <label for="orden">Orden:</label>
        <input type="number" id="orden" name="orden" class="form-control" value="<?= htmlspecialchars($carrusel['orden']) ?>" required min="1" step="1">
        <div class="invalid-feedback">Por favor, ingrese un orden válido (mayor a cero y sin decimales).</div>
      </div>

      <!-- Activo -->
      <div class="col-md-6 mb-3">
        <label for="activo">¿Activo en el carrusel?:</label>
        <select id="activo" name="activo" class="form-control" required>
          <option disabled selected value="">Seleccione una opción</option>
          <option value="true" <?= ($carrusel['activo'] === true) ? 'selected' : '' ?>>Sí</option>
          <option value="false" <?= ($carrusel['activo'] === false) ? 'selected' : '' ?>>No</option>
        </select>
        <div class="invalid-feedback">Seleccione si la imagen estará activa.</div>
      </div>

      <!-- Imagen actual -->
      <div class="col-12 mb-3">
        <label>Imagen actual:</label>
        <div class="position-relative mb-2" style="max-width: 300px;">
          <img src="<?= htmlspecialchars($carrusel['imagen']) ?>" class="img-thumbnail w-100" style="max-height: 250px; object-fit: contain;">
          <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($carrusel['imagen']) ?>">
        </div>
        <p>Si selecciona una nueva imagen, se reemplazará la actual.</p>
      </div>

      <!-- Nueva imagen -->
      <div class="col-12 mb-3">
        <label for="imagen">Nueva imagen (opcional):</label>
        <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*">
        <div class="invalid-feedback">Es obligatorio subir una imagen válida.</div>
      </div>

      <!-- Previsualización nueva imagen -->
      <div class="mb-3" id="preview-container" style="display: none;">
          <div class="img-wrapper">
              <img id="preview-img" src="#" alt="Previsualización" class="preview-img">
              <button type="button" id="remove-img-btn" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1" title="Eliminar imagen">
                  <i class="bi bi-x-lg"></i>
              </button>
          </div>
      </div>
    </div>

    <!-- Botones -->
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">Guardar cambios</button>
      <a href="carrusel.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación Bootstrap -->
<script>
  (() => {
    'use strict';
    const form = document.getElementById('formCarrusel');
    form.addEventListener('submit', event => {
      const orden = document.getElementById('orden');
      const ordenValor = parseInt(orden.value);

      orden.classList.remove('is-invalid');

      let valido = true;

      if (isNaN(ordenValor) || ordenValor < 1) {
        orden.classList.add('is-invalid');
        valido = false;
      }

      if (!form.checkValidity() || !valido) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add('was-validated');
    });
  })();
</script>

<!-- Previsualización de imagen -->
<script>
  function clearFileInput() {
    const fileInput = document.getElementById('imagen');
    fileInput.value = '';
    document.getElementById('preview-img').src = '#';
    document.getElementById('preview-container').style.display = 'none';
}

function handleImageChange(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        clearFileInput();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('imagen').addEventListener('change', handleImageChange);
    document.getElementById('remove-img-btn').addEventListener('click', clearFileInput);
});
</script>

</body>
</html>