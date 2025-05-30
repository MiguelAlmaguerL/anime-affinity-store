<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

// Validar ID
$idProducto = $_POST['id'] ?? null;
if (!$idProducto) {
    $_SESSION['error_editar'] = 'ID de producto no proporcionado.';
    header('Location: productos.php');
    exit;
}

// Campos del formulario
$nombre       = $_POST['nombre'] ?? '';
$descripcion  = $_POST['descripcion'] ?? '';
$precio       = floatval($_POST['precio'] ?? 0);
$categoria    = $_POST['categoria'] ?? '';
$marca        = $_POST['marca'] ?? '';
$serie        = $_POST['serie'] ?? '';
$escala       = $_POST['escala'] ?? '';
$estado       = $_POST['estado'] ?? '';
$fechaLanz    = $_POST['fecha_lanzamiento'] ?? '';
$imagenesViejas = $_POST['imagenes_anteriores'] ?? []; // Array con URLs de imágenes que el usuario decidió conservar
$fechaEdicion = date('c');

// Verificación del precio negativo
if ($precio < 0) {
    $_SESSION['error_editar'] = 'El precio no puede ser negativo.';
    header("Location: editar_producto.php?id=$idProducto");
    exit;
}

// Subir nuevas imágenes a Cloudinary
$imagenesNuevas = [];
foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
    if ($_FILES['imagenes']['error'][$index] === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
        $filePath = $tmpName;
        $fileName = $_FILES['imagenes']['name'][$index];
        $timestamp = time();

        $params_to_sign = ['timestamp' => $timestamp];
        ksort($params_to_sign);
        $signature_base = http_build_query($params_to_sign) . $api_secret;
        $signature = sha1($signature_base);

        $post = [
            'file' => new CURLFile($filePath),
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        if (isset($json['secure_url'])) {
            $imagenesNuevas[] = $json['secure_url'];
        }
    }
}

// Combinar imágenes finales: conservadas + nuevas
$imagenesFinales = array_merge($imagenesViejas, $imagenesNuevas);

// Construir nuevo documento
$productoActualizado = [
    'fields' => [
        'nombre'     => ['stringValue' => $nombre],
        'descripcion'=> ['stringValue' => $descripcion],
        'precio'     => ['doubleValue' => $precio],
        'categoriasID' => ['referenceValue' => "projects/$projectId/databases/(default)/documents/categorias/$categoria"],
        'marcasID'    => ['referenceValue' => "projects/$projectId/databases/(default)/documents/marcas/$marca"],
        'seriesID'    => ['referenceValue' => "projects/$projectId/databases/(default)/documents/series/$serie"],
        'escalasID'   => ['referenceValue' => "projects/$projectId/databases/(default)/documents/escalas/$escala"],
        'estadosID'   => ['referenceValue' => "projects/$projectId/databases/(default)/documents/estados/$estado"],
        'imagenes'    => ['arrayValue' => ['values' => array_map(fn($url) => ['stringValue' => $url], $imagenesFinales)]],
        'fecha_lanzamiento' => ['timestampValue' => $fechaLanz . "T00:00:00Z"],
        'fecha_subida' => ['timestampValue' => $fechaEdicion],
        'slug' => ['stringValue' => strtolower(str_replace(' ', '-', $nombre))]
    ]
];

// Enviar a Firebase (método PATCH)
$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/$idProducto?updateMask.fieldPaths=nombre&updateMask.fieldPaths=descripcion&updateMask.fieldPaths=precio&updateMask.fieldPaths=categoriasID&updateMask.fieldPaths=marcasID&updateMask.fieldPaths=seriesID&updateMask.fieldPaths=escalasID&updateMask.fieldPaths=estadosID&updateMask.fieldPaths=imagenes&updateMask.fieldPaths=fecha_lanzamiento&updateMask.fieldPaths=fecha_subida&updateMask.fieldPaths=slug";

$headers = [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
];

$options = [
    'http' => [
        'method' => 'PATCH',
        'header' => implode("\r\n", $headers),
        'content' => json_encode($productoActualizado)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
if ($response === false) {
    $_SESSION['error_editar'] = 'Hubo un error al actualizar el producto.';
    header("Location: editar_producto.php?id=$idProducto");
    exit;
}

// Éxito
$_SESSION['mensaje_exito'] = '✅ Producto actualizado correctamente.';
header("Location: productos.php");
exit;