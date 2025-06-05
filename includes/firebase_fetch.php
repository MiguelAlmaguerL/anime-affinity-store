<?php
// Obtener token de acceso JWT
$keyFilePath = __DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json';
$projectId = 'affinityanimestore';
$accessToken = getAccessToken($keyFilePath);

// Obtener la API Key de Firebase
function obtenerApiKeyFirebase($keyFilePath) {
    $jsonKey = json_decode(file_get_contents($keyFilePath), true);
    return $jsonKey['api_key'] ?? '';
}

function getAccessToken($keyFilePath) {
    $authUrl = "https://oauth2.googleapis.com/token";
    $scopes = ["https://www.googleapis.com/auth/datastore"];

    $jsonKey = json_decode(file_get_contents($keyFilePath), true);

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $now = time();
    $claim = [
        'iss' => $jsonKey['client_email'],
        'scope' => implode(' ', $scopes),
        'aud' => $authUrl,
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $base64UrlHeader = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    $base64UrlClaim = rtrim(strtr(base64_encode(json_encode($claim)), '+/', '-_'), '=');
    $signatureInput = $base64UrlHeader . "." . $base64UrlClaim;

    openssl_sign($signatureInput, $signature, $jsonKey['private_key'], 'sha256WithRSAEncryption');
    $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    $jwt = $signatureInput . "." . $base64UrlSignature;

    $response = file_get_contents($authUrl, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'content' => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ])
        ]
    ]));

    $data = json_decode($response, true);
    return $data['access_token'];
}

