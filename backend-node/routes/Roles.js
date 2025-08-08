const express = require('express');
const router = express.Router();
const {
  createRole,
  getAllRoles,
  getRoleById,
  updateRole,
  deleteRole
} = require('../controllers/role.controller');

// 📌 Rutas CRUD para roles
router.post('/', createRole);
router.get('/', getAllRoles);
router.get('/:id', getRoleById);
router.put('/:id', updateRole);
router.delete('/:id', deleteRole);

module.exports = router;
