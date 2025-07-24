const User = require('../models/User');
const jwt = require('jsonwebtoken');

// Usar variable de entorno o valor por defecto
const JWT_SECRET = process.env.JWT_SECRET || 'defaultsecret';

// LOGIN
exports.login = async (req, res) => {
  const { email, password } = req.body;

  try {
    // Buscar usuario por correo
    const user = await User.findOne({ email });

    if (!user) {
      return res.status(401).json({ message: 'Usuario no encontrado' });
    }

    // Verificar contraseña
    const isMatch = await user.comparePassword(password);
    if (!isMatch) {
      return res.status(401).json({ message: 'Contraseña incorrecta' });
    }

    // Generar token
    const token = jwt.sign(
      { userId: user._id, role: user.role },
      JWT_SECRET,
      { expiresIn: '4h' }
    );

    // Enviar respuesta
    return res.status(200).json({
      message: 'Inicio de sesión exitoso',
      token,
      user: {
        name: user.name,
        role: user.role,
        email: user.email
      }
    });
  } catch (error) {
    console.error('Error en login:', error);
    return res.status(500).json({ message: 'Error del servidor' });
  }
};

// REGISTER (temporal para pruebas)
exports.register = async (req, res) => {
  const { name, email, password, role } = req.body;

  try {
    // Verificar si ya existe el correo
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: 'El usuario ya existe' });
    }

    // Crear y guardar usuario
    const user = new User({ name, email, password, role });
    await user.save();

    res.status(201).json({ message: 'Usuario creado correctamente', user });
  } catch (error) {
    console.error('Error en registro:', error);
    res.status(500).json({ message: 'Error del servidor' });
  }
};
