const mysql = require('mysql2/promise');

// //const connection = mysql.createPool({
//     host: 'localhost',
//     user: 'root',
//     password: '',
//     port:'3307',
//     database: 'usuariosDB'
// });
// //


async function traerUsuario(usuario) {
    const result = await connection.query('SELECT * FROM usuarios WHERE usuario = ?', usuario);
    return result[0];
}


async function validarUsuario(usuario, password) {
    const result = await connection.query('SELECT * FROM usuarios WHERE usuario = ? AND password = ?', [usuario, password]);
    return result[0];
}


module.exports = {
    validarUsuario, traerUsuario
};

