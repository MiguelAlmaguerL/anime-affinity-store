<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php'; // Aquí están las constantes API

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

// Obtener los datos del producto
$getUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/{$id}";
$getHeaders = [
    "Authorization: Bearer $accessToken"
];
$getOptions = [
    'http' => [
        'method' => 'GET',
        'header' => implode("\r\n", $getHeaders)
    ]
];
$getResponse = @file_get_contents($getUrl, false, stream_context_create($getOptions));

if ($getResponse !== false) {
    $data = json_decode($getResponse, true);
    $imagenes = $data['fields']['imagenes']['arrayValue']['values'] ?? [];

    // Función reutilizable para extraer public_id desde una URL
    function obtenerPublicIdDesdeUrl($url) {
        $partes = explode('/', parse_url($url, PHP_URL_PATH));
        $archivo = end($partes);
        return preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $archivo);
    }

    // Eliminar cada imagen de Cloudinary
    foreach ($imagenes as $img) {
        $url = $img['stringValue'] ?? null;
        if (!$url) continue;

        $publicId = obtenerPublicIdDesdeUrl($url);
        $timestamp = time();

        $params_to_sign = [
            'public_id' => $publicId,
            'timestamp' => $timestamp
        ];
        ksort($params_to_sign);
        $signature_base = http_build_query($params_to_sign) . CLOUDINARY_API_SECRET;
        $signature = sha1($signature_base);

        $post = [
            'public_id' => $publicId,
            'api_key' => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_exec($ch);
        curl_close($ch);
    }
}

//  Eliminar el producto de Firebase
$deleteUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/{$id}";
$deleteOptions = [
    'http' => [
        'method' => 'DELETE',
        'header' => implode("\r\n", $getHeaders)
    ]
];

$deleteResponse = @file_get_contents($deleteUrl, false, stream_context_create($deleteOptions));

if ($deleteResponse === false) {
    $_SESSION['error_eliminar'] = '❌ Error al eliminar el producto.';
} else {
    $_SESSION['mensaje_exito'] = '✅ Producto eliminado correctamente.';
}

header('Location: productos.php');
exit;