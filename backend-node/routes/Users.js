const express = require('express');
const router = express.Router();
const {
  getAllUsers,
  getUserById,   // <-- este debes agregar
  deleteUser,
  updateUser,
  registerUser
} = require('../controllers/user.controller');


// 🔐 Si tienes middleware JWT, se puede agregar aquí, ejemplo:
// const verifyToken = require('../middlewares/verifyToken');
// router.use(verifyToken);

// 📌 Rutas CRUD + registro
router.get('/:id', getUserById); // GET /api/users/:id
router.get('/', getAllUsers);    // GET /api/users
router.post('/register', registerUser);     // POST /api/users/register
router.delete('/:id', deleteUser);            // DELETE /api/users/:id
router.put('/:id', updateUser);               // PUT /api/users/:id


module.exports = router;
