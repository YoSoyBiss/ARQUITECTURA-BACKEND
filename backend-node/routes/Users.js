const express = require('express');
const router = express.Router();
const User = require('../models/User');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

// Register user
router.post('/register', async (req, res) => {
  const { name, password, role } = req.body;

  try {
    const hash = await bcrypt.hash(password, 10);
    const user = new User({ name, password: hash, role });
    await user.save();
    res.status(201).json({ message: 'User created successfully' });
  } catch (error) {
    console.error('Error creating user:', error); 
    res.status(500).json({ error: 'Failed to create user' });
  }
});


// GET /api/users - obtener todos los usuarios (solo name, role y _id)
router.get('/', async (req, res) => {
  try {
    const users = await User.find({}, 'name role');
    res.json(users);
  } catch (error) {
    console.error('Error creating user:', error); 
    res.status(500).json({ error: 'Failed to fetch users' });
  }
});

// DELETE /api/users/:id - eliminar usuario por id
router.delete('/:id', async (req, res) => {
  try {
    const deleted = await User.findByIdAndDelete(req.params.id);
    if (!deleted) return res.status(404).json({ error: 'User not found' });
    res.json({ message: 'User deleted successfully' });
  } catch (error) {
    console.error('Error deleting user:', error); 
    res.status(500).json({ error: 'Failed to delete user' });
  }
});

// PUT /api/users/:id - actualizar usuario por id
router.put('/:id', async (req, res) => {
  try {
    const { name, password, role } = req.body;
    const updateData = { name, role };
    
    // Si mandan password, lo hasheamos
    if (password) {
      const hash = await bcrypt.hash(password, 10);
      updateData.password = hash;
    }
    
    const updatedUser = await User.findByIdAndUpdate(
      req.params.id,
      updateData,
      { new: true }
    );

    if (!updatedUser) return res.status(404).json({ error: 'User not found' });

    res.json({ message: 'User updated successfully', user: updatedUser });
  } catch (error) {
    console.error('Error updating user:', error); 
    res.status(500).json({ error: 'Failed to update user' });
  }
});


// Login user
router.post('/login', async (req, res) => {
  const { name, password } = req.body;

  try {
    const user = await User.findOne({ name });
    if (!user) return res.status(400).json({ error: 'User not found' });

    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) return res.status(400).json({ error: 'Incorrect password' });

    const token = jwt.sign(
      { id: user._id, role: user.role },
      process.env.JWT_SECRET,
      { expiresIn: '1h' }
    );

    res.json({ token, role: user.role, name: user.name });
  } catch (error) {
    console.error('Error login:', error); 
    res.status(500).json({ error: 'Login error' });
  }
});

module.exports = router;