// Función para obtener las imágenes del carrusel desde Firestore
function obtenerImagenesCarrusel() {
    global $accessToken, $projectId;

    if (empty($projectId) || empty($accessToken)) {
        echo "ERROR: Falta projectId o accessToken.";
        return [];
    }

    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $body = json_encode([
        "structuredQuery" => [
            "from" => [["collectionId" => "carrusel_img"]],
            "where" => [
                "fieldFilter" => [
                    "field" => ["fieldPath" => "activo"],
                    "op" => "EQUAL",
                    "value" => ["booleanValue" => true]
                ]
            ],
            "orderBy" => [[
                "field" => ["fieldPath" => "orden"],
                "direction" => "ASCENDING"
            ]],
            "limit" => 50
        ]
    ]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $entries = json_decode($response, true);
    $imagenes = [];

    foreach ($entries as $entry) {
        if (!isset($entry['document']['fields'])) continue;

        $fields = $entry['document']['fields'];
        $imagenes[] = [
            'url' => $fields['url']['stringValue'] ?? '',
            'titulo' => $fields['titulo']['stringValue'] ?? '',
            'orden' => (int)($fields['orden']['integerValue'] ?? 0),
            'fecha_subida' => $fields['fecha_subida']['timestampValue'] ?? ''
        ];
    }
    return $imagenes;
}


//Función para traer productos con estado de 'Inventario' o 'Existencia' -- SOLO PARA PAGINA DE INICIO
function obtenerProductosInventario($limite = 4) {
    global $accessToken, $projectId;

    // Consulta sin filtro: trae los productos más recientes
    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $body = json_encode([
        "structuredQuery" => [
            "from" => [["collectionId" => "productos"]],
            "orderBy" => [[
                "field" => ["fieldPath" => "fecha_subida"],
                "direction" => "DESCENDING"
            ]],
            "limit" => 250  // mayor cantidad para poder filtrar después
        ]
    ]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!is_array($data)) return [];

    $productos = [];

    foreach ($data as $doc) {
        if (!isset($doc['document']['fields'])) continue;
        $fields = $doc['document']['fields'];
        $estadoRef = $fields['estadosID']['referenceValue'] ?? '';

        // Si la referencia contiene 'inventario', lo consideramos válido
        if (strpos($estadoRef, 'estados/inventario') !== false) {
            $productos[] = [
                'id' => basename($doc['document']['name']),
                'nombre' => $fields['nombre']['stringValue'] ?? '',
                'precio' => isset($fields['precio']['integerValue'])
                    ? (int)$fields['precio']['integerValue']
                    : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
                'imagenes' => array_map(
                    fn($img) => $img['stringValue'],
                    $fields['imagenes']['arrayValue']['values'] ?? []
                ),
                'estado_nombre' => 'En existencia'  // Valor fijo por ahora
            ];
        }

        // Limitamos manualmente en PHP
        if (count($productos) >= $limite) break;
    }

    return $productos;
}


//Función para mostrar los Productos en estado de 'Preventa' -- SOLO PARA PAGINA DE INICIO ---
function obtenerProductosPreventa($limite = 4) {
    global $accessToken, $projectId;

    // Consulta sin filtro: trae los productos más recientes
    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $body = json_encode([
        "structuredQuery" => [
            "from" => [["collectionId" => "productos"]],
            "orderBy" => [[
                "field" => ["fieldPath" => "fecha_subida"],
                "direction" => "DESCENDING"
            ]],
            "limit" => 250  // mayor cantidad para poder filtrar después
        ]
    ]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!is_array($data)) return [];

    $productos = [];

    foreach ($data as $doc) {
        if (!isset($doc['document']['fields'])) continue;
        $fields = $doc['document']['fields'];
        $estadoRef = $fields['estadosID']['referenceValue'] ?? '';

        //Si la referencia contiene 'preventa', lo consideramos válido
        if (strpos($estadoRef, 'estados/preventa') !== false) {
            $productos[] = [
                'id' => basename($doc['document']['name']),
                'nombre' => $fields['nombre']['stringValue'] ?? '',
                'precio' => isset($fields['precio']['integerValue'])
                    ? (int)$fields['precio']['integerValue']
                    : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
                'imagenes' => array_map(
                    fn($img) => $img['stringValue'],
                    $fields['imagenes']['arrayValue']['values'] ?? []
                ),
                'estado_nombre' => 'Preventa'  // Valor fijo por ahora
            ];
        }

        // Limitamos manualmente en PHP
        if (count($productos) >= $limite) break;
    }

    return $productos;
}

function obtenerProductosPaginados($limite = 21, $startAfter = null) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $structuredQuery = [
        "from" => [["collectionId" => "productos"]],
        "orderBy" => [[
            "field" => ["fieldPath" => "fecha_subida"],
            "direction" => "DESCENDING"
        ]],
        "limit" => $limite
    ];

    if ($startAfter) {
        $structuredQuery["startAt"] = [
            "values" => [[ "timestampValue" => $startAfter ]]
        ];
    }

    $body = json_encode(["structuredQuery" => $structuredQuery]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!is_array($data)) return [];

    $productos = [];

    foreach ($data as $doc) {
        if (!isset($doc['document']['fields'])) continue;

        $fields = $doc['document']['fields'];

        $productos[] = [
            'id'         => basename($doc['document']['name']),
            'nombre'     => $fields['nombre']['stringValue'] ?? '',
            //'descripcion'=> $fields['descripcion']['stringValue'] ?? '',
            'precio'     => isset($fields['precio']['integerValue']) 
                ? (int)$fields['precio']['integerValue']
                : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
            'imagenes'   => array_map(
                fn($img) => $img['stringValue'],
                $fields['imagenes']['arrayValue']['values'] ?? []
            ),
            'fecha_subida' => $fields['fecha_subida']['timestampValue'] ?? '',
            // Campos de referencia sin resolver
            'categoria'  => $fields['categoriasID']['referenceValue'] ?? '',
            'marca'      => $fields['marcasID']['referenceValue'] ?? '',
            'serie'      => $fields['seriesID']['referenceValue'] ?? '',
            'escala'     => $fields['escalasID']['referenceValue'] ?? '',
            'estado'     => $fields['estadosID']['referenceValue'] ?? ''
        ];
    }

    return $productos;
}


