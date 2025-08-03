const express = require('express');
const router = express.Router();

const {
  loginUser,
  getAllUsers,
  getUserById,
  deleteUser,
  updateUser,
  registerUser
} = require('../controllers/user.controller');

// 📌 Rutas CRUD + registro + login
router.post('/login', loginUser);             // POST /api/users/login
router.post('/register', registerUser);       // POST /api/users/register
router.get('/:id', getUserById);              // GET /api/users/:id
router.get('/', getAllUsers);                 // GET /api/users
router.put('/:id', updateUser);               // PUT /api/users/:id
router.delete('/:id', deleteUser);            // DELETE /api/users/:id

module.exports = router;
