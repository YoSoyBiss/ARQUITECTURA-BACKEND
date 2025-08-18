// index.js
require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const bcrypt = require('bcryptjs');

// Importar los modelos y el controlador
const Role = require('./models/Role');
const User = require('./models/User');
const userController = require('./controllers/user.controller'); // Importar el controlador

const app = express();
app.use(cors());
app.use(express.json());

// Importar y usar las rutas
app.use('/api/users', require('./routes/Users'));
app.use('/api/roles', require('./routes/Roles'));
app.use('/api/sales', require('./routes/Sales'));

// Conexión a MongoDB
mongoose.connect(process.env.MONGODB_URI, {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(async () => {
  console.log('✅ Conectado a MongoDB');

  try {
    // Crear roles por defecto si no existen
    const roleCount = await Role.estimatedDocumentCount();
    if (roleCount === 0) {
      await Role.insertMany([
        { name: 'admin', description: 'Administrador del sistema' },
        { name: 'seller', description: 'Vendedor' },
        { name: 'consultant', description: 'Consultor' }
      ]);
      console.log('✅ Roles por defecto creados');
    } else {
      console.log('ℹ️ Roles ya existen, no se crean nuevamente');
    }

    // --- Lógica para crear el usuario admin usando el controlador ---
    const adminEmail = 'admin@gmail.com';
    const adminPassword = '123456';
    const adminName = 'admin';

    // Verificar si el usuario admin ya existe
    const existingAdmin = await User.findOne({ email: adminEmail });
    if (!existingAdmin) {
      // Buscar el rol de admin
      const adminRole = await Role.findOne({ name: 'admin' });

      if (adminRole) {
        // Crear objetos simulados (mock) de req y res para la función del controlador
        const mockReq = {
          body: {
            name: adminName,
            email: adminEmail,
            password: adminPassword,
            role: adminRole._id
          }
        };

        const mockRes = {
          status: (statusCode) => {
            console.log(`[Mock Res] Status: ${statusCode}`);
            return mockRes;
          },
          json: (data) => {
            console.log('[Mock Res] JSON Data:', data);
          }
        };

        // Llamar a la función del controlador para crear el usuario
        await userController.registerUser(mockReq, mockRes);
        console.log('✅ Usuario admin creado exitosamente a través del controlador.');
      } else {
        console.error('❌ Error: El rol "admin" no fue encontrado, no se puede crear el usuario.');
      }
    } else {
      console.log('ℹ️ El usuario admin ya existe, no se crea nuevamente');
    }
    // --- Fin de la nueva lógica ---

  } catch (error) {
    console.error('❌ Error durante la inicialización de la base de datos (roles/usuario admin):', error);
  }

}).catch((err) => {
  console.error('❌ Error de conexión MongoDB:', err);
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`🚀 Servidor corriendo en http://localhost:${PORT}`);
});
