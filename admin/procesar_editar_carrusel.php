<?php
session_start();
require_once __DIR__ . '/../includes/firebase_fetch.php';
require_once __DIR__ . '/../cloudinary/cloudinary_config.php';

if (!isset($_SESSION['admin_logueado']) || $_SESSION['admin_logueado'] !== true) {
    header('Location: login.php');
    exit;
}

$idCarrusel = $_POST['id'] ?? null;
if (!$idCarrusel) {
    echo "❌ ID de carrusel no proporcionado.";
    exit;
}

// Obtener campos del formulario
$titulo = $_POST['titulo'] ?? '';
$activo = isset($_POST['activo']) ? true : false;
$imagenAnterior = $_POST['imagen_actual'] ?? '';
$orden_raw = $_POST['orden'] ?? '';
$orden = trim($orden_raw);

// Función para extraer public_id
function obtenerPublicIdDesdeUrl($url) {
    $partes = explode('/', parse_url($url, PHP_URL_PATH));
    $archivo = end($partes);
    return preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $archivo);
}

// Validación del campo orden
if (!filter_var($orden, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
    $_SESSION['error_carrusel'] = '❌ El campo "Orden" debe ser un número entero mayor que cero.';
    header("Location: editar_carrusel.php?id=$idCarrusel");
    exit;
}

$orden = (int) $orden;

// Verificar que el campo "orden" no esté duplicado
$accessToken = getAccessToken(__DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json');

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

// Revisar si hay algún documento con ese orden (distinto del actual)
$ordenDuplicado = false;

foreach ($ordenData as $entry) {
    if (isset($entry['document'])) {
        $docPath = $entry['document']['name'] ?? '';
        $docParts = explode('/', $docPath);
        $foundId = end($docParts);

        if ($foundId !== $idCarrusel) {
            $ordenDuplicado = true;
            break;
        }
    }
}

if ($ordenDuplicado) {
    $_SESSION['error_carrusel'] = "❌ Ya existe una imagen con ese número de orden. Por favor, asigne uno diferente e intente nuevamente.";
    header("Location: editar_carrusel.php?id=$idCarrusel");
    exit;
}

// Subida de nueva imagen (si hay)
$nuevaImagenURL = '';
if (!empty($_FILES['imagen']['name'])) {
    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
        $filePath = $_FILES['imagen']['tmp_name'];
        $fileName = $_FILES['imagen']['name'];
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
            exit;
        }

        if (isset($json['secure_url'])) {
            $nuevaImagenURL = $json['secure_url'];
        } else {
            echo "<p style='color:red;'>❌ Error al subir imagen $fileName: " . htmlspecialchars($json['error']['message'] ?? 'desconocido') . "</p>";
            exit;
        }

        // Si había imagen anterior, eliminarla
        if (!empty($imagenAnterior)) {
            $publicId = obtenerPublicIdDesdeUrl($imagenAnterior);

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
}

// Si no se subió nueva imagen, conservar la anterior
$imagenFinal = !empty($nuevaImagenURL) ? $nuevaImagenURL : $imagenAnterior;

$fechaSubida = date('c');

// Construir payload para Firestore
$carrusel = [
    'fields' => [
        'titulo' => ['stringValue' => $titulo],
        'url' => ['stringValue' => $imagenFinal],
        'orden' => ['integerValue' => $orden],
        'activo' => ['booleanValue' => $activo],
        'fecha_subida' => ['timestampValue' => $fechaSubida]
    ]
];

$url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/carrusel_img/{$idCarrusel}?updateMask.fieldPaths=titulo&updateMask.fieldPaths=descripcion&updateMask.fieldPaths=url&updateMask.fieldPaths=enlace&updateMask.fieldPaths=orden&updateMask.fieldPaths=activo&updateMask.fieldPaths=fecha_subida";

$options = [
    'http' => [
        'method' => 'PATCH',
        'header' => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        'content' => json_encode($carrusel)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));
if ($response === false) {
    $_SESSION['error_edicion'] = "❌ Error al guardar los cambios.";
    header("Location: editar_carrusel.php?id=$idCarrusel");
    exit;
}

$_SESSION['mensaje_exito'] = "✅ Carrusel actualizado correctamente.";
header('Location: carrusel.php');
exit;
