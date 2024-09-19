<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ingresar.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];

    // Llamar a la API de carritos para agregar el producto
    $username = $_SESSION['username'];
    $urlCarrito = "http://localhost:3003/carrito/add"; // URL de la API de carritos

    // Datos a enviar
    $data = [
        'username' => $username,
        'producto_id' => $producto_id,
        'cantidad' => $cantidad
    ];

    // Configuración de la solicitud POST
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($urlCarrito, false, $context);

    // Redirigir de nuevo a la página del usuario después de agregar al carrito
    header('Location: usuario.php');
}
