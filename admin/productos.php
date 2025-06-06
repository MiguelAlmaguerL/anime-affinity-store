<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

// Capturar búsqueda
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$limite = 21;

// Capturar paginación
$startAfter = isset($_GET['after']) ? $_GET['after'] : null;

if ($busqueda !== '') {
    // Usar búsqueda local (hasta 250) y filtrar manualmente
    $todosLosProductos = obtenerProductosParaBusqueda(250);
    $productos = array_filter($todosLosProductos, function ($p) use ($busqueda) {
        return stripos($p['nombre'], $busqueda) !== false;
    });
    $mostrarBotonVerMas = false;  // No hay paginación en búsqueda
} else {
    // Sin búsqueda: usar paginación normal
    $productos = obtenerProductosPaginados($limite, $startAfter);
    $mostrarBotonVerMas = count($productos) === $limite;
}
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
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        <!-- <a href="dashboard.php" class="btn btn-outline-secondary">← Volver</a> -->
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <form method="get" class="d-flex" style="max-width: 600px;">
                <input type="text" name="q" class="form-control me-2" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($busqueda) ?>">
                <button type="submit" class="btn btn-primary me-2">Buscar</button>
            </form>
            <a href="productos.php" class="btn btn-outline-secondary">← Ver desde el inicio</a>
        </div>

        <div class="d-flex gap-2">
            <a href="carrusel.php" class="btn btn-outline-dark">🎞️ Carrusel de Imágenes</a>
            <a href="agregar_producto.php" class="btn btn-success">+ Agregar Producto</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark align-middle">
                <tr>
                    <th class="text-center align-middle">Imagen</th>
                    <th>Nombre</th>
                    <th class="text-center align-middle">Precio</th>
                    <th class="text-center align-middle">Estado</th>
                    <th class="text-center align-middle">Fecha de Subida</th>
                    <th class="text-center align-middle">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td class="text-center">
                        <img src="<?= htmlspecialchars($producto['imagenes'][0] ?? '../assets/img/default.png') ?>"
                        width="80" class="img-thumbnail" alt=" ">
                    </td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td class="text-center">$<?= number_format($producto['precio'], 2) ?> MXN</td>
                    <td class="text-center">
                        <?php
                            // Mostrar un estado legible en la tabla
                            if (strpos($producto['estado'], 'inventario') !== false) echo 'Disponible';
                                elseif (strpos($producto['estado'], 'preventa') !== false) echo 'Preventa';
                                elseif (strpos($producto['estado'], 'en-camino') !== false) echo 'En Camino';
                                elseif (strpos($producto['estado'], 'sin-existencia') !== false) echo 'Sin Existencia';
                            else echo 'Otro';
                        ?>
                    </td>
                    <td class="text-center"><?= date('Y-m-d', strtotime($producto['fecha_subida'] ?? 'now')) ?></td>
                    <td class="text-center align-middle">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="editar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <button class="btn btn-sm btn-danger btn-confirmar-eliminar"
                                data-id="<?= $producto['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($producto['nombre']) ?>">
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($mostrarBotonVerMas): ?>
            <div class="text-center my-3">
                <a href="?after=<?= urlencode($productos[$limite - 1]['fecha_subida']) ?>" class="btn btn-outline-primary">Ver más</a>
            </div>
        <?php endif; ?>
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

<!-- Script para manejar el modal de eliminación -->
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