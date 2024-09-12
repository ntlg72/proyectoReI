const { Router } = require('express');
const router = Router();
const usuariosModel = require('../models/usuariosModel');


router.get('/usuarios/:usuario', async (req, res) => {
    const usuario = req.params.usuario;
    var result;
    result = await usuariosModel.traerUsuario(usuario) ;
    res.json(result[0]);
});


router.get('/usuarios/:usuario/:password', async (req, res) => {
    const usuario = req.params.usuario;
    const password = req.params.password;
    var result;
    result = await usuariosModel.validarUsuario(usuario, password) ;
    res.json(result);
});

module.exports = router;