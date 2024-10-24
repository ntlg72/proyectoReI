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

// Obtener categorías desde la API
$urlCategorias = "http://localhost:3002/categorias"; // URL de tu API de categorías
$responseCategorias = file_get_contents($urlCategorias);

if ($responseCategorias === false) {
    die('Error al obtener las categorías');
}

$categorias = json_decode($responseCategorias, true);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reset básico para asegurar consistencia en navegadores */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        h2 {
            font-size: 2rem;
            color: #16a085;
            text-align: center;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 1.2rem;
            color: #34495e;
            margin-bottom: 10px;
        }

        /* Botones de navegación en la parte superior derecha */
        .nav-buttons {
            position: absolute;
            top: 20px; /* Ajusta la distancia desde la parte superior */
            right: 20px; /* Ajusta la distancia desde la derecha */
            display: flex;
            gap: 10px; /* Espaciado entre botones */
        }

        /* Botón Ver Carrito */
        .btn-carrito {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .btn-carrito:hover {
            background-color: #2980b9;
        }

        /* Botón Cerrar Sesión */
        .btn-logout {
            padding: 10px 20px;
            background-color: #e74c3c; /* Color de fondo para cerrar sesión */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .btn-logout:hover {
            background-color: #c0392b; /* Color de fondo al pasar el mouse */
        }

        /* Contenedor de productos */
        .productos {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .producto {
            background-color: white;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }

        .producto:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .producto p {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .producto form {
            margin-top: 10px;
        }

        .producto label {
            font-size: 0.9rem;
            color: #2c3e50;
        }

        .producto input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .producto button {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #16a085;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .producto button:hover {
            background-color: #1abc9c;
        }
    </style>
</head>
<body>

    <h1>Bienvenido, <?php echo htmlspecialchars($username); ?></h1>

    <div class="nav-buttons">
        <a href="ver-carrito.php" class="btn btn-carrito">Ver Carrito</a>
        <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
    </div>

    <div class="mb-3">
        <form method="post" action="">
            <div class="input-group">
                <select class="form-select" id="searchProductCategory" name="selectedCategory" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['product_category']); ?>"><?php echo htmlspecialchars($categoria['product_category']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>

    <?php
    // Botón para volver a ver todos los productos
    echo '<div class="mb-3">';
    echo '<a href="usuario.php" class="btn btn-secondary">Ver todos los productos</a>';
    echo '</div>';

    // Filtrar productos por categoría seleccionada
    if (isset($_POST['selectedCategory'])) {
        $selected_category = htmlspecialchars($_POST['selectedCategory']);
        $productos_url = "http://localhost:3002/productos/categoria/$selected_category"; 
        $response = file_get_contents($productos_url);
        $productos = json_decode($response, true);

        if ($productos) {
            echo '<h2>Productos en la categoría: ' . htmlspecialchars($selected_category) . '</h2>';
            echo '<div class="productos">';
            foreach ($productos as $producto) {
                echo '<div class="producto">';
                echo '<h3>' . htmlspecialchars($producto['product_name']) . '</h3>';
                echo '<p>Precio: ' . htmlspecialchars($producto['unit_price_cop']) . ' COP</p>';
                echo '<form action="agregar-carrito.php" method="POST">';
                echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($producto['product_id']) . '">';
                echo '<label for="cantidad_' . htmlspecialchars($producto['product_id']) . '">Cantidad:</label>';
                echo '<input type="number" id="cantidad_' . htmlspecialchars($producto['product_id']) . '" name="cantidad" value="1" min="1" max="1000">';
                echo '<button type="submit">Agregar al carrito</button>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No se encontraron productos en esta categoría.</p>';
        }
    } else {
        echo '<h2>Productos disponibles</h2>';
        echo '<div class="productos">';
        foreach ($productos as $producto) {
            echo '<div class="producto">';
            echo '<h3>' . htmlspecialchars($producto['product_name']) . '</h3>';
            echo '<p>Precio: ' . htmlspecialchars($producto['unit_price_cop']) . ' COP</p>';
            echo '<form action="agregar-carrito.php" method="POST">';
            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($producto['product_id']) . '">';
            echo '<label for="cantidad_' . htmlspecialchars($producto['product_id']) . '">Cantidad:</label>';
            echo '<input type="number" id="cantidad_' . htmlspecialchars($producto['product_id']) . '" name="cantidad" value="1" min="1" max="1000">';
            echo '<button type="submit">Agregar al carrito</button>';
            echo '</form>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>

</body>
</html>