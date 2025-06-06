<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$imagenes = obtenerTodosLosCarrusel();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Carrusel - Panel de Administración</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
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
        <h2>Gestión del Carrusel</h2>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="productos.php" class="btn btn-outline-secondary">← Volver al Panel de Productos</a>
        </div>
        <a href="agregar_carrusel.php" class="btn btn-success">+ Agregar Imagen</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark align-middle">
                <tr>
                    <th class="text-center align-middle">Imagen</th>
                    <th>Título</th>
                    <th class="text-center align-middle">Estado</th>
                    <th class="text-center align-middle">Fecha de Subida</th>
                    <th class="text-center align-middle">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($imagenes as $img): ?>
                <tr>
                    <td class="text-center"><img src="<?= htmlspecialchars($img['url']) ?>" width="120" class="img-thumbnail" alt="Imagen carrusel"></td>
                    <td><?= htmlspecialchars($img['titulo']) ?></td>
                    <td class="text-center"><?= $img['activo'] === true ? 'Publicado' : 'Sin Publicar' ?></td>
                    <td class="text-center"><?= date('Y-m-d', strtotime($img['fecha_subida'])) ?></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="editar_carrusel.php?id=<?= urlencode($img['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <button class="btn btn-sm btn-danger btn-confirmar-eliminar"
                                data-id="<?= $img['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($img['titulo']) ?>">
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarLabel">¿Eliminar imagen?</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas eliminar la imagen titulada <strong id="tituloEliminar"></strong>?
      </div>
      <div class="modal-footer">
        <a href="#" id="btnEliminarConfirmado" class="btn btn-danger">Sí, eliminar</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
  const botonesEliminar = document.querySelectorAll('.btn-confirmar-eliminar');
  const tituloEliminar = document.getElementById('tituloEliminar');
  const btnEliminar = document.getElementById('btnEliminarConfirmado');

  botonesEliminar.forEach(boton => {
    boton.addEventListener('click', () => {
      const titulo = boton.dataset.nombre;
      const id = boton.dataset.id;
      tituloEliminar.textContent = titulo;
      btnEliminar.href = `eliminar_carrusel.php?id=${id}`;
      modal.show();
    });
  });
</script>

</body>
</html>