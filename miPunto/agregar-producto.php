<?php
// crear-producto.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria = $_POST['category'];
    $nombre = $_POST['name'];
    $stock = $_POST['stock'];
    $precio = $_POST['price'];

    // Datos del nuevo producto
    $data = array(
        "product_category" => $categoria,
        "product_name" => $nombre,
        "product_stock" => $stock,
        "unit_price_cop" => $precio
    );

    // Llamada a la API para crear un nuevo producto
    $productos_url = "http://localhost:3002/productos";
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($productos_url, false, $context);

    if ($result) {
        header("Location: admin-productos.php");
    } else {
        echo "Error al añadir el producto.";
    }
}
?>
