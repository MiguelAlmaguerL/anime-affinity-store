<?php
// Obtener token de acceso JWT
$keyFilePath = __DIR__ . '/../firebase/affinityanimestore-firebase-adminsdk-fbsvc-7a1a2b791b.json';
$projectId = 'affinityanimestore';
$accessToken = getAccessToken($keyFilePath);

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

/*function obtenerProductosPorEstado($accessToken, $projectId, $estado, $limite = 4) {
    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";

    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'productos']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'estado'],
                    'op' => 'EQUAL',
                    'value' => ['stringValue' => $estado]
                ]
            ],
            'orderBy' => [[
                'field' => ['fieldPath' => 'fecha_subida'],
                'direction' => 'DESCENDING'
            ]],
            'limit' => $limite
        ]
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Authorization: Bearer $accessToken\r\nContent-Type: application/json",
            'content' => json_encode($query)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}*/

// Función para productos recientes (estado: Disponible o todos)
function obtenerProductosRecientes($limite = 8) {
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
            'nombre' => $fields['nombre']['stringValue'] ?? '',
            'precio' => isset($fields['precio']['integerValue']) ? (int)$fields['precio']['integerValue'] : 0,
            'imagenes' => array_map(
                fn($img) => $img['stringValue'],
                $fields['imagenes']['arrayValue']['values'] ?? []
            )
        ];
    }

    return $productos;
}

//NUEVA FUNCIÓN: Productos en Preventa
/*function obtenerProductosPreventa($limite = 4) {
    global $accessToken, $projectId;

    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $body = json_encode([
        "structuredQuery" => [
            "from" => [["collectionId" => "productos"]],
            "where" => [
                "fieldFilter" => [
                    "field" => ["fieldPath" => "estado"],
                    "op" => "EQUAL",
                    "value" => ["stringValue" => "Preventa"]
                ]
            ],
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
            'nombre' => $fields['nombre']['stringValue'] ?? '',
            'precio' => isset($fields['precio']['integerValue']) ? (int)$fields['precio']['integerValue'] : 0,
            'imagenes' => array_map(
                fn($img) => $img['stringValue'],
                $fields['imagenes']['arrayValue']['values'] ?? []
            )
        ];
    }

    return $productos;
}*/