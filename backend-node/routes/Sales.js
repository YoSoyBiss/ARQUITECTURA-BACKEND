const express = require('express');
const router = express.Router();
const Sale = require('../models/Sale');
const axios = require('axios');

// URL base del microservicio de productos Laravel
const LARAVEL_API_BASE = 'http://localhost:8000/api/products';

// Registrar una venta
router.post('/', async (req, res) => {
  const { userId, details } = req.body;

  if (!userId || !Array.isArray(details) || details.length === 0) {
    return res.status(400).json({ error: 'Missing required fields or details is empty' });
  }

  const productCache = {};
  let total = 0;

  try {
    // 1. Validar existencia de productos y stock suficiente
    for (const item of details) {
      let { productId, quantity } = item;

      const parsedId = Number(productId);
      const parsedQuantity = Number(quantity);

      if (!Number.isInteger(parsedId)) {
        return res.status(400).json({ error: `Invalid productId: ${productId}` });
      }

      if (!Number.isInteger(parsedQuantity) || parsedQuantity <= 0) {
        return res.status(400).json({ error: `Invalid quantity: ${quantity}` });
      }

      const response = await axios.get(`${LARAVEL_API_BASE}/${parsedId}`);
      const product = response.data;

      if (!product) {
        return res.status(404).json({ error: `Product with ID ${parsedId} not found` });
      }

      if (product.stock < parsedQuantity) {
        return res.status(400).json({ error: `Not enough stock for product ID ${parsedId}` });
      }

      productCache[parsedId] = product;

      // Calcular precio total parcial y asignar unitPrice desde Laravel
      item.productId = parsedId;
      item.quantity = parsedQuantity;
      item.unitPrice = product.price;

      total += parsedQuantity * product.price;
    }

    // 2. Descontar el stock en Laravel
    for (const item of details) {
      const { productId, quantity } = item;
      const product = productCache[productId];

      await axios.put(`${LARAVEL_API_BASE}/${productId}`, {
        title: product.title,
        author: product.author,
        publisher: product.publisher,
        price: product.price,
        stock: product.stock - quantity,
      });
    }

    // 3. Registrar venta en MongoDB
    const sale = new Sale({ total, userId, details });
    await sale.save();

    res.status(201).json({ message: 'Sale registered successfully', sale });

  } catch (error) {
    console.error('Error registering sale:', error?.response?.data || error.message);
    res.status(500).json({ error: 'Failed to register sale' });
  }
});

// Consultar todas las ventas
router.get('/', async (req, res) => {
  try {
    const sales = await Sale.find().populate('userId', 'name role');
    res.json(sales);
  } catch (error) {
    res.status(500).json({ error: 'Failed to get sales' });
  }
});

// Obtener venta por ID
router.get('/:id', async (req, res) => {
  try {
    const sale = await Sale.findById(req.params.id);
    if (!sale) return res.status(404).json({ error: 'Sale not found' });
    res.json(sale);
  } catch (error) {
    res.status(500).json({ error: 'Failed to get sale' });
  }
});

// Actualizar una venta
router.put('/:id', async (req, res) => {
  const saleId = req.params.id;
  const { total, userId, details } = req.body;

  if (!total || !userId || !Array.isArray(details) || details.length === 0) {
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

// Eliminar una venta
router.delete('/:id', async (req, res) => {
  try {
    const sale = await Sale.findByIdAndDelete(req.params.id);
    if (!sale) return res.status(404).json({ error: 'Sale not found' });

    res.json({ message: 'Sale deleted successfully' });
  } catch (error) {
    console.error('Error deleting sale:', error);
    res.status(500).json({ error: 'Failed to delete sale' });
  }
});

module.exports = router;
