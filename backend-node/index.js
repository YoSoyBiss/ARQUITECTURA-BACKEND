// index.js
require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const bcrypt = require('bcryptjs'); // Necesario para hashear la contraseña del admin

// Importar los modelos necesarios
const Role = require('./models/Role');
const User = require('./models/User');

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

    // --- NUEVA LÓGICA PARA CREAR EL USUARIO ADMIN ---
    const adminEmail = 'admin@gmail.com';
    const adminPassword = '123456';
    const adminName = 'admin';

    // 1. Verificar si el usuario admin ya existe
    const existingAdmin = await User.findOne({ email: adminEmail });
    if (!existingAdmin) {
      // 2. Si no existe, encontrar el rol de admin
      const adminRole = await Role.findOne({ name: 'admin' });

      if (adminRole) {
        // 3. Hashear la contraseña
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash(adminPassword, salt);

        // 4. Crear el nuevo usuario admin
        const newAdminUser = new User({
          name: adminName,
          email: adminEmail,
          password: hashedPassword,
          role: adminRole._id
        });

        // 5. Guardar el usuario en la base de datos
        await newAdminUser.save();
        console.log('✅ Usuario admin creado exitosamente');
      } else {
        console.error('❌ Error: El rol "admin" no fue encontrado, no se puede crear el usuario.');
      }
    } else {
      console.log('ℹ️ El usuario admin ya existe, no se crea nuevamente');
    }
    // --- FIN DE LA NUEVA LÓGICA ---

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
