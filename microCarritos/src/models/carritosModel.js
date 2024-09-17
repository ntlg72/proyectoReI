const mysql = require('mysql2/promise');


const connection = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    port: '3307',
    database: 'carritos_BD'
});

async function crearCarritoVacio(userID) {
    const result = await connection.query('INSERT INTO carritos VALUES (null, ?, [], ?, ?, ?)', [userID]);
    return result;
}


async function guardarCarrito(carrito) {
    const subtotal = carrito.subtotal;
    const precioEnvio = carrito.precioEnvio;
    const total = carrito.total;
    const usuario_id = carrito.usuario_id

    const result = await connection.query('INSERT INTO carritos VALUES (null, ?, ?, ?, ?)', [subtotal, precioEnvio, total, usuario_id]);
    return result;


}


async function traerCarrito(id_carrito) {
    const result = await connection.query('SELECT * FROM carritos WHERE id_carrito = ?', id_carrito);
    return result[0];
}


async function traerCarritos() {
    const result = await connection.query('SELECT * FROM carritos');
    return result[0];
}

async function addToCart(username, product, quantity) {
    // Verificar si el producto ya está en el carrito
    const existingProduct = await db.query('SELECT * FROM cart WHERE username = ? AND product_id = ?', [username, product.id]);
    
    if (existingProduct.length) {
        // Si existe, actualizar la cantidad
        await db.query('UPDATE cart SET quantity = quantity + ? WHERE username = ? AND product_id = ?', [quantity, username, product.id]);
    } else {
        // Si no existe, insertar el nuevo producto en el carrito
        await db.query('INSERT INTO cart (username, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)', [username, product.id, product.name, product.price, quantity]);
    }

    // Opcionalmente, puedes retornar un mensaje de éxito o el carrito actualizado
    return { message: 'Producto añadido al carrito' };
}

async function removeFromCart(username, productId) {
    // Eliminar el producto del carrito del usuario
    await db.query('DELETE FROM cart WHERE username = ? AND product_id = ?', [username, productId]);

    // Obtener el carrito actualizado
    const updatedCart = await getCartByUsername(username);
    
    return updatedCart;
}

// Función para obtener el carrito del usuario por su nombre de usuario
async function getCartByUsername(username) {
    const result = await db.query('SELECT * FROM cart WHERE username = ?', [username]);
    return result;
}

async function createFactura(username, cart) {
    // Calcular el subtotal
    const subtotal = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
    const precioEnvio = obtenerPrecioEnvio(cart); // Suponiendo que tienes una función para calcular el costo de envío
    const total = subtotal + precioEnvio;
    // Crear la orden en la base de datos
    const facturaResult = await db.query('INSERT INTO orders (user_id, subtotal, precio_envio, total) VALUES (?, ?, ?, ?)', [userId, subtotal, precioEnvio, total]);
    // Obtener el ID de la orden creada
    const facturaId = facturaResult.insertId;

    // Insertar los productos en la tabla de order_items
    for (const item of cart) {
        await db.query('INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)', 
            [orderId, item.product_id, item.name, item.price, item.quantity]);
    }

    // Vaciar el carrito del usuario
    await clearCart(username);

    return orderResult;
}

// Función para vaciar el carrito del usuario
async function clearCart(username) {
    await db.query('DELETE FROM cart WHERE user_id = ?', [username]);
}
// async function traerFactura() {
//     const result = await connection.query('SELECT * FROM carritos WHERE id_carrito = ?', id_carrito);
//     return result[0];
// }

// async function traerFacturas() {
//     const result = await connection.query('SELECT * FROM carritos');
//     return result[0];
// }

// async function crearFactura() {
//     const result = await connection.query('SELECT * FROM carritos');
//     return result[0];
// }

module.exports = {
    crearCarrito,
    traerCarrito,
    traerCarritos
};