const mysql = require('mysql2/promise');


const connection = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    port: '3307',
    database: 'carritos_BD'
});

// async function crearCarritoVacio(userID) {
//     const result = await connection.query('INSERT INTO carritos VALUES (null, ?, [], ?, ?, ?)', [userID]);
//     return result;
// }

async function createCartIfNotExists(username) {
    // Verificar la existencia del usuario a través de la API externa
    try {
        await axios.get(`http://localhost:3001/usuarios/${username}`);
    } catch (error) {
        throw new Error('El usuario no existe o no se pudo verificar la existencia del usuario.');
    }

    // Verificar si el carrito ya existe para el usuario
    const [existingCart] = await db.query('SELECT id FROM carritos WHERE usuario_id = ?', [username]);

    if (existingCart.length === 0) {
        // Crear un nuevo carrito vacío
        const [result] = await db.query('INSERT INTO carritos (usuario_id, subtotal, precioEnvio, total) VALUES (?, 0, 0, 0)', [username]);
        return result.insertId; // Retornar el ID del carrito creado
    }

    return existingCart[0].id; // Retornar el ID del carrito existente
}

async function traerCarrito(id_carrito) {
    const result = await connection.query('SELECT * FROM carritos WHERE id_carrito = ?', id_carrito);
    return result[0];
}


async function traerCarritos() {
    const result = await connection.query('SELECT * FROM carritos');
    return result[0];
}

async function agregarACarrito(username, product, quantity) {
    // Obtener el precio del producto desde la API
    let productPrice;
    try {
        const productoResponse = await axios.get(`http://localhost:3002/productos/${product.id}`);
        productPrice = productoResponse.data.precio;
    } catch (error) {
        throw new Error('No se pudo obtener el precio del producto.');
    }

    // Verificar si el carrito del usuario ya existe
    const existingCart = await db.query('SELECT * FROM carritos WHERE usuario_id = ?', [username]);

    let carritoId;
    if (existingCart.length) {
        // Obtener el ID del carrito existente
        carritoId = existingCart[0].id;
    } else {
        // Crear un nuevo carrito si no existe
        const result = await db.query('INSERT INTO carritos (subtotal, precioEnvio, total, usuario_id) VALUES (0, 0, 0, ?)', [username]);
        carritoId = result.insertId;
    }

    // Verificar si el producto ya está en el carrito
    const existingProduct = await db.query('SELECT * FROM items_carrito WHERE carrito_id = ? AND producto_id = ?', [carritoId, product.id]);

    if (existingProduct.length) {
        // Si existe, actualizar la cantidad
        await db.query('UPDATE items_carrito SET cantidad = cantidad + ? WHERE carrito_id = ? AND producto_id = ?', [quantity, carritoId, product.id]);
    } else {
        // Si no existe, insertar el nuevo producto en el carrito
        await db.query('INSERT INTO items_carrito (carrito_id, producto_id, precio, cantidad) VALUES (?, ?, ?, ?)', [carritoId, product.id, productPrice, quantity]);
    }

    // Opcionalmente, puedes retornar un mensaje de éxito o el carrito actualizado
    return { message: 'Producto añadido al carrito' };
}

async function guardarCarrito(carrito) {
    const { subtotal, precioEnvio, total, usuario_id, items } = carrito;

    // Insertar el carrito en la base de datos
    const result = await connection.query('INSERT INTO carritos (subtotal, precioEnvio, total, usuario_id) VALUES (?, ?, ?, ?)', [subtotal, precioEnvio, total, usuario_id]);

    // Obtener el ID del carrito recién creado
    const carritoId = result.insertId;

    // Procesar cada item en el carrito
    for (const item of items) {
        const { producto_id, cantidad } = item;

        // Obtener información del producto desde la API
        const productoResponse = await axios.get(`http://localhost:3002/productos/${producto_id}`);
        const producto = productoResponse.data;

        // Obtener el precio del producto desde la API
        const precio = producto.precio;

        // Insertar el item en la base de datos
        await connection.query('INSERT INTO items_carrito (carrito_id, producto_id, precio, cantidad) VALUES (?, ?, ?, ?)', 
                               [carritoId, producto_id, precio, cantidad]);
    }

    return result;
}

