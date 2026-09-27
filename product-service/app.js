const express = require('express');
const { Pool } = require('pg');

const app = express();
const PORT = process.env.PORT || 3001;

app.use(express.json());

// Konfigurasi koneksi database PostgreSQL
const pool = new Pool({
  host: process.env.DB_HOST || 'product-db',
  user: process.env.DB_USER || 'admin',
  password: process.env.DB_PASSWORD || 'admin123',
  database: process.env.DB_NAME || 'productdb',
  port: parseInt(process.env.DB_PORT, 10) || 5432,
});

// 1. GET - Ambil Semua Produk
app.get('/products', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM products ORDER BY id ASC');
    res.json(result.rows);
  } catch (err) {
    console.error('Error GET /products:', err.message);
    res.status(500).json({ error: 'Gagal mengambil data produk' });
  }
});

// 2. GET - Ambil Produk Berdasarkan ID
app.get('/products/:id', async (req, res) => {
  const { id } = req.params;
  try {
    const result = await pool.query('SELECT * FROM products WHERE id = $1', [id]);
    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Produk tidak ditemukan' });
    }
    res.json(result.rows[0]);
  } catch (err) {
    console.error(`Error GET /products/${id}:`, err.message);
    res.status(500).json({ error: 'Gagal mengambil data produk' });
  }
});

// 3. POST - Tambah Produk
app.post('/products', async (req, res) => {
  const { name, description, price, stock } = req.body;

  if (!name || price === undefined) {
    return res.status(400).json({ error: 'Name dan price wajib diisi!' });
  }

  try {
    const query = `
      INSERT INTO products (name, description, price, stock)
      VALUES ($1, $2, $3, $4)
      RETURNING *;
    `;
    const values = [name, description || null, price, stock || 0];
    const result = await pool.query(query, values);

    res.status(201).json({
      message: 'Product berhasil ditambahkan!',
      data: result.rows[0]
    });
  } catch (err) {
    console.error('Error POST /products:', err.message);
    res.status(500).json({ error: 'Gagal menambahkan produk' });
  }
});

// 4. PUT - Perbarui Produk
app.put('/products/:id', async (req, res) => {
  const { id } = req.params;
  const { name, description, price, stock } = req.body;

  try {
    const query = `
      UPDATE products 
      SET 
        name = COALESCE($1, name),
        description = COALESCE($2, description),
        price = COALESCE($3, price),
        stock = COALESCE($4, stock),
        updated_at = NOW()
      WHERE id = $5
      RETURNING *;
    `;
    const values = [name || null, description || null, price || null, stock || null, id];
    const result = await pool.query(query, values);

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Produk tidak ditemukan' });
    }

    res.json({
      message: 'Product berhasil diperbarui!',
      data: result.rows[0]
    });
  } catch (err) {
    console.error(`Error PUT /products/${id}:`, err.message);
    res.status(500).json({ error: 'Gagal memperbarui produk' });
  }
});

// 5. DELETE - Hapus Produk
app.delete('/products/:id', async (req, res) => {
  const { id } = req.params;

  try {
    const result = await pool.query('DELETE FROM products WHERE id = $1 RETURNING *', [id]);

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Produk tidak ditemukan' });
    }

    res.json({
      message: 'Product berhasil dihapus!',
      data: result.rows[0]
    });
  } catch (err) {
    console.error(`Error DELETE /products/${id}:`, err.message);
    res.status(500).json({ error: 'Gagal menghapus produk' });
  }
});

app.listen(PORT, '0.0.0.0', () => {
  console.log(`Server running on port ${PORT}`);
});