// Función para obtener productos con estado 'preventa' con paginación basada en fecha_subida ---
function obtenerProductosPreventaPaginados($limite = 21, $startAfter = null) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $structuredQuery = [
        "from" => [["collectionId" => "productos"]],
        "orderBy" => [[
            "field" => ["fieldPath" => "fecha_subida"],
            "direction" => "DESCENDING"
        ]],
        "limit" => 250 // Obtenemos más para filtrar manualmente los de 'preventa'
    ];

    // Si se envía un cursor, lo usamos como punto de partida
    if ($startAfter) {
        $structuredQuery["startAt"] = [
            "values" => [[ "timestampValue" => $startAfter ]]
        ];
    }

    $body = json_encode(["structuredQuery" => $structuredQuery]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!is_array($data)) return [];

    $productos = [];
    
    foreach ($data as $doc) {
        if (!isset($doc['document']['fields'])) continue;

        $fields = $doc['document']['fields'];
        $estadoRef = $fields['estadosID']['referenceValue'] ?? '';

        // Solo agregar productos que estén en 'preventa'
        if (strpos($estadoRef, 'estados/preventa') !== false) {
            $productos[] = [
                'id' => basename($doc['document']['name']),
                'nombre' => $fields['nombre']['stringValue'] ?? '',
                'precio' => isset($fields['precio']['integerValue']) 
                    ? (int)$fields['precio']['integerValue']
                    : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
                'imagenes' => array_map(
                    fn($img) => $img['stringValue'],
                    $fields['imagenes']['arrayValue']['values'] ?? []
                ),
                'fecha_subida' => $fields['fecha_subida']['timestampValue'] ?? '',
                'categoria'  => $fields['categoriasID']['referenceValue'] ?? '',
                'marca'      => $fields['marcasID']['referenceValue'] ?? '',
                'serie'      => $fields['seriesID']['referenceValue'] ?? '',
                'escala'     => $fields['escalasID']['referenceValue'] ?? '',
                'estado'     => $fields['estadosID']['referenceValue'] ?? ''
            ];

            // Cortamos al alcanzar el límite
            if (count($productos) >= $limite) break;
        }
    }

    return $productos;
}

// Función para obtener hasta 250 productos recientes para búsqueda local (este número podría ser mayor)
function obtenerProductosParaBusqueda($limite = 250) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $body = json_encode([
        "structuredQuery" => [
            "from" => [["collectionId" => "productos"]],
            "orderBy" => [[
                "field" => ["fieldPath" => "fecha_subida"],
                "direction" => "DESCENDING"
            ]],
            "limit" => $limite
        ]
    ]);

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!is_array($data)) return [];

    $productos = [];

    foreach ($data as $doc) {
        if (!isset($doc['document']['fields'])) continue;

        $fields = $doc['document']['fields'];

        $productos[] = [
            'id' => basename($doc['document']['name']),
            'nombre' => $fields['nombre']['stringValue'] ?? '',
            'precio' => isset($fields['precio']['integerValue'])
                ? (int)$fields['precio']['integerValue']
                : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
            'imagenes' => array_map(
                fn($img) => $img['stringValue'],
                $fields['imagenes']['arrayValue']['values'] ?? [],   
            ),
            // Campos de referencia sin resolver aún
            'categoria'  => $fields['categoriasID']['referenceValue'] ?? '',
            'marca'      => $fields['marcasID']['referenceValue'] ?? '',
            'serie'      => $fields['seriesID']['referenceValue'] ?? '',
            'escala'     => $fields['escalasID']['referenceValue'] ?? '',
            'estado'     => $fields['estadosID']['referenceValue'] ?? ''
        ];
    }

    return $productos;
}

