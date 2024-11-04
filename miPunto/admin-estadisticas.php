<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Más Vendidos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            color: #212529;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 40px;
        }
        h1 {
            color: #5086c1;
            margin-bottom: 30px;
            font-family: 'Open Sans', sans-serif;
        }
        .card-header {
            background-color: #5086c1;
            color: white;
            font-size: 18px;
        }
        .card-body {
            background-color: #ffffff;
        }
        .btn-admin, .btn-danger {
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-admin {
            background-color: #5086c1;
            color: white;
        }
        .btn-admin:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
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
        .table th, .table td {
            vertical-align: middle;
        }
        .chart-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            margin-bottom: 40px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .chart-title {
            font-size: 18px;
            color: #5086c1;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1 class="text-center">Estadísticas</h1>
    <div class="btn-container text-center mb-3">
        <a href="admin.php" class="btn btn-admin"><i class="fas fa-box"></i> Gestionar Productos</a>
        <a href="ventas_por_ciudad.html" class="btn btn-admin"><i class="fas fa-box"></i> Ventas por ciudad</a>
        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <div class="container">
        <div class="chart-container">
            <h2 class="chart-title text-center">Ventas por Categoría</h2>
            <img src="ventas_por_categoria.png" alt="Ventas por Categoría" class="img-fluid">
        </div>
        <div class="chart-container">
            <h2 class="chart-title text-center">Distribución de Productos por Rango de Precio</h2>
            <img src="producto_rango_precio.png" alt="Distribución de Productos por Rango de Precio" class="img-fluid">
        </div>
        <div class="chart-container">
            <h2 class="chart-title text-center">Distribución de Frecuencia de Facturas Creadas</h2>
            <img src="frecuencia_facturas.png" alt="Distribución de Frecuencia de Facturas" class="img-fluid">
        </div>
        <div class="chart-container">
            <h2 class="chart-title text-center">Top de Productos Más Vendidos</h2>
            <img src="top_productos.png" alt="Top de Productos Más Vendidos" class="img-fluid">
        </div>
    </div>
</body>
</html>
