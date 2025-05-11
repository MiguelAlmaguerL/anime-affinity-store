<?php
session_start();

// 🛑 Verifica si el admin está logueado
if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Anime Affinity Store</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet"> <!-- opcional para estilos -->
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Bienvenido al Panel de Administración</h1>

        <div class="d-flex justify-content-center gap-3">
            <a href="productos.php" class="btn btn-primary">Gestionar Productos</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>
