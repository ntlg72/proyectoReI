const { Router } = require('express');
const router = Router();
const usuariosModel = require('../models/usuariosModel');


router.get('/usuarios/:username', async (req, res) => {
    const username = req.params.username;
    var result;
    result = await usuariosModel.traerUsuario(username) ;
    res.json(result[0]);
});


router.get('/usuarios/:username/:password', async (req, res) => {
    const username = req.params.username;
    const password = req.params.password;
    var result;
    result = await usuariosModel.validarUsuario(username, password) ;
    res.json(result);
});

module.exports = router;