async function agregarACarrito(username, product, quantity) {
    // Obtener el precio del producto desde la API
    let productPrice;
    try {
        const productoResponse = await axios.get(`http://localhost:3002/productos/${product.id}`);
        productPrice = productoResponse.data.precio;
    } catch (error) {
        throw new Error('No se pudo obtener el precio del producto.');
    }

    // Verificar si el carrito del usuario ya existe
    const existingCart = await db.query('SELECT * FROM carritos WHERE usuario_id = ?', [username]);

    let carritoId;
    if (existingCart.length) {
        // Obtener el ID del carrito existente
        carritoId = existingCart[0].id;
    } else {
        // Crear un nuevo carrito si no existe
        const result = await db.query('INSERT INTO carritos (subtotal, precioEnvio, total, usuario_id) VALUES (0, 0, 0, ?)', [username]);
        carritoId = result.insertId;
    }

    // Verificar si el producto ya está en el carrito
    const existingProduct = await db.query('SELECT * FROM items_carrito WHERE carrito_id = ? AND producto_id = ?', [carritoId, product.id]);

    if (existingProduct.length) {
        // Si existe, actualizar la cantidad
        await db.query('UPDATE items_carrito SET cantidad = cantidad + ? WHERE carrito_id = ? AND producto_id = ?', [quantity, carritoId, product.id]);
    } else {
        // Si no existe, insertar el nuevo producto en el carrito
        await db.query('INSERT INTO items_carrito (carrito_id, producto_id, precio, cantidad) VALUES (?, ?, ?, ?)', [carritoId, product.id, productPrice, quantity]);
    }

    // Opcionalmente, puedes retornar un mensaje de éxito o el carrito actualizado
    return { message: 'Producto añadido al carrito' };
}

async function removeFromCart(username, productId) {
    // Obtener el carrito del usuario
    const existingCart = await db.query('SELECT * FROM carritos WHERE usuario_id = ?', [username]);

    if (existingCart.length === 0) {
        throw new Error('El carrito del usuario no existe.');
    }

    const carritoId = existingCart[0].id;

    // Eliminar el producto del carrito del usuario
    await db.query('DELETE FROM items_carrito WHERE carrito_id = ? AND producto_id = ?', [carritoId, productId]);

    // Opcional: Actualizar el subtotal, precio de envío y total del carrito si es necesario

    // Obtener el carrito actualizado
    const updatedCart = await getCartByUsername(username);
    
    return updatedCart;
}

async function crearFactura(username, cart) {
    // Calcular el subtotal
    const subtotal = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
    const precioEnvio = obtenerPrecioEnvio(cart); // Suponiendo que tienes una función para calcular el costo de envío
    const total = subtotal + precioEnvio;

    // Obtener la información del usuario desde la API
    let user;
    try {
        const userResponse = await axios.get(`http://localhost:3001/usuarios/${username}`);
        user = userResponse.data;
    } catch (error) {
        throw new Error('No se pudo obtener la información del usuario.');
    }

    // Crear la factura en la base de datos
    const facturaResult = await db.query('INSERT INTO factura (user_id, email, nombre, ciudad, direccion, documento_identidad, subtotal, precio_envio, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
        [username, user.email, user.nombre, user.ciudad, user.direccion, user.documento_identidad, subtotal, precioEnvio, total]);

    // Obtener el ID de la factura creada
    const facturaId = facturaResult.insertId;

    // Insertar los productos en la tabla de factura_items
    for (const item of cart) {
        await db.query('INSERT INTO factura_items (factura_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)', 
            [facturaId, item.product_id, item.name, item.price, item.quantity]);
    }

    // Vaciar el carrito del usuario y eliminar los items asociados
    await db.query('DELETE FROM items_carrito WHERE carrito_id IN (SELECT id FROM carritos WHERE usuario_id = ?)', [username]);
    await db.query('DELETE FROM carritos WHERE usuario_id = ?', [username]);

    return { message: 'Factura creada y carrito vaciado correctamente', facturaId };
}
module.exports = {
    crearFactura,
    traerCarrito,
    traerCarritos,
    removeFromCart,
    agregarACarrito,
    guardarCarrito,
    createCartIfNotExists
};