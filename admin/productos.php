//Miguel estuvo aqui
<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}
$productos = obtenerProductosPaginados(21); // Limite de 21 productos para la tabla
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
                            // Mostrar un estado legible
                        if (strpos($producto['estado'], 'inventario') !== false) echo 'Disponible';
                        elseif (strpos($producto['estado'], 'preventa') !== false) echo 'Preventa';
                        elseif (strpos($producto['estado'], 'en-camino') !== false) echo 'En Camino';
                        elseif (strpos($producto['estados'], 'sin-existencia') !== false) echo 'Sin Existencia';
                        else echo 'Otro';
                        ?>
                    </td>
                    <td><?= date('Y-m-d', strtotime($producto['fecha_subida'] ?? 'now')) ?></td>
                    <td>
                        <a href="editar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_producto.php?id=<?= urlencode($producto['id']) ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
