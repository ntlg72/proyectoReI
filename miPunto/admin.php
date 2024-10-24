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
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            color: #212529;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        .card-header {
            background-color: #007BFF;
            color: white;
            font-size: 18px;
        }
        .card-body {
            background-color: #ffffff;
        }
        .btn-admin {
            background-color: #007BFF;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-admin:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        h1 {
            color: #007BFF;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Panel de Administración</h1>
        <div class="btn-container text-center">
            <button class="btn btn-admin" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">Añadir Producto</button>
            <a href="admin-facturas.php" class="btn btn-admin">Ver Facturas</a>
            <a href="admin-estadisticas.php" class="btn btn-admin"> Ver Estadísticas</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <!-- Barra de búsqueda por ID -->
        <div class="mb-3">
            <form method="post" action="">
                <label for="searchProductId" class="form-label">Buscar Producto por ID</label>
                <input type="text" class="form-control" id="searchProductId" name="searchProductId" placeholder="Ingresa el ID del producto" required>
                <button type="submit" class="btn btn-primary mt-2">Buscar</button>
            </form>
        </div>           
        <?php
        if (isset($_POST['searchProductId'])) {
            $product_id = htmlspecialchars($_POST['searchProductId']);
            
            // Llamar API para obtener producto
            $productos_url = "http://localhost:3002/productos/$product_id";
            $curl = curl_init($productos_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Decodificar la respuesta JSON
            $producto = json_decode($response, true);

            // Verifica si el producto fue encontrado
            if ($http_status == 200 && $producto) {
                // Mostrar los detalles del producto y agregar botones de Eliminar y Actualizar
                echo '<div id="searchResult" class="card mb-3">';
                echo '  <div class="card-header">Resultado de la Búsqueda</div>';
                echo '  <div class="card-body">';
                echo '      <p><strong>ID:</strong> ' . htmlspecialchars($producto['product_id']) . '</p>';
                echo '      <p><strong>Categoría:</strong> ' . htmlspecialchars($producto['product_category']) . '</p>';
                echo '      <p><strong>Nombre:</strong> ' . htmlspecialchars($producto['product_name']) . '</p>';
                echo '      <p><strong>Stock:</strong> ' . htmlspecialchars($producto['product_stock']) . '</p>';
                echo '      <p><strong>Precio Unitario (COP):</strong> ' . htmlspecialchars($producto['unit_price_cop']) . '</p>';
                echo '      <div class="d-flex justify-content-between">';
                echo '      <td>
                                <a href="confirmar-eliminar.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-danger">Eliminar</a>
                                <a href="editar-producto.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-warning">Editar</a>
                            </td>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            } else {
                // Producto no encontrado
                echo '<div id="searchResult" class="card mb-3">';
                echo '  <div class="card-header">Resultado de la Búsqueda</div>';
                echo '  <div class="card-body">';
                echo '      <p>Producto no encontrado.</p>';
                echo '  </div>';
                echo '</div>';
            }
        }
        ?>
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
            echo '<a href="admin.php" class="btn btn-secondary">Ver todos los productos</a>';
            echo '</div>';

            // Filtrar productos por categoría seleccionada
            if (isset($_POST['selectedCategory'])) {
                $selected_category = htmlspecialchars($_POST['selectedCategory']);
                $productos_url = "http://localhost:3002/productos/categoria/$selected_category"; 
                $response = file_get_contents($productos_url);
                $productos = json_decode($response, true);

                if ($productos) {
                    echo '<h2>Productos en la categoría: ' . htmlspecialchars($selected_category) . '</h2>';
                    echo '<table class="table table-striped">';
                    echo '<thead><tr><th>ID</th><th>Categoría</th><th>Nombre</th><th>Stock</th><th>Precio Unitario (COP)</th><th>Acciones</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($productos as $producto) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($producto['product_id']) . '</td>';
                        echo '<td>' . htmlspecialchars($producto['product_category']) . '</td>';
                        echo '<td>' . htmlspecialchars($producto['product_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($producto['product_stock']) . '</td>';
                        echo '<td>' . htmlspecialchars($producto['unit_price_cop']) . '</td>';
                        echo '<td>
                                <a href="confirmar-eliminar.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-danger">Eliminar</a>
                                <a href="editar-producto.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-warning">Editar</a>
                            </td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No se encontraron productos en esta categoría.</p>';
                }
            } else {
            ?>
            <!-- Sección de Productos -->
            <div class="card mb-3">
                <div class="card-header">Productos Disponibles</div>
                <div class="card-body">
                    <?php
                    // URL del servicio API para obtener los productos
                    $productos_url = "http://localhost:3002/productos/desc";
                    $curl = curl_init($productos_url);

                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    // Decodificar la respuesta JSON en un arreglo
                    $productos = json_decode($response, true);

                    if (is_array($productos) && count($productos) > 0) {
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>ID</th><th>Categoría</th><th>Nombre</th><th>Stock</th><th>Precio Unitario (COP)</th><th>Acciones</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($productos as $producto) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($producto['product_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($producto['product_category']) . '</td>';
                            echo '<td>' . htmlspecialchars($producto['product_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($producto['product_stock']) . '</td>';
                            echo '<td>' . htmlspecialchars($producto['unit_price_cop']) . '</td>';
                            echo '<td>
                                    <a href="confirmar-eliminar.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-danger">Eliminar</a>
                                    <a href="editar-producto.php?id=' . htmlspecialchars($producto['product_id']) . '" class="btn btn-sm btn-warning">Editar</a>
                                </td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>No se encontraron productos.</p>';
                    }
                    ?>
                </div>
            </div>
            <?php } ?>


</body>
</html>
