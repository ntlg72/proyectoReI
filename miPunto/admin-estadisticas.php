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
</head>
<body>
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