// Función para traer producto por su ID y mostrarlo en Detalles.php ---
function obtenerProductoPorId($id) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/productos/{$id}";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers)
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return null;

    $doc = json_decode($response, true);
    if (!isset($doc['fields'])) return null;

    $fields = $doc['fields'];

    // 🔄 Resolver referencias a otras colecciones
    $resolverReferencia = function($refPath) use ($accessToken, $projectId) {
        if (!$refPath) return null;

        $ruta = str_starts_with($refPath, 'projects/')
            ? $refPath
            : "projects/{$projectId}/databases/(default)/documents/{$refPath}";

        $url = "https://firestore.googleapis.com/v1/{$ruta}";
        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ];
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers)
            ]
        ];
        $resp = @file_get_contents($url, false, stream_context_create($opts));
        if ($resp === false) return null;
        $data = json_decode($resp, true);

        if (strpos($refPath, 'escalas/') !== false) {
            return $data['fields']['valor']['stringValue'] ?? null;
        } else {
            return $data['fields']['nombre']['stringValue'] ?? null;
        }
    };

    // 🔍 Extraer ID del documento desde referenceValue (ej: "estados/sin-existencia")
    $extraerIdDeReferencia = function($ref) {
        $partes = explode('/', $ref);
        return end($partes);
    };

    return [
        'id' => $id,
        'nombre' => $fields['nombre']['stringValue'] ?? '',
        'descripcion' => $fields['descripcion']['stringValue'] ?? '',
        'precio' => isset($fields['precio']['integerValue'])
            ? (int)$fields['precio']['integerValue']
            : (isset($fields['precio']['doubleValue']) ? (float)$fields['precio']['doubleValue'] : 0),
        'imagenes' => array_map(
            fn($img) => $img['stringValue'],
            $fields['imagenes']['arrayValue']['values'] ?? []
        ),
        'categoria' => isset($fields['categoriasID']['referenceValue'])
            ? $resolverReferencia($fields['categoriasID']['referenceValue'])
            : null,
        'marca' => isset($fields['marcasID']['referenceValue'])
            ? $resolverReferencia($fields['marcasID']['referenceValue'])
            : null,
        'escala' => isset($fields['escalasID']['referenceValue'])
            ? $resolverReferencia($fields['escalasID']['referenceValue'])
            : null,
        'estado' => isset($fields['estadosID']['referenceValue'])
            ? $extraerIdDeReferencia($fields['estadosID']['referenceValue'])
            : null,
        'serie' => isset($fields['seriesID']['referenceValue'])
            ? $resolverReferencia($fields['seriesID']['referenceValue'])
            : null,
        'fecha_lanzamiento' => $fields['fecha_lanzamiento']['timestampValue'] ?? null,
        'fecha_subida' => $fields['fecha_subida']['timestampValue'] ?? null,
        'slug' => $fields['slug']['stringValue'] ?? null
    ];
}

// Obtener todas las categorías
function obtenerCategorias() {
    return obtenerDocumentosDesdeColeccion('categorias', 'nombre');
}

// Obtener todas las marcas
function obtenerMarcas() {
    return obtenerDocumentosDesdeColeccion('marcas', 'nombre');
}

// Obtener todas las series
function obtenerSeries() {
    return obtenerDocumentosDesdeColeccion('series', 'nombre');
}

// Obtener todas las escalas
function obtenerEscalas() {
    return obtenerDocumentosDesdeColeccion('escalas', 'valor');
}

// Función reutilizable base
function obtenerDocumentosDesdeColeccion($coleccion, $campoNombre) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$coleccion}";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers)
        ]
    ];

    $response = @file_get_contents($url, false, stream_context_create($opts));
    if ($response === false) return [];

    $data = json_decode($response, true);
    if (!isset($data['documents'])) return [];

    $resultado = [];
    foreach ($data['documents'] as $doc) {
        $id = basename($doc['name']);
        $nombre = $doc['fields'][$campoNombre]['stringValue'] ?? '';
        $resultado[] = ['id' => $id, 'nombre' => $nombre];
    }

    return $resultado;
}