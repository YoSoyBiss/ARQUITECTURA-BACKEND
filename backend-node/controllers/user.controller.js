const User = require('../models/User');
const bcrypt = require('bcryptjs');

// ✅ Obtener todos los usuarios (solo name, role, email)
exports.getAllUsers = async (req, res) => {
  try {
    const users = await User.find({}, 'name role email');
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

// ✅ Actualizar usuario por ID
exports.updateUser = async (req, res) => {
  console.log('Datos recibidos para actualizar:', req.body);
  try {
    const { name, password, role } = req.body;
    const updateData = {};
    if (name) updateData.name = name;
    if (role) updateData.role = role;

    if (password) {
      const salt = await bcrypt.genSalt(10);
      updateData.password = await bcrypt.hash(password, salt);
    }

    const updatedUser = await User.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true, select: '-password' }
    );

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

// ✅ Registrar nuevo usuario (rol por defecto)
exports.registerUser = async (req, res) => {
  const { name, email, password } = req.body;

  try {
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: 'Ya existe una cuenta con ese correo' });
    }

    const user = new User({
      name,
      email,
      password,
      role: 'consultant' // Rol por defecto
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
