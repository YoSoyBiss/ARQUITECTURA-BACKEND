const express = require('express');
const router = express.Router();
const Sale = require('../models/Sale');

// Registrar una venta
router.post('/', async (req, res) => {
  const { total, userId, details } = req.body;

  if (!total || !userId || !details || !Array.isArray(details) || details.length === 0) {
    return res.status(400).json({ error: 'Missing required fields or details is empty' });
  }

  try {
    const sale = new Sale({
      total,
      userId,
      details,
    });
    await sale.save();
    res.status(201).json({ message: 'Sale registered successfully', sale });
  } catch (error) {
    console.error('Error registering sale:', error);
    res.status(500).json({ error: 'Failed to register sale' });
  }
});

// Consultar todas las ventas (podrías añadir filtros y paginación luego)
router.get('/', async (req, res) => {
  try {
    const sales = await Sale.find().populate('userId', 'name role');
    res.json(sales);
  } catch (error) {
    res.status(500).json({ error: 'Failed to get sales' });
  }
});

// PUT /api/sales/:id — actualizar una venta por id
router.put('/:id', async (req, res) => {
  const saleId = req.params.id;
  const { total, userId, details } = req.body;

  if (!total || !userId || !details || !Array.isArray(details) || details.length === 0) {
    return res.status(400).json({ error: 'Missing required fields or details is empty' });
  }

  try {
    const sale = await Sale.findByIdAndUpdate(
      saleId,
      { total, userId, details },
      { new: true, runValidators: true }
    );
    if (!sale) return res.status(404).json({ error: 'Sale not found' });

    res.json({ message: 'Sale updated successfully', sale });
  } catch (error) {
    console.error('Error updating sale:', error);
    res.status(500).json({ error: 'Failed to update sale' });
  }
});

// DELETE /api/sales/:id — eliminar una venta por id
router.delete('/:id', async (req, res) => {
  const saleId = req.params.id;

  try {
    const sale = await Sale.findByIdAndDelete(saleId);
    if (!sale) return res.status(404).json({ error: 'Sale not found' });

    res.json({ message: 'Sale deleted successfully' });
  } catch (error) {
    console.error('Error deleting sale:', error);
    res.status(500).json({ error: 'Failed to delete sale' });
  }
});


module.exports = router;
