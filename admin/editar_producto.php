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

// Obtener las colecciones relacionadas para los select
$categorias = obtenerCategorias();
$marcas = obtenerMarcas();
$series = obtenerSeries();
$escalas = obtenerEscalas();
$estados = [
    ['id' => 'inventario', 'nombre' => 'En existencia'],
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    .img-wrapper {
      width: 150px;
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

    .remove-img-btn {
      position: absolute;
      top: 6px;
      right: 6px;
      background-color: rgba(220, 53, 69, 1);
      border: none;
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: #fff;
      border-radius: 50%;
      cursor: pointer;
    }
  </style>
</head>

<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Editar Producto</h2>

  <form id="formProducto" class="needs-validation" novalidate action="procesar_editar_producto.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($producto['id']) ?>">

    <div class="row">
      <!-- Nombre -->
      <div class="col-md-6 mb-3">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
        <div class="invalid-feedback">El nombre es obligatorio.</div>
      </div>

      <!-- Precio -->
      <div class="col-md-6 mb-3">
        <label for="precio">Precio (MXN):</label>
        <input type="number" step="0.01" name="precio" class="form-control" value="<?= htmlspecialchars($producto['precio']) ?>" required min="0">
        <div class="invalid-feedback">Por favor, ingresa un precio válido (número mayor o igual a cero).</div>
      </div>

      <!-- Descripción -->
      <div class="col-12 mb-3">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        <div class="invalid-feedback">La descripción es obligatoria.</div>
      </div>

      <!-- Categoría -->
      <div class="col-md-4 mb-3">
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" class="form-control" required>
          <option disabled selected value="">Seleccione una categoría</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($producto['categoria'] === $c['nombre']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona una categoría.</div>
      </div>

      <!-- Marca -->
      <div class="col-md-4 mb-3">
        <label for="marca">Marca:</label>
        <select id="marca" name="marca" class="form-control" required>
          <option disabled selected value="">Seleccione una marca</option>
          <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['id'] ?>" <?= ($producto['marca'] === $m['nombre']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($m['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona una marca.</div>
      </div>

      <!-- Serie -->
      <div class="col-md-4 mb-3">
        <label for="serie">Serie:</label>
        <select id="serie" name="serie" class="form-control" required>
          <option disabled selected value="">Seleccione una serie</option>
          <?php foreach ($series as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($producto['serie'] === $s['nombre']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona una serie.</div>
      </div>

      <!-- Escala -->
      <div class="col-md-6 mb-3">
        <label for="escala">Escala:</label>
        <select id="escala" name="escala" class="form-control" required>
          <option disabled selected value="">Seleccione una escala</option>
          <?php foreach ($escalas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($producto['escala'] === $e['nombre']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona una escala.</div>
      </div>

      <!-- Estado -->
      <div class="col-md-6 mb-3">
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" class="form-control" required>
          <option disabled selected value="">Seleccione un estado</option>
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($producto['estado'] === $e['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un estado.</div>
      </div>

      <!-- Fecha de lanzamiento -->
      <div class="col-md-6 mb-3">
        <label for="fecha_lanzamiento">Fecha de lanzamiento:</label>
        <input type="date" id="fecha_lanzamiento" name="fecha_lanzamiento" class="form-control"
               value="<?= $producto['fecha_lanzamiento'] ? date('Y-m-d', strtotime($producto['fecha_lanzamiento'])) : '' ?>">
      </div>

      <!-- Imágenes actuales -->
      <div class="col-12 mb-3">
        <div id="imagenes-actuales" class="d-flex flex-wrap gap-3">
          <?php foreach ($producto['imagenes'] as $url): ?>
            <div class="img-wrapper imagen-previa" data-url="<?= $url ?>">
              <img src="<?= $url ?>" class="preview-img">
              <button type="button" class="remove-img-btn eliminar-imagen" title="Eliminar imagen">
                <i class="bi bi-x-lg"></i>
              </button>
              <input type="hidden" name="imagenes_anteriores[]" value="<?= $url ?>">
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Nuevas imágenes -->
      <div class="col-12 mb-3">
        <label for="imagenes">Nuevas imágenes (opcional):</label>
        <input type="file" id="imagenes" name="imagenes[]" class="form-control" accept="image/*" multiple>
        <div id="preview-nuevas-imagenes" class="d-flex flex-wrap gap-3 mt-3"></div>
      </div>
    </div>

    <!-- Botones -->
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">Guardar cambios</button>
      <a href="productos.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación de formulario con Bootstrap -->	
<script>
  (() => {
    'use strict';
    const form = document.getElementById('formProducto');
    form.addEventListener('submit', event => {
      const precio = document.querySelector('input[name="precio"]');
      const precioValor = parseFloat(precio.value);

      // Reinicia el estado de error anterior (si lo hubiera)
      precio.classList.remove('is-invalid');

      let valido = true;

      if (isNaN(precioValor) || precioValor < 0) {
        precio.classList.add('is-invalid');
        valido = false;
      }

      if (!form.checkValidity() || !valido) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add('was-validated');
    });
  })();

  // Eliminar imagen existente (anteriores)
  document.querySelectorAll('.eliminar-imagen').forEach(boton => {
    boton.addEventListener('click', function () {
      const imagen = this.closest('.imagen-previa');
      imagen.remove();
    });
  });
</script>

<!-- Previsualización de nuevas imágenes -->
<script>
    const inputImagenes = document.getElementById('imagenes');
  const preview = document.getElementById('preview-nuevas-imagenes');

  inputImagenes.addEventListener('change', function () {
    preview.innerHTML = '';
    const dt = new DataTransfer();

    Array.from(inputImagenes.files).forEach((file, index) => {
      const reader = new FileReader();

      reader.onload = function (e) {
        const wrapper = document.createElement('div');
        wrapper.classList.add('img-wrapper');

        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'preview-img';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-img-btn';
        btn.innerHTML = '<i class="bi bi-x-lg"></i>';
        btn.title = 'Eliminar imagen';

        btn.addEventListener('click', () => {
          wrapper.remove();

          // Quitar del DataTransfer
          const nuevaLista = new DataTransfer();
          Array.from(dt.files).forEach((f, i) => {
            if (i !== index) nuevaLista.items.add(f);
          });

          inputImagenes.files = nuevaLista.files;
        });

        wrapper.appendChild(img);
        wrapper.appendChild(btn);
        preview.appendChild(wrapper);
      };

      reader.readAsDataURL(file);
      dt.items.add(file);
    });

    inputImagenes.files = dt.files;
  });
</script>

</body>
</html>
