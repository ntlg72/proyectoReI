const { Router } = require('express');
const router = Router();
const productosModel = require('../models/productosModel');



router.get('/productos', async (req, res) => {
    var result;
    result = await productosModel.traerProductos() ;
    //console.log(result);
    res.json(result);
});


router.get('/productos/:id', async (req, res) => {
    const id = req.params.id;
    var result;
    result = await productosModel.traerProducto(id) ;
    //console.log(result);
    res.json(result[0]);
});


router.post('/productos', async (req, res) => {
    const nombre = req.body.nombre;
    const categoria = req.body.categoria;
    const precio = req.body.precio;
    const inventario = req.body.inventario;
    
    var result = await productosModel.crearProducto(nombre, categoria, precio, inventario);
    res.send("producto creado");
});


router.put('/productos/:id', async (req, res) => {
    //const nombre = req.body.nombre;
    //const precio = req.body.precio;
    const inventario = req.body.inventario;
    const id = req.params.id;
    
    var result = await productosModel.actualizarProducto(id, inventario);
    res.send("inventario actualizado");
});


router.delete('/productos/:id', async (req, res) => {
    const id = req.params.id;
    var result;
    result = await productosModel.borrarProducto(id) ;
    //console.log(result);
    res.send("producto borrado");
});


module.exports = router;