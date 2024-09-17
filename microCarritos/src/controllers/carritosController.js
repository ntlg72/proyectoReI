const express = require('express');
const router = express.Router();
const axios = require('axios');
const carritosModel = require('../models/carritosModel');


router.get('/carritos/:id_carrito', async (req, res) => {
    const id = req.params.id;
    var result;
    result = await carritosModel.traerCarrito(id_carrito);
    res.json(result[0]);
});


router.get('/carritos', async (req, res) => {
    var result;
    result = await carritosModel.traerCarritos();
    res.json(result);
});

router.get('/carritovacio', async (req, res) => {
    try {
        // Obtener el ID del usuario desde el microservicio de productos
        const userId = await axios.get('http://localhost:3001/usuarios/:username'); // Reemplazar con la URL correcta


        // Verificar si existe un carrito para este usuario
        const carritoExistente = await getCartByUser(userId.data.username);


        if (!carritoExistente) {
            // Crear un carrito vacío
            const nuevoCarrito = await crearCarritoVacio(userId.data.id);
            res.json(nuevoCarrito);
        } else {
            res.json({ message: 'Carrito ya existe' });
        }
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Error al crear el carrito' });
    }
});


const obtenerPrecioEnvio = (ciudad) => {
    const preciosEnvio = {
        'Bogotá': 5000,
        'Medellín': 6000,
        'Cali': 7000,
        'Cartagena': 8000,
    };
    return preciosEnvio[ciudad] || 10000; // Precio por defecto si la ciudad no está en el JSON
};




// Ruta para añadir productos al carrito
router.post('/anadiracarrito', async (req, res) => {
    const { username, product, quantity } = req.body;

    // Validación básica
    if (!username || !product || !quantity) {
        return res.status(400).json({ message: 'Faltan datos requeridos.' });
    }

    try {
        // Llamar a la función para añadir el producto al carrito
        const result = await addToCart(username, product, quantity);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al añadir producto al carrito:', error);
        res.status(500).json({ message: 'Error al añadir producto al carrito.' });
    }
});

router.delete('/remove-from-cart', async (req, res) => {
    const { username, productId } = req.body;

    // Validación básica
    if (!username || !productId) {
        return res.status(400).json({ message: 'Faltan datos requeridos.' });
    }

    try {
        // Llamar a la función para eliminar el producto del carrito
        const updatedCart = await removeFromCart(username, productId);
        res.status(200).json(updatedCart);
    } catch (error) {
        console.error('Error al eliminar producto del carrito:', error);
        res.status(500).json({ message: 'Error al eliminar producto del carrito.' });
    }
});

router.post('/crearfactura', async (req, res) => {
    const { username, cart } = req.body;

    // Validación básica
    if (!username || !cart || !Array.isArray(cart)) {
        return res.status(400).json({ message: 'Faltan datos requeridos o el carrito no es válido.' });
    }

    try {
        // Llamar a la función para crear la orden
        const order = await createFactura(username, cart);
        res.status(200).json(order);
    } catch (error) {
        console.error('Error al crear la orden:', error);
        res.status(500).json({ message: 'Error al crear la orden.' });
    }
});

module.exports = router;
