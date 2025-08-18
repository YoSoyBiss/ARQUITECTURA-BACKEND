const User = require('../models/User');
const bcrypt = require('bcryptjs');
const Role = require('../models/Role');
const mongoose = require('mongoose');


// ... resto de imports


// ✅ Obtener todos los usuarios (solo name, role, email)
exports.getAllUsers = async (req, res) => {
  try {
    const users = await User.find({}, 'name role email').populate('role', 'name');
    res.json(users);
  } catch (error) {
    console.error('[UserController] getAllUsers:', error);
    res.status(500).json({ error: 'Error al obtener los usuarios' });
  }
};


// ✅ Eliminar usuario por ID
exports.deleteUser = async (req, res) => {
  try {
    const deleted = await User.findByIdAndDelete(req.params.id);
    if (!deleted) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }
    res.json({ message: 'Usuario eliminado correctamente' });
  } catch (error) {
    console.error('[UserController] deleteUser:', error);
    res.status(500).json({ error: 'Error al eliminar el usuario' });
  }
};



exports.updateUser = async (req, res) => {
  console.log('Datos recibidos para actualizar:', req.body);
  try {
    const { name, password, role } = req.body;
    const updateData = {};

    if (name) updateData.name = name;

    if (role) {
      // Validar que role sea un ObjectId válido y exista
      if (!mongoose.Types.ObjectId.isValid(role)) {
        return res.status(400).json({ error: 'Rol inválido (formato incorrecto)' });
      }
      const roleDoc = await Role.findById(role);
      if (!roleDoc) {
        return res.status(400).json({ error: 'Rol no encontrado' });
      }
      updateData.role = roleDoc._id;
    }

    if (password) {
      const salt = await bcrypt.genSalt(10);
      updateData.password = await bcrypt.hash(password, salt);
    }

    const updatedUser = await User.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, select: '-password' }
    ).populate('role', 'name');  // opcional: poblar info del rol para la respuesta

    if (!updatedUser) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }

    console.log('Usuario actualizado:', updatedUser);

    res.json({ message: 'Usuario actualizado correctamente', user: updatedUser });
  } catch (error) {
    console.error('[UserController] updateUser:', error);
    res.status(500).json({ error: 'Error al actualizar el usuario' });
  }
};


exports.registerUser = async (req, res) => {
  const { name, email, password, role } = req.body;  // recuerda que ahora recibes role como id

  try {
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: 'Ya existe una cuenta con ese correo' });
    }

    // Busca el rol por _id (role viene del select)
    const roleDoc = await Role.findById(role);
    if (!roleDoc) {
      return res.status(400).json({ message: 'Rol no válido' });
    }

    const user = new User({
      name,
      email,
      password,
      role: roleDoc._id
    });

    await user.save();

    const userObj = user.toObject();
    delete userObj.password;

    res.status(201).json({ message: 'Usuario registrado exitosamente', user: userObj });
  } catch (error) {
    console.error('[UserController] registerUser:', error);
    res.status(500).json({ message: 'Error del servidor al registrar' });
  }
};

// Obtener usuario por ID (sin password)
exports.getUserById = async (req, res) => {
  try {
    const user = await User.findById(req.params.id).select('-password');
    if (!user) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }
    res.json(user);
  } catch (error) {
    console.error('[UserController] getUserById:', error);
    res.status(500).json({ error: 'Error al obtener el usuario' });
  }
};

// ✅ Login de usuario

exports.loginUser = async (req, res) => {
  const { email, password } = req.body;

  try {
    // 🔹 Buscar usuario y popular el nombre del rol
    const user = await User.findOne({ email }).populate('role', 'name');
    if (!user) {
      return res.status(401).json({ message: 'Credenciales inválidas' });
    }

    // 🔹 Verificar contraseña
    const isMatch = await user.comparePassword(password);
    if (!isMatch) {
      return res.status(401).json({ message: 'Credenciales inválidas' });
    }

    // 🔹 Convertir a objeto plano y eliminar password
    const userObj = user.toObject();
    delete userObj.password;

    // 🔹 Reemplazar el objeto de rol por su nombre
    if (userObj.role && userObj.role.name) {
      userObj.role = userObj.role.name;
    }

    res.json({ message: 'Login exitoso', user: userObj });
  } catch (error) {
    console.error('[UserController] loginUser:', error);
    res.status(500).json({ message: 'Error en el servidor al iniciar sesión' });
  }

};

// ... (todas las demás funciones: getAllUsers, deleteUser, etc.)

// ✅ Nueva función para actualizar la contraseña
exports.updatePassword = async (req, res) => {
    try {
        const { id } = req.params;
        const { currentPassword, newPassword } = req.body;

        // 1. Encontrar el usuario por ID
        const user = await User.findById(id);
        if (!user) {
            return res.status(404).json({ message: 'Usuario no encontrado' });
        }

        // 2. Verificar la contraseña actual
        const isMatch = await user.comparePassword(currentPassword);
        if (!isMatch) {
            // Si la contraseña actual no coincide, devolver un error 401
            return res.status(401).json({ message: 'Contraseña actual incorrecta' });
        }

        // 3. Hashear y guardar la nueva contraseña
        const salt = await bcrypt.genSalt(10);
        user.password = await bcrypt.hash(newPassword, salt);
        await user.save();

        res.status(200).json({ message: 'Contraseña actualizada correctamente' });

    } catch (error) {
        console.error('[UserController] updatePassword:', error);
        res.status(500).json({ message: 'Error en el servidor al actualizar la contraseña' });
    }
};

// ✅ Nueva función para actualizar la contraseña
exports.updatePassword = async (req, res) => {
    try {
        const { id } = req.params;
        const { currentPassword, newPassword } = req.body;

        // 1. Encontrar el usuario por ID
        const user = await User.findById(id);
        if (!user) {
            return res.status(404).json({ message: 'Usuario no encontrado' });
        }

        // 2. Verificar la contraseña actual
        const isMatch = await user.comparePassword(currentPassword);
        if (!isMatch) {
            // Si la contraseña actual no coincide, devolver un error 401
            return res.status(401).json({ message: 'Contraseña actual incorrecta' });
        }

        // 3. Hashear y guardar la nueva contraseña
        const salt = await bcrypt.genSalt(10);
        user.password = await bcrypt.hash(newPassword, salt);
        await user.save();

        res.status(200).json({ message: 'Contraseña actualizada correctamente' });

    } catch (error) {
        console.error('[UserController] updatePassword:', error);
        res.status(500).json({ message: 'Error en el servidor al actualizar la contraseña' });
    }
};



// Asegúrate de exportar la nueva función


