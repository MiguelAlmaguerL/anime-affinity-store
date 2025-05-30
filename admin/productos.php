<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}
$productos = obtenerProductosPaginados(4); // Limite de 10 productos para la tabla
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos - Panel de Administración</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['mensaje_exito']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_eliminar'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_eliminar']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_eliminar']); ?>
        <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Productos</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <div class="mb-3 text-end">
        <a href="agregar_producto.php" class="btn btn-success">+ Agregar Producto</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>
                        <img src="<?= htmlspecialchars($producto['imagenes'][0] ?? '../assets/img/default.png') ?>"
                        width="80" class="img-thumbnail" alt=" ">
                    </td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td>$<?= number_format($producto['precio'], 2) ?> MXN</td>
                    <td>
                        <?php
                            // Mostrar un estado legible en la tabla
                            if (strpos($producto['estado'], 'inventario') !== false) echo 'Disponible';
                                elseif (strpos($producto['estado'], 'preventa') !== false) echo 'Preventa';
                                elseif (strpos($producto['estado'], 'en-camino') !== false) echo 'En Camino';
                                elseif (strpos($producto['estado'], 'sin-existencia') !== false) echo 'Sin Existencia';
                            else echo 'Otro';
                        ?>
                    </td>
                    <td><?= date('Y-m-d', strtotime($producto['fecha_subida'] ?? 'now')) ?></td>
                    <td>
                        <a href="editar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                        <button class="btn btn-sm btn-danger btn-confirmar-eliminar" 
                            data-id="<?= $producto['id'] ?>" 
                            data-nombre="<?= htmlspecialchars($producto['nombre']) ?>">
                            Eliminar
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarLabel">¿Eliminar producto?</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar el producto <strong id="nombreProductoEliminar"></strong>? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer">
        <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">Sí, eliminar</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const modalEliminar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
  const botonesEliminar = document.querySelectorAll('.btn-confirmar-eliminar');
  const nombreSpan = document.getElementById('nombreProductoEliminar');
  const btnConfirmar = document.getElementById('btnConfirmarEliminar');

  botonesEliminar.forEach(boton => {
    boton.addEventListener('click', () => {
      const nombre = boton.dataset.nombre;
      const id = boton.dataset.id;
      nombreSpan.textContent = nombre;
      btnConfirmar.href = `eliminar_producto.php?id=${id}`;
      modalEliminar.show();
    });
  });
</script>

</body>
</html>