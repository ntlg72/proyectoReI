const express = require('express');
const carritosController = require('./controllers/carritosController');
const morgan = require('morgan');
const app = express();
const cors = require('cors');
app.use(morgan('dev'));
app.use(express.json());

app.use(cors());
app.use(carritosController);


app.listen(3003, () => {
    console.log('Microservicio Carritos ejecutandose en el puerto 3003');
});