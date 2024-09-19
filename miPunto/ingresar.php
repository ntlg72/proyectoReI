<?php
// Encabezados para asegurarse de que PHP siempre devuelva JSON
header('Content-Type: application/json');

// Configura la URL de tu API
$apiUrl = 'http://localhost:3001/login';

// Lee los datos JSON enviados desde el formulario
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['message' => 'Solicitud inválida. No se recibieron datos JSON.']);
    exit;
}

$username = $data['username'];
$password = $data['password'];

// Crear el array de datos para enviar a la API
$postData = [
    'username' => $username,
    'password' => $password
];

// Inicializa cURL
$ch = curl_init($apiUrl);

// Configura cURL para hacer una solicitud POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

// Ejecuta la solicitud
$response = curl_exec($ch);

// Manejar errores de cURL
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['message' => 'Error en la solicitud: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Cierra cURL
curl_close($ch);

// Decodifica la respuesta de la API
$result = json_decode($response, true);

// Verifica si la respuesta de la API es válida
if (!$result) {
    http_response_code(502);
    echo json_encode(['message' => 'Respuesta inválida de la API.']);
    exit;
}

// Verifica si el username es 'admin'
if ($username === 'admin') {
    // Devuelve una señal en la respuesta JSON para que el frontend pueda manejar la redirección
    http_response_code(200);
    echo json_encode(['message' => 'Login exitoso', 'redirect' => 'admin.php']);
} elseif (isset($result['cartId'])) {
    // Enviar una respuesta con éxito (código 200)
    http_response_code(200);
    echo json_encode(['message' => 'Login exitoso', 'cartId' => $result['cartId']]);
} else {
    // Si hubo un error o credenciales inválidas
    http_response_code(401);
    echo json_encode(['message' => $result['message'] ?? 'Credenciales inválidas']);
}
?>
