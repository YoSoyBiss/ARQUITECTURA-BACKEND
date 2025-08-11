require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');

// Importar el modelo de Roles directamente
const Role = require('./models/Role');

const app = express();
app.use(cors());
app.use(express.json());

app.use('/api/users', require('./routes/Users'));
app.use('/api/roles', require('./routes/Roles'));
app.use('/api/sales', require('./routes/Sales'));

// Conexión a MongoDB
mongoose.connect(process.env.MONGODB_URI, {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(async () => {
  console.log('✅ Conectado a MongoDB');

  // Crear roles por defecto si no existen
  try {
    const count = await Role.estimatedDocumentCount();
    if (count === 0) {
      await Role.insertMany([
        { name: 'admin', description: 'Administrador del sistema' },
        { name: 'seller', description: 'Vendedor' },
        { name: 'consultant', description: 'Consultor' }
      ]);
      console.log('✅ Roles por defecto creados');
    } else {
      console.log('ℹ️ Roles ya existen, no se crean nuevamente');
    }
  } catch (error) {
    console.error('❌ Error creando roles por defecto:', error);
  }

}).catch((err) => {
  console.error('❌ Error de conexión MongoDB:', err);
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor corriendo en http://localhost:${PORT}`);
});
