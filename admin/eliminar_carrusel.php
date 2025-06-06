<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error_eliminar'] = 'ID de la imagen no especificado.';
    header('Location: carrusel.php');
    exit;
}

$accessToken = getAccessToken(__DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');
$projectId = 'affinityanimestore';

// Obtener los datos del documento
$getUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/carrusel_img/{$id}";
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
    $url = $data['fields']['url']['stringValue'] ?? null;

    // Eliminar de Cloudinary si hay URL
    if ($url) {
        function obtenerPublicIdDesdeUrl($url) {
            $partes = explode('/', parse_url($url, PHP_URL_PATH));
            $archivo = end($partes);
            return preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $archivo);
        }

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

// Eliminar el documento de Firestore
$deleteUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/carrusel_img/{$id}";
$deleteOptions = [
    'http' => [
        'method' => 'DELETE',
        'header' => implode("\r\n", $getHeaders)
    ]
];

$deleteResponse = @file_get_contents($deleteUrl, false, stream_context_create($deleteOptions));

if ($deleteResponse === false) {
    $_SESSION['error_eliminar'] = '❌ Error al eliminar la imagen del carrusel.';
} else {
    $_SESSION['mensaje_exito'] = '✅ Imagen del carrusel eliminada correctamente.';
}

header('Location: carrusel.php');
exit;
