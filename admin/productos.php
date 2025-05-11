<?php
session_start();

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
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
                <!-- 🔻 Productos renderizados dinámicamente -->
                <tr>
                    <td><img src="../assets/img/ejemplo1.png" width="80" class="img-thumbnail" alt="Producto"></td>
                    <td>Zoro Roronoa</td>
                    <td>$1,199.00 MXN</td>
                    <td>Disponible</td>
                    <td>2024-05-09</td>
                    <td>
                        <a href="editar_producto.php?id=123" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_producto.php?id=123" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
                <!-- Más productos aquí -->
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
