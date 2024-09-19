<?php
// Verifica si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ingresar.php');
    exit();
}

// Obtener el nombre de usuario
$username = $_SESSION['username'];

// Llamar a la API de productos para obtener la lista de productos
$urlProductos = "http://localhost:3002/productos"; // URL de tu API de productos
$response = file_get_contents($urlProductos);

if ($response === false) {
    die('Error al obtener los productos');
}

// Decodificar la respuesta JSON
$productos = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error al decodificar JSON: ' . json_last_error_msg());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de agregar tu hoja de estilos -->
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($username); ?></h1>

    <!-- Mostrar productos -->
    <h2>Productos disponibles</h2>
    <div class="productos">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $producto): ?>
                <div class="producto">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p>Precio: <?php echo htmlspecialchars($producto['precio']); ?> COP</p>
                    <form action="agregar-carrito.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                        <label for="cantidad_<?php echo htmlspecialchars($producto['id']); ?>">Cantidad:</label>
                        <input type="number" id="cantidad_<?php echo htmlspecialchars($producto['id']); ?>" name="cantidad" value="1" min="1" max="1000">
                        <button type="submit">Agregar al carrito</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles.</p>
        <?php endif; ?>
    </div>

    <!-- Botón para ver el carrito -->
    <div>
        <a href="ver_carrito.php" class="btn-carrito">Ver Carrito</a>
    </div>

    <style>
        /* Aquí puedes añadir tu CSS para estilos básicos */
        .productos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .producto {
            border: 1px solid #ccc;
            padding: 15px;
            width: 200px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
        }
        .btn-carrito {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-carrito:hover {
            background-color: #2980b9;
        }
    </style>

</body>
</html>
