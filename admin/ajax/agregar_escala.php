<?php
require_once __DIR__ . '/../../includes/firebase_fetch.php';

header('Content-Type: application/json');

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if (!$input || !isset($input['valor'])) {
    echo json_encode(['success' => false, 'error' => 'No se recibió el valor']);
    exit;
}

$valor = trim($input['valor']);
if ($valor === '') {
    echo json_encode(['success' => false, 'error' => 'Valor vacío']);
    exit;
}

// Formato personalizado para slug de escala
function slugEscala($texto) {
    // Convierte 1/7 en 01-07 ó 1/12 en 01-12
    if (!preg_match('/^(\d{1,2})\/(\d{1,2})$/', $texto, $matches)) {
        return false;
    }
    return sprintf('%02d-%02d', $matches[1], $matches[2]);
}


$slug = slugEscala($valor);

// Validar formato del slug
if (!$slug) {
    echo json_encode(['success' => false, 'error' => 'Formato inválido. Usa formato tipo 1/7, 1/12, etc.']);
    exit;
}

// URL con documentId
$projectId = 'affinityanimestore';
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/escalas?documentId={$slug}";
$accessToken = getAccessToken(__DIR__ . '/../../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');

$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$body = json_encode([
    'fields' => [
        'valor' => ['stringValue' => $valor]
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
        'error' => 'Ya existe una escala con ese valor.'
    ]);
    exit;
}

// Si no hay error pero no devuelve name (raro)
if (!isset($data['name'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Error inesperado.', 
        'debug' => $data
    ]);
    exit;
}

// ✅ Sé logró
echo json_encode([
    'success' => true,
    'id' => basename($data['name']),
    'valor' => $valor
]);