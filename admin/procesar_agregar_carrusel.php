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

// Validar el campo de "orden"
if (!filter_var($orden, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    $_SESSION['error_carrusel'] = '❌ El campo "Orden" debe ser un número entero mayor que cero.';
    header('Location: agregar_carrusel.php');
    exit;
}

// Verificar que el campo "orden" no esté duplicado
$ordenRepetidoUrl = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";
$consultaOrden = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'carrusel_img']],
        'where' => [
            'fieldFilter' => [
                'field' => ['fieldPath' => 'orden'],
                'op' => 'EQUAL',
                'value' => ['integerValue' => $orden]
            ]
        ],
        'limit' => 1
    ]
];

$ordenContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]),
        'content' => json_encode($consultaOrden)
    ]
]);

$ordenResponse = file_get_contents($ordenRepetidoUrl, false, $ordenContext);
$ordenData = json_decode($ordenResponse, true);

// Verificamos si existe al menos un resultado con el mismo "orden"
foreach ($ordenData as $entry) {
    if (isset($entry['document'])) {
        $_SESSION['error_carrusel'] = "❌ Ya existe una imagen con ese número de orden. Por favor, asigne uno diferente e intente nuevamente.";
        header('Location: agregar_carrusel.php');
        exit;
    }
}

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
header('Location: carrusel.php?agregado=1');
exit;
?>