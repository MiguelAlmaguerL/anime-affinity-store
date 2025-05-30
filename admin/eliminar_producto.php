<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error_eliminar'] = 'ID de producto no especificado.';
    header('Location: productos.php');
    exit;
}

$accessToken = getAccessToken(__DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');
$projectId = 'affinityanimestore';

$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/{$id}";
$headers = [
    "Authorization: Bearer $accessToken"
];

$options = [
    'http' => [
        'method' => 'DELETE',
        'header' => implode("\r\n", $headers)
    ]
];

$response = @file_get_contents($url, false, stream_context_create($options));

if ($response === false) {
    $_SESSION['error_eliminar'] = '❌ Error al eliminar el producto.';
} else {
    $_SESSION['mensaje_exito'] = '✅ Producto eliminado correctamente.';
}

header('Location: productos.php');
exit;