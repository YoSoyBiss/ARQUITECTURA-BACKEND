require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');

const app = express();
app.use(cors()); // Permite peticiones del frontend
app.use(express.json()); // Parsear JSON

// Conexión a MongoDB
mongoose.connect(process.env.MONGODB_URI, {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(() => {
  console.log('✅ Conectado a MongoDB');
}).catch((err) => {
  console.error('❌ Error de conexión MongoDB:', err);
});

// Rutas de la API
app.use('/api/users', require('./routes/Users'));

// Si no tienes el archivo auth.routes.js, comenta o elimina esta línea:
// app.use('/api/auth', require('./routes/auth.routes'));

// Si tienes rutas de ventas, asegúrate que exista el archivo routes/Sales.js,
// sino comenta también:
// app.use('/api/sales', require('./routes/Sales'));

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor corriendo en http://localhost:${PORT}`);
});
