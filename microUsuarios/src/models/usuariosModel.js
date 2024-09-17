const mysql = require('mysql2/promise');

const connection = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    port:'3307',
    database: 'usuarios_BD'
});



async function traerUsuario(username) {
    const result = await connection.query('SELECT * FROM usuarios WHERE username = ?', username);
    return result[0];
}


async function validarUsuario(username, password) {
    const result = await connection.query('SELECT * FROM usuarios WHERE username = ? AND password = ?', [username, password]);
    return result[0];
}


module.exports = {
    validarUsuario, traerUsuario
};

