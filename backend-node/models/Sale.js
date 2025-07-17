const mongoose = require('mongoose');

const saleDetailSchema = new mongoose.Schema({
  productId: { type: Number, required: true },
  quantity: { type: Number, required: true },
  unitPrice: { type: Number, required: true },
});

const saleSchema = new mongoose.Schema({
  total: { type: Number, required: true },
  date: { type: Date, default: Date.now },
  userId: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
  details: [saleDetailSchema],
});

module.exports = mongoose.model('Sale', saleSchema);
