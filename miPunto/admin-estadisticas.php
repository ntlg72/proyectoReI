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
        .btn-admin {
            background-color: #5086c1;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-admin:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .form-select {
            border-radius: 8px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <h1 class="text-center">Estadisticas</h1>
    <div class="btn-container text-center mb-3">
            <a href="admin.php" class="btn btn-admin"><i class="fas fa-box"></i> Gestionar Productos</a>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    <canvas id="myChart" style="width: 900px; height: 500px;"></canvas>
    <script type="text/javascript">
        // Función para obtener datos de la API REST
        async function fetchProducts() {
            try {
                const response = await fetch('http://localhost:3003/ProductosMasVendidos'); 
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Función para obtener el nombre del producto por su ID
        async function fetchProductName(productId) {
            try {
                const response = await fetch(`http://localhost:3002/productos/${productId}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const product = await response.json();
                return product.product_name; // Asegúrate de que el campo se llame 'name'
            } catch (error) {
                console.error('Error fetching product name:', error);
            }
        }

        // Crear el gráfico después de obtener los datos
        async function createChart() {
            const products = await fetchProducts();
            if (!products) return; // Si no se obtienen productos, no crear el gráfico

            const labels = [];
            const quantities = [];

            // Obtener nombres de productos en paralelo
            await Promise.all(products.map(async (product) => {
                const name = await fetchProductName(product.product_id);
                labels.push(name);
                quantities.push(parseInt(product.total_quantity));
            }));

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar', // Cambia el tipo a 'horizontalBar' si prefieres
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: quantities,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Productos'
                            }
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Productos Más Vendidos'
                        }
                    }
                }
            });
        }

        // Llama a la función para crear el gráfico
        createChart();
    </script>
</body>
</html>

