<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

$errorMensaje = $_SESSION['error_agregar_producto'] ?? null;
unset($_SESSION['error_agregar_producto']);

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
    ['id'=> 'sin-existencia', 'nombre'=> 'Sin existencia']
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
  <?php if ($errorMensaje): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($errorMensaje) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  <?php endif; ?>
  <h2 class="mb-4">Agregar Nuevo Producto</h2>
  <form action="procesar_agregar_producto.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    
  <div class="row">
      <!-- Nombre -->
      <div class="col-md-6 mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" required>
        <div class="invalid-feedback">Este campo es obligatorio.</div>
      </div>

      <!-- Precio -->
      <div class="col-md-6 mb-3">
        <label>Precio (MXN):</label>
        <input type="number" step="0.01" name="precio" class="form-control" min="0" required>
        <div class="invalid-feedback">Por favor, ingresa un precio válido (número mayor o igual a cero).</div>
      </div>

      <!-- Descripción -->
      <div class="col-md-12 mb-3">
        <label>Descripción:</label>
        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
        <div class="invalid-feedback">La descripción es obligatoria.</div>
      </div>

      <!-- Categoría -->
      <div class="col-md-4 mb-3">
        <label for="selectCategoria" class="form-label">Categoría:</label>
        <div class="input-group has-validation">
          <select name="categoria" id="selectCategoria" class="form-control" required>
            <option disabled selected value="">Seleccione una categoría</option>
            <?php foreach ($categorias as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarCategoria" title="Agregar categoría">+</button>
          <div class="invalid-feedback">Selecciona una categoría.</div>
        </div>
      </div>

      <!-- Marca -->
      <div class="col-md-4 mb-3">
        <label for="selectMarca" class="form-label">Marca:</label>
        <div class="input-group">
          <select name="marca" id="selectMarca" class="form-control" required>
            <option disabled selected value="">Seleccione una marca</option>
            <?php foreach ($marcas as $m): ?>
              <option value="<?= $m['id'] ?>"><?= $m['nombre'] ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarMarca" title="Agregar marca">+</button>
          <div class="invalid-feedback">Selecciona una marca.</div>
        </div>
      </div>

      <!-- Serie -->
      <div class="col-md-4 mb-3">
        <label for="selectSerie" class="form-label">Serie:</label>
        <div class="input-group">
          <select name="serie" id="selectSerie" class="form-control" required>
            <option disabled selected value="">Seleccione una serie</option>
            <?php foreach ($series as $s): ?>
              <option value="<?= $s['id'] ?>"><?= $s['nombre'] ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarSerie" title="Agregar serie">+</button>
          <div class="invalid-feedback">Selecciona una serie.</div>
        </div>
      </div>

      <!-- Escala -->
      <div class="col-md-6 mb-3">
        <label for="selectEscala" class="form-label">Escala:</label>
        <div class="input-group">
          <select name="escala" id="selectEscala" class="form-control" required>
            <option disabled selected value="">Seleccione una escala</option>
            <?php foreach ($escalas as $e): ?>
              <option value="<?= $e['id'] ?>"><?= $e['nombre'] ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarEscala" title="Agregar escala">+</button>
          <div class="invalid-feedback">Selecciona una escala.</div>
        </div>
      </div>

      <!-- Estado -->
      <div class="col-md-6 mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control" required>
          <option disabled selected value="">Seleccione un estado</option>
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id'] ?>"> <?= $e['nombre'] ?> </option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">Selecciona un estado.</div>
      </div>

      <!-- Fecha de lanzamiento -->
      <div class="col-md-6 mb-3">
        <label>Fecha de lanzamiento:</label>
        <input type="date" name="fecha_lanzamiento" class="form-control" required>
        <div class="invalid-feedback">Selecciona un fecha.</div>
      </div>

      <!-- Imágenes -->
      <div class="col-md-6 mb-3">
        <label>Imágenes del producto:</label>
        <input type="file" name="imagenes[]" id="input-imagenes" class="form-control" accept="image/*" multiple required>
        <div class="invalid-feedback">Por favor, sube al menos una imagen.</div>
        <div id="preview-imagenes" class="mt-3 d-flex flex-wrap gap-2"></div>
      </div>

      <!-- Botones -->
      <div class="col-12 mb-3 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-success">Guardar producto</button>
        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </div>
  </form>
</div>

<!-- Modal Agregar Categoría -->
<div class="modal fade" id="modalAgregarCategoria" tabindex="-1" aria-labelledby="modalAgregarCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formAgregarCategoria" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarCategoriaLabel">Agregar nueva categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" placeholder="Nombre de la nueva categoría" required>
        <div class="invalid-feedback">Ingresa un nombre válido para la categoría.</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>


  <!-- Modal Agregar Marca -->
<div class="modal fade" id="modalAgregarMarca" tabindex="-1" aria-labelledby="modalAgregarMarcaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formAgregarMarca" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarMarcaLabel">Agregar nueva marca</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre_marca" id="nombre_marca" class="form-control" placeholder="Nombre de la nueva marca" required>
        <div class="invalid-feedback">Ingresa un nombre válido para la marca.</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Agregar Serie -->
<div class="modal fade" id="modalAgregarSerie" tabindex="-1" aria-labelledby="modalAgregarSerieLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formAgregarSerie" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarSerieLabel">Agregar nueva serie</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nombre_serie" id="nombre_serie" class="form-control" placeholder="Nombre de la nueva serie" required>
        <div class="invalid-feedback">Ingresa un nombre válido para la serie.</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Agregar Escala -->
<div class="modal fade" id="modalAgregarEscala" tabindex="-1" aria-labelledby="modalAgregarEscalaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formAgregarEscala" class="modal-content needs-validation" novalidate>
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarEscalaLabel">Agregar nueva escala</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="valor_escala" id="valor_escala" class="form-control" placeholder="Ej: 1/7 o 1/12" required>
        <div class="invalid-feedback">Ingresa un valor válido para la escala (Ejem. 1/7 o 1/12).</div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación de formulario -->
<script>
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>

<!-- Previsualización de imágenes -->
<script>
  const inputImagenes = document.getElementById("input-imagenes");
  const previewContainer = document.getElementById("preview-imagenes");

  let archivosSeleccionados = [];

  inputImagenes.addEventListener("change", function () {
    // Actualizamos el arreglo con los nuevos archivos
    archivosSeleccionados = Array.from(inputImagenes.files);
    actualizarPrevisualizacion();
  });

  function actualizarPrevisualizacion() {
    previewContainer.innerHTML = "";

    archivosSeleccionados.forEach((file, index) => {
      if (!file.type.startsWith("image/")) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        const contenedor = document.createElement("div");
        contenedor.classList.add("position-relative");

        const img = document.createElement("img");
        img.src = e.target.result;
        img.alt = "Vista previa";
        img.style.width = "100px";
        img.style.height = "100px";
        img.style.objectFit = "cover";
        img.classList.add("rounded", "shadow", "img-thumbnail");

        const botonEliminar = document.createElement("button");
        botonEliminar.type = "button";
        botonEliminar.innerHTML = "&times;";
        botonEliminar.title = "Eliminar imagen";
        botonEliminar.classList.add("btn", "btn-sm", "btn-danger", "position-absolute", "top-0", "end-0");

        botonEliminar.addEventListener("click", () => {
          archivosSeleccionados.splice(index, 1); // Elimina del array
          actualizarInputFile();                  // Actualiza el input file real
          actualizarPrevisualizacion();           // Refresca vista previa
        });

        contenedor.appendChild(img);
        contenedor.appendChild(botonEliminar);
        previewContainer.appendChild(contenedor);
      };
      reader.readAsDataURL(file);
    });
  }

  function actualizarInputFile() {
    const dataTransfer = new DataTransfer();
    archivosSeleccionados.forEach(file => dataTransfer.items.add(file));
    inputImagenes.files = dataTransfer.files;
  }
