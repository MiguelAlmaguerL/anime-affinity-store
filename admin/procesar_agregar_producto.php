<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

// Cloudinary config
$cloud_name = '...';
$api_key = '...';
$api_secret = '...';

// Procesar campos del formulario
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$categoria = $_POST['categoria'] ?? '';
$marca = $_POST['marca'] ?? '';
$serie = $_POST['serie'] ?? '';
$escala = $_POST['escala'] ?? '';
$estado = $_POST['estado'] ?? '';
$fechaLanzamiento = $_POST['fecha_lanzamiento'] ?? '';
$fechaSubida = date('c'); // Formato ISO 8601

// Subir imagenes a Cloudinary
$imagenes = [];

foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
    // Validar que sea un archivo valido
    if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
        $filePath = $tmpName;
        $fileName = $_FILES['imagenes']['name'][$index];
        $timestamp = time();

        $params_to_sign = [
            'timestamp' => $timestamp,
        ];

        // Crear la firma para la solicitud
        ksort($params_to_sign);
        $signature_base = http_build_query($params_to_sign) . $api_secret;
        $signature = sha1($signature_base);

        $post = [
            'file' => new CURLFile($filePath),
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        // Ejecutar CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        if (isset($json['secure_url'])) {
            $imagenes[] = $json['secure_url'];
        } else {
            echo "<p style='color:red;'>❌ Error al subir imagen $fileName: " . htmlspecialchars($json['error']['message'] ?? 'desconocido') . "</p>";
        }
    }
}

// Construir el documento para Firebase
$producto = [
    'fields' => [
        'nombre' => ['stringValue' => $nombre],
        'descripcion' => ['stringValue' => $descripcion],
        'precio' => ['doubleValue' => $precio],
        'categoriasID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/categorias/$categoria"],
        'marcasID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/marcas/$marca"],
        'seriesID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/series/$serie"],
        'escalasID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/escalas/$escala"],
        'estadosID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/estados/$estado"],
        'imagenes' => ['arrayValue' => ['values' => array_map(fn($url) => ['stringValue' => $url], $imagenes)]],
        'fecha_lanzamiento' => ['timestampValue' => $fechaLanzamiento . "T00:00:00Z"],
        'fecha_subida' => ['timestampValue' => $fechaSubida],
        'slug' => ['stringValue' => strtolower(str_replace(' ', '-', $nombre))]
    ]
];

// Enviar a Firebase
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos";
$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => json_encode($producto)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
if ($response === false) {
    echo "Error al guardar el producto.";
    exit;
}

// Redirigir
header('Location: productos.php?agregado=1');
exit;
?>
