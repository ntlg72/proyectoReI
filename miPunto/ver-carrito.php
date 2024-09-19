<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ingresar.php');
    exit();
}

$username = $_SESSION['username'];

// Reemplazar ':username' con el nombre de usuario en la URL para obtener los items del carrito
$urlCarritoItems = "http://localhost:3003/carrito/items/" . urlencode($username);

// Configurar opciones de contexto para manejo de errores
$options = [
    'http' => [
        'method'  => 'GET',
        'header'  => 'Content-Type: application/json',
        'timeout' => 30, // Tiempo de espera en segundos
    ]
];
$context = stream_context_create($options);

// Llamar a la API de carritos para obtener los productos del carrito
$responseItems = @file_get_contents($urlCarritoItems, false, $context);

// Manejar errores al obtener el carrito
if ($responseItems === false) {
    $error = error_get_last();
    die('Error al obtener el carrito: ' . $error['message']);
}

// Decodificar la respuesta JSON
$carritoItems = json_decode($responseItems, true);

// Manejar errores al decodificar JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error al decodificar JSON: ' . json_last_error_msg());
}

// Obtener carrito_id de los items
if (isset($carritoItems['items']) && !empty($carritoItems['items'])) {
    $carrito_id = $carritoItems['items'][0]['carrito_id']; // Asumimos que todos los items pertenecen al mismo carrito
} else {
    die('No se encontró ningún producto en el carrito.');
}

// Ahora llama a la API de carritos para obtener el subtotal, precioEnvio y total utilizando el carrito_id
$urlCarritoDetails = "http://localhost:3003/carritos/" . urlencode($carrito_id);
$responseDetails = @file_get_contents($urlCarritoDetails, false, $context);

if ($responseDetails === false) {
    $error = error_get_last();
    die('Error al obtener detalles del carrito: ' . $error['message']);
}

// Decodificar la respuesta JSON
$carritoDetails = json_decode($responseDetails, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error al decodificar JSON de detalles: ' . json_last_error_msg());
}

// Combinar los resultados de los items y los detalles
$carrito = [
    'items' => $carritoItems['items'],
    'subtotal' => $carritoDetails['subtotal'],
    'precioEnvio' => $carritoDetails['precioEnvio'],
    'total' => $carritoDetails['total'],
];

// Función para eliminar un producto del carrito
if (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
    $product_id = $_POST['product_id'];
    
    $urlEliminar = "http://localhost:3003/carrito/eliminar";
    
    $data = [
        'username' => $username,
        'productId' => $product_id
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'DELETE',
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($urlEliminar, false, $context);
    
    if ($result === false) {
        $error = error_get_last();
        echo 'Error al eliminar el producto del carrito: ' . $error['message'];
    } else {
        header('Location: ver-carrito.php');
        exit();
    }
}

// Función para vaciar el carrito
if (isset($_POST['action']) && $_POST['action'] === 'vaciar') {
    $urlVaciar = "http://localhost:3003/carrito/vaciar";
    
    $data = [
        'cartId' => $carrito_id
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'DELETE',
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($urlVaciar, false, $context);
    
    if ($result === false) {
        echo 'Error al vaciar el carrito';
    } else {
        header('Location: ver-carrito.php');
        exit();
    }
}

// Función para modificar la cantidad de un producto en el carrito
if (isset($_POST['action']) && $_POST['action'] === 'modificar') {
    $product_id = $_POST['product_id'];
    $cantidad = $_POST['cantidad'];
    
    if (!is_numeric($cantidad) || $cantidad <= 0) {
        echo 'Cantidad inválida';
        exit();
    }
    
    $urlModificar = "http://localhost:3003/carrito/actualizar";
    
    $data = [
        'username' => $username,
        'product_id' => $product_id,
        'quantity' => $cantidad
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($urlModificar, false, $context);
    
    if ($result === false) {
        echo 'Error al modificar la cantidad del producto';
    } else {
        header('Location: ver-carrito.php');
        exit();
    }
}

// Función para crear la factura
if (isset($_POST['action']) && $_POST['action'] === 'facturar') {
    $urlFacturar = "http://localhost:3003/factura/crear";
    
    $data = [
        'username' => $username
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true,
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($urlFacturar, false, $context);
    
    if ($result === false) {
        echo 'Error al crear la factura';
    } else {
        echo 'Factura creada exitosamente';
        // Puedes redirigir o mostrar un enlace para descargar la factura si es necesario
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carrito</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de agregar tu hoja de estilos -->
</head>
<body>
    <h1>Carrito de Compras</h1>
    
    <h2>Productos en el carrito</h2>
    <div class="carrito">
        <?php if (!empty($carrito['items'])): ?>
            <form action="ver-carrito.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito['items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['producto_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['precio']); ?> COP</td>
                                <td>
                                    <input type="number" name="cantidad" value="<?php echo htmlspecialchars($item['cantidad']); ?>" min="1" max="1000">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['producto_id']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($item['cantidad'] * $item['precio']); ?> COP</td>
                                <td>
                                    <button type="submit" name="action" value="modificar">Modificar</button>
                                    <button type="submit" name="action" value="eliminar">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>

            <!-- Mostrar subtotal, precio de envío y total -->
            <div class="totales">
                <h3>Totales</h3>
                <p>Subtotal: <?php echo isset($carrito['subtotal']) ? htmlspecialchars($carrito['subtotal']) : '0'; ?> COP</p>
                <p>Precio de Envío: <?php echo isset($carrito['precioEnvio']) ? htmlspecialchars($carrito['precioEnvio']) : '0'; ?> COP</p>
                <p>Total: <?php echo isset($carrito['total']) ? htmlspecialchars($carrito['total']) : '0'; ?> COP</p>
            </div>

            <form action="ver-carrito.php" method="POST">
                <button type="submit" name="action" value="vaciar">Vaciar Carrito</button>
                <button type="submit" name="action" value="facturar">Crear Factura</button>
            </form>

        <?php else: ?>
            <p>No hay productos en el carrito.</p>
        <?php endif; ?>
    </div>


    <style>
        /* Aquí puedes añadir tu CSS para estilos básicos */
        .carrito {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            color: white;
            background-color: #3498db;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</body>
</html>
