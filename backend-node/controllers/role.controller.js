const Role = require('../models/Role');

// Crear nuevo rol
exports.createRole = async (req, res) => {
  try {
    const { name, description } = req.body;
    const role = new Role({ name, description });
    await role.save();
    res.status(201).json({ message: 'Rol creado', role });
  } catch (error) {
    console.error('[RoleController] createRole:', error);
    res.status(500).json({ error: 'Error al crear rol' });
  }
};

// Obtener todos los roles
exports.getAllRoles = async (req, res) => {
  try {
    const roles = await Role.find();
    res.json(roles);
  } catch (error) {
    console.error('[RoleController] getAllRoles:', error);
    res.status(500).json({ error: 'Error al obtener los roles' });
  }
};

// Obtener rol por ID
exports.getRoleById = async (req, res) => {
  try {
    const role = await Role.findById(req.params.id);
    if (!role) return res.status(404).json({ error: 'Rol no encontrado' });
    res.json(role);
  } catch (error) {
    console.error('[RoleController] getRoleById:', error);
    res.status(500).json({ error: 'Error al obtener el rol' });
  }
};

// Actualizar rol
exports.updateRole = async (req, res) => {
  try {
    const { name, description } = req.body;
    const updated = await Role.findByIdAndUpdate(
      req.params.id,
      { name, description },
      { new: true }
    );
    if (!updated) return res.status(404).json({ error: 'Rol no encontrado' });
    res.json({ message: 'Rol actualizado', role: updated });
  } catch (error) {
    console.error('[RoleController] updateRole:', error);
    res.status(500).json({ error: 'Error al actualizar el rol' });
  }
};

// Eliminar rol
exports.deleteRole = async (req, res) => {
  try {
    const deleted = await Role.findByIdAndDelete(req.params.id);
    if (!deleted) return res.status(404).json({ error: 'Rol no encontrado' });
    res.json({ message: 'Rol eliminado correctamente' });
  } catch (error) {
    console.error('[RoleController] deleteRole:', error);
    res.status(500).json({ error: 'Error al eliminar el rol' });
  }
};
