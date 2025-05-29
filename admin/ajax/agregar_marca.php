<?php
require_once __DIR__ . '/../../includes/firebase_fetch.php';

header('Content-Type: application/json');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if (!$input || !isset($input['nombre'])) {
    echo json_encode(['success' => false, 'error' => 'No se recibió el nombre correctamente']);
    exit;
}

$nombre = trim($input['nombre']);
if ($nombre === '') {
    echo json_encode(['success' => false, 'error' => 'Nombre vacío']);
    exit;
}

// Slug
function generarSlug($texto) {
    $slug = strtolower($texto);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

$slug = generarSlug($nombre);

// URL con documentId
$projectId = 'affinityanimestore';
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/marcas?documentId={$slug}";
$accessToken = getAccessToken(__DIR__ . '/../../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');

$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$body = json_encode([
    'fields' => [
        'nombre' => ['stringValue' => $nombre]
    ]
]);

$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $body
    ]
];

// Ejecutar solicitud
$response = @file_get_contents($url, false, stream_context_create($options));

// ✅ Manejo de errores de conexión
if ($response === false) {
    $error = error_get_last();
    echo json_encode([
        'success' => false,
        'error' => 'No se pudo conectar con Firebase',
        'debug' => $error['message'] ?? 'Sin detalles'
    ]);
    exit;
}

// ✅ Procesar respuesta
$data = json_decode($response, true);

// Si ya existe
if (isset($data['error']['status']) && $data['error']['status'] === 'ALREADY_EXISTS') {
    echo json_encode([
        'success' => false,
        'error' => 'Ya existe una marca con ese nombre.'
    ]);
    exit;
}

// Si no hay error pero no devuelve name (raro)
if (!isset($data['name'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Respuesta inválida de Firebase.',
        'debug' => $data
    ]);
    exit;
}

// ✅ Sé logró
echo json_encode([
    'success' => true,
    'id' => basename($data['name']),
    'nombre' => $nombre
]);
