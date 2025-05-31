<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$idProducto = $_POST['id'] ?? null;
if (!$idProducto) {
    echo "❌ ID de producto no proporcionado.";
    exit;
}

// Obtener campos del formulario
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$categoria = $_POST['categoria'] ?? '';
$marca = $_POST['marca'] ?? '';
$serie = $_POST['serie'] ?? '';
$escala = $_POST['escala'] ?? '';
$estado = $_POST['estado'] ?? '';
$fechaLanzamiento = $_POST['fecha_lanzamiento'] ?? '';
$imagenesConservadas = $_POST['imagenes_anteriores'] ?? [];

if (!is_array($imagenesConservadas)) {
    $imagenesConservadas = [$imagenesConservadas];
}

// Validaciones
if ($precio < 0) {
    $_SESSION['error_edicion'] = "❌ El precio no puede ser negativo.";
    header("Location: editar_producto.php?id=$idProducto");
    exit;
}

// Función para extraer public_id
function obtenerPublicIdDesdeUrl($url) {
    $partes = explode('/', parse_url($url, PHP_URL_PATH));
    $archivo = end($partes);
    return preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $archivo);
}

// Subida de nuevas imágenes
$imagenesNuevas = [];
if (!empty($_FILES['imagenes']['name'][0])) {
    foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
            $filePath = $tmpName;
            $fileName = $_FILES['imagenes']['name'][$index];
            $timestamp = time();

            $params_to_sign = ['timestamp' => $timestamp];
            ksort($params_to_sign);
            $signature_base = http_build_query($params_to_sign) . CLOUDINARY_API_SECRET;
            $signature = sha1($signature_base);

            $post = [
                'file' => new CURLFile($filePath),
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
            if (!$response) {
                echo "❌ Error CURL: " . curl_error($ch);
                curl_close($ch);
                exit;
            }
            curl_close($ch);

            $json = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<p style='color:red;'>❌ Error al parsear respuesta de Cloudinary para $fileName.</p>";
                continue;
            }

            if (isset($json['secure_url'])) {
                $imagenesNuevas[] = $json['secure_url'];
            } else {
                echo "<p style='color:red;'>❌ Error al subir imagen $fileName: " . htmlspecialchars($json['error']['message'] ?? 'desconocido') . "</p>";
            }
        }
    }
}

// Obtener las imágenes originales y comparar
$productoData = obtenerProductoPorId($idProducto); // Usa la función del include
$imagenesOriginales = $productoData['imagenes'] ?? [];
$imagenesEliminadas = array_diff($imagenesOriginales, $imagenesConservadas);

// Eliminar imágenes de Cloudinary
foreach ($imagenesEliminadas as $img) {
    // Asegurar que sea un string
    $url = is_array($img) && isset($img['stringValue']) ? $img['stringValue'] : $img;

    if (!is_string($url)) continue; // por si acaso, ignorar datos raros

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

// Combinar imágenes nuevas con conservadas
$imagenesFinales = array_merge($imagenesConservadas, $imagenesNuevas);
$fechaSubida = date('c');

$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $nombre)), '-'));

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
        'imagenes' => ['arrayValue' => ['values' => array_map(fn($url) => ['stringValue' => $url], $imagenesFinales)]],
        'fecha_lanzamiento' => ['timestampValue' => $fechaLanzamiento . "T00:00:00Z"],
        'fecha_subida' => ['timestampValue' => $fechaSubida],
        'slug' => ['stringValue' => $slug]
    ]
];

$accessToken = getAccessToken(__DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/{$idProducto}?updateMask.fieldPaths=nombre&updateMask.fieldPaths=descripcion&updateMask.fieldPaths=precio&updateMask.fieldPaths=categoriasID&updateMask.fieldPaths=marcasID&updateMask.fieldPaths=seriesID&updateMask.fieldPaths=escalasID&updateMask.fieldPaths=estadosID&updateMask.fieldPaths=imagenes&updateMask.fieldPaths=fecha_lanzamiento&updateMask.fieldPaths=fecha_subida&updateMask.fieldPaths=slug";

$options = [
    'http' => [
        'method' => 'PATCH',
        'header' => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        'content' => json_encode($producto)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
if ($response === false) {
    $_SESSION['error_edicion'] = "❌ Error al guardar los cambios.";
    header("Location: editar_producto.php?id=$idProducto");
    exit;
}

$_SESSION['mensaje_exito'] = "✅ Producto actualizado correctamente.";
header('Location: productos.php');
exit;
