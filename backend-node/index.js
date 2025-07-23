require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

// Conexión a MongoDB
mongoose.connect(process.env.MONGODB_URI, {
  useNewUrlParser: true,
  useUnifiedTopology: true
}).then(() => {
  console.log('MongoDB connected');
}).catch((err) => {
  console.error('MongoDB connection error:', err);
});

// Rutas
const usersRouter = require('./routes/Users');
app.use('/api/users', usersRouter);

const salesRouter = require('./routes/Sales');
app.use('/api/sales', salesRouter);

const authRoutes = require('./routes/auth.routes');
app.use('/api', authRoutes);


// Inicio del servidor
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
