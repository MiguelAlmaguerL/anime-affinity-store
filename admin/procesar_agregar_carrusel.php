<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

// Obtener campos del formulario
$titulo = $_POST['titulo'] ?? '';
$orden = intval($_POST['orden'] ?? 0);
$fechaSubida = date('c'); // ISO 8601
$activo = isset($_POST['activo']) && $_POST['activo'] === 'true' ? true : false;

// Subir imagen a Cloudinary
$imagenUrl = '';

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['imagen']['tmp_name'];
    $fileName = $_FILES['imagen']['name'];
    $timestamp = time();

    // Crear firma Cloudinary
    $params_to_sign = ['timestamp' => $timestamp];
    ksort($params_to_sign);
    $signature_base = http_build_query($params_to_sign) . CLOUDINARY_API_SECRET;
    $signature = sha1($signature_base);

    // POST a Cloudinary
    $post = [
        'file' => new CURLFile($tmpName),
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);

    if (isset($json['secure_url'])) {
        $imagenUrl = $json['secure_url'];
    } else {
        $_SESSION['error_carrusel'] = "❌ Error al subir la imagen: " . htmlspecialchars($json['error']['message'] ?? 'desconocido');
        header('Location: agregar_carrusel.php');
        exit;
    }
} else {
    $_SESSION['error_carrusel'] = "❌ No se seleccionó ninguna imagen válida.";
    header('Location: agregar_carrusel.php');
    exit;
}

// Armar documento para Firestore
$carruselDoc = [
    'fields' => [
        'url' => ['stringValue' => $imagenUrl],
        'titulo' => ['stringValue' => $titulo],
        'orden' => ['integerValue' => $orden],
        'fecha_subida' => ['timestampValue' => $fechaSubida],
        'activo' => ['booleanValue' => $activo]
    ]
];

$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/carrusel_img";
$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => json_encode($carruselDoc)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
if ($response === false) {
    $_SESSION['error_carrusel'] = "❌ Error al guardar la imagen del carrusel.";
    header('Location: agregar_carrusel.php');
    exit;
}

// Redirigir con éxito
header('Location: productos.php?agregado=1');
exit;
?>