const express = require('express');
const router = express.Router();
const axios = require('axios');
const carritosModel = require('../models/carritosModel');


router.get('/carritos/:id_carrito', async (req, res) => {
    const id_carrito = req.params.id_carrito;
    var result;
    result = await carritosModel.traerCarrito(id_carrito);
    res.json(result[0]);
});


router.get('/carritos', async (req, res) => {
    var result;
    result = await carritosModel.traerCarritos();
    res.json(result);
});

// router.get('/carritovacio', async (req, res) => {
//     try {
//         // Obtener el ID del usuario desde el microservicio de productos
//         const userId = await axios.get('http://localhost:3001/usuarios/:username'); // Reemplazar con la URL correcta


//         // Verificar si existe un carrito para este usuario
//         const carritoExistente = await getCartByUser(userId.data.username);


//         if (!carritoExistente) {
//             // Crear un carrito vacío
//             const nuevoCarrito = await crearCarritoVacio(userId.data.id);
//             res.json(nuevoCarrito);
//         } else {
//             res.json({ message: 'Carrito ya existe' });
//         }
//     } catch (error) {
//         console.error(error);
//         res.status(500).json({ error: 'Error al crear el carrito' });
//     }
// });


const obtenerPrecioEnvio = (ciudad) => {
    const preciosEnvio = {
        'Bogotá': 5000,
        'Medellín': 6000,
        'Cali': 7000,
        'Cartagena': 8000,
    };
    return preciosEnvio[ciudad] || 10000; // Precio por defecto si la ciudad no está en el JSON
};




// // Ruta para añadir productos al carrito
// router.post('/anadiracarrito', async (req, res) => {
//     const { username, product, quantity } = req.body;

//     // Validación básica
//     if (!username || !product || !quantity) {
//         return res.status(400).json({ message: 'Faltan datos requeridos.' });
//     }

//     try {
//         // Llamar a la función para añadir el producto al carrito
//         const result = await addToCart(username, product, quantity);
//         res.status(200).json(result);
//     } catch (error) {
//         console.error('Error al añadir producto al carrito:', error);
//         res.status(500).json({ message: 'Error al añadir producto al carrito.' });
//     }
// });

// router.delete('/remove-from-cart', async (req, res) => {
//     const { username, productId } = req.body;

//     // Validación básica
//     if (!username || !productId) {
//         return res.status(400).json({ message: 'Faltan datos requeridos.' });
//     }

//     try {
//         // Llamar a la función para eliminar el producto del carrito
//         const updatedCart = await removeFromCart(username, productId);
//         res.status(200).json(updatedCart);
//     } catch (error) {
//         console.error('Error al eliminar producto del carrito:', error);
//         res.status(500).json({ message: 'Error al eliminar producto del carrito.' });
//     }
// });

// router.post('/crearfactura', async (req, res) => {
//     const { username, cart } = req.body;

//     // Validación básica
//     if (!username || !cart || !Array.isArray(cart)) {
//         return res.status(400).json({ message: 'Faltan datos requeridos o el carrito no es válido.' });
//     }

//     try {
//         // Llamar a la función para crear la orden
//         const order = await createFactura(username, cart);
//         res.status(200).json(order);
//     } catch (error) {
//         console.error('Error al crear la orden:', error);
//         res.status(500).json({ message: 'Error al crear la orden.' });
//     }
// });
// Ruta para crear un carrito vacío (puede ser usado por otras APIs o directamente si se necesita)
// Ruta para crear el carrito
router.post('/create', async (req, res) => {
    const { username } = req.body;

    // Verificar si el username es null o undefined
    if (!username) {
        return res.status(400).json({ message: 'El username es obligatorio.' });
    }

    try {
        const cartId = await carritosModel.createCartIfNotExists(username);
        res.status(200).json({ message: 'Carrito creado', cartId });
    } catch (error) {
        console.error('Error al crear el carrito:', error.message);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});


router.post('/carrito', async (req, res) => {
    try {
        const carrito = req.body;

        // Validar datos del carrito aquí, si es necesario

        const result = await guardarCarrito(carrito);
        res.status(201).json({ message: 'Carrito guardado exitosamente', carritoId: result.insertId });
    } catch (error) {
        console.error('Error al guardar el carrito:', error);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});

router.post('/carrito/add', async (req, res) => {
    try {
        const { username, product, quantity } = req.body;

        if (!username || !product || !quantity) {
            return res.status(400).json({ message: 'Faltan datos necesarios' });
        }

        const result = await carritosModel.agregarACarrito(username, product, quantity);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al agregar el producto al carrito:', error);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});


router.delete('/carrito/remove', async (req, res) => {
    try {
        const { username, productId } = req.body;

        if (!username || !productId) {
            return res.status(400).json({ message: 'Faltan datos necesarios' });
        }

        const updatedCart = await removeFromCart(username, productId);
        res.status(200).json(updatedCart);
    } catch (error) {
        console.error('Error al eliminar el producto del carrito:', error);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});
router.post('/factura', async (req, res) => {
    try {
        const { username, cart } = req.body;

        if (!username || !Array.isArray(cart) || cart.length === 0) {
            return res.status(400).json({ message: 'Datos insuficientes: username y cart son requeridos' });
        }

        const result = await crearFactura(username, cart);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al crear la factura:', error);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});

router.post('/factura', async (req, res) => {
    try {
        const { username, cart } = req.body;

        if (!username || !Array.isArray(cart) || cart.length === 0) {
            return res.status(400).json({ message: 'Datos insuficientes: username y cart son requeridos' });
        }

        const result = await crearFactura(username, cart);
        res.status(200).json(result);
    } catch (error) {
        console.error('Error al crear la factura:', error.message);
        res.status(500).json({ message: 'Error interno del servidor' });
    }
});


module.exports = router;