</script>

<!-- Agregar categoría, marca, serie y escala -->
<script>
// Agregar Categoría
document.getElementById('formAgregarCategoria').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const nombre = document.getElementById('nombre_categoria').value.trim();

  // Validación Bootstrap
  if (!form.checkValidity()) {
    form.classList.add('was-validated');
    return;
  }

  // Si pasa validación, continuar
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

      // Limpiar campo, quitar validación y cerrar modal
      document.getElementById('nombre_categoria').value = '';
      form.classList.remove('was-validated');
      bootstrap.Modal.getInstance(document.getElementById('modalAgregarCategoria')).hide();
    } else {
      console.error("Respuesta completa del servidor:", data);
      alert("❌ Error: " + (data.error || "No se pudo guardar la categoría."));
    }
  })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("❌ Error inesperado al conectar con el servidor. Revisa la consola.");
    });
  });

  // Agregar Marca
  document.getElementById('formAgregarMarca').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const nombre = document.getElementById('nombre_marca').value.trim();

    // Validación Bootstrap
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    console.log("Enviando nombre:", nombre);

    fetch('ajax/agregar_marca.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const select = document.getElementById('selectMarca');
        const option = new Option(data.nombre, data.id, true, true);
        select.add(option);

        // Reset
        document.getElementById('nombre_marca').value = '';
        form.classList.remove('was-validated');
        bootstrap.Modal.getInstance(document.getElementById('modalAgregarMarca')).hide();
      } else {
        alert("❌ Error: " + (data.error || "No se pudo guardar la marca."));
      }
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("❌ Error inesperado al conectar con el servidor. Revisa la consola.");
    });
  });

  // Agregar Serie
  document.getElementById('formAgregarSerie').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const nombre = document.getElementById('nombre_serie').value.trim();

    // Validación Bootstrap
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    console.log("Enviando nombre:", nombre); 

    fetch('ajax/agregar_serie.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const select = document.getElementById('selectSerie');
        const option = new Option(data.nombre, data.id, true, true);
        select.add(option);

        // Reset
        document.getElementById('nombre_serie').value = '';
        form.classList.remove('was-validated');
        bootstrap.Modal.getInstance(document.getElementById('modalAgregarSerie')).hide();
      } else {
        alert("❌ Error: " + (data.error || "No se pudo guardar la serie."));
      }
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("❌ Error inesperado al conectar con el servidor. Revisa la consola.");
    });
  });

  // Agregar Escala
    document.getElementById('formAgregarEscala').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const valor = document.getElementById('valor_escala').value.trim();

    // Verifica validez del formulario
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    // Validar formato tipo 1/7, 1/12, etc.
    const formatoEscala = /^\d{1,2}\/\d{1,2}$/;
    if (!formatoEscala.test(valor)) {
      alert("El formato debe ser como 1/7, 1/12, 6/10, etc.");
      return;
    }

    console.log("Enviando escala:", valor);

    fetch('ajax/agregar_escala.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ valor })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const select = document.getElementById('selectEscala');
        const option = new Option(data.valor, data.id, true, true);
        select.add(option);

        // Limpiar y cerrar modal
        document.getElementById('valor_escala').value = '';
        form.classList.remove('was-validated');
        bootstrap.Modal.getInstance(document.getElementById('modalAgregarEscala')).hide();
      } else {
        alert("❌ Error: " + (data.error || "No se pudo guardar la escala."));
        console.error("DEBUG:", data.debug || data);
      }
    })
    .catch(error => {
      console.error("Error en la solicitud:", error);
      alert("❌ Error inesperado al conectar con el servidor. Revisa la consola.");
    });
  });
</script>

</body>
</html>