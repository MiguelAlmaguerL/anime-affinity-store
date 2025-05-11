<?php
session_start();

// 🔐 Datos de acceso válidos (puedes cambiarlos o ponerlos en Firestore después)
$usuario_valido = 'admin';
$contrasena_valida = '123456'; // ← se puede cifrar más adelante

// 🔎 Obtener los datos del formulario
$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// ✅ Validar credenciales
if ($usuario === $usuario_valido && $contrasena === $contrasena_valida) {
    $_SESSION['admin_logueado'] = true;
    header('Location: dashboard.php');
    exit;
} else {
    $_SESSION['error'] = 'Usuario o contraseña incorrectos.';
    header('Location: login.php');
    exit;
}
