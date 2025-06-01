<?php
session_start();

require_once __DIR__ . '/../includes/firebase_fetch.php';

$FIREBASE_API_KEY = obtenerApiKeyFirebase($keyFilePath);

// Obtener datos del formulario
$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';

// Endpoint de Firebase Auth REST API
$url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$FIREBASE_API_KEY";

// Datos para la petición
$data = [
    'email' => $usuario,
    'password' => $contrasena,
    'returnSecureToken' => true
];

// Configurar petición cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['idToken'])) {
    // Opcional: Solo permitir acceso a ciertos correos
    $adminEmails = ['anime.affinity.store@gmail.com']; // Cambia esto por el correo de tu admin
    if (!in_array(strtolower($usuario), $adminEmails)) {
        $_SESSION['error'] = 'No tienes permisos de administrador.';
        header('Location: login.php');
        exit;
    }

    // Login exitoso
    $_SESSION['admin_logueado'] = true;
    $_SESSION['firebase_id_token'] = $result['idToken'];
    $_SESSION['firebase_email'] = $usuario;
    header('Location: productos.php');
    exit;
} else {
    // Error de autenticación
    $_SESSION['error'] = 'Usuario o contraseña incorrectos.';
    header('Location: login.php');
    exit;
}
