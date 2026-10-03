const express = require('express');
const { Pool } = require('pg');

const app = express();
const PORT = process.env.PORT || 3001;

// Batas body dinaikkan ke 3MB agar menerima Base64 gambar tanpa error 413
app.use(express.json({ limit: '3mb' }));

const pool = new Pool({
  host: process.env.DB_HOST || 'product-db',
  user: process.env.DB_USER || 'admin',
  password: process.env.DB_PASSWORD || 'admin123',
  database: process.env.DB_NAME || 'productdb',
  port: parseInt(process.env.DB_PORT, 10) || 5432,
});

// Helper Fungsi Validasi Base64 Image
function validateBase64Image(image) {
  if (!image || typeof image !== 'string' || image.trim() === '') {
    return { valid: false, message: 'Field image wajib diisi' };
  }

  let cleanBase64 = image;
  if (image.includes(',')) {
    cleanBase64 = image.split(',')[1];
  }

  // Validasi Format Base64
  const base64Regex = /^[A-Za-z0-9+/]+={0,2}$/;
  if (cleanBase64.length % 4 !== 0 || !base64Regex.test(cleanBase64)) {
    return { valid: false, message: 'Field image harus berupa Base64 yang valid' };
  }

  // Validasi Ukuran (Diubah sementara ke 10 byte untuk Skenario 4)
  const buffer = Buffer.from(cleanBase64, 'base64');
const maxSizeInBytes = 2 * 1024 * 1024; // 2 MB// Limit sementara untuk uji coba Skenario 4

  if (buffer.length > maxSizeInBytes) {
    return { valid: false, message: 'Ukuran image maksimal 2 MB' };
  }

  // Validasi Magic Bytes
  const hexHeader = buffer.toString('hex', 0, 4);
  const isPNG = hexHeader.startsWith('89504e47');
  const isJPEG = hexHeader.startsWith('ffd8ff');
  const isWEBP = hexHeader.startsWith('52494646');
  const isGIF = hexHeader.startsWith('474946');

  if (!isPNG && !isJPEG && !isWEBP && !isGIF) {
    return { valid: false, message: 'Field image harus berupa Base64 yang valid' };
  }

  return { valid: true, cleanBase64 };
}

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
  const { name, description, price, stock, image } = req.body;

  if (!name || price === undefined) {
    return res.status(400).json({ error: 'Name dan price wajib diisi!' });
  }

  const imageValidation = validateBase64Image(image);
  if (!imageValidation.valid) {
    return res.status(400).json({ error: imageValidation.message });
  }

  try {
    const query = `
      INSERT INTO products (name, description, price, stock, image)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *;
    `;
    const values = [name, description || null, price, stock || 0, imageValidation.cleanBase64];
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
  const { name, description, price, stock, image } = req.body;

  const imageValidation = validateBase64Image(image);
  if (!imageValidation.valid) {
    return res.status(400).json({ error: imageValidation.message });
  }

  try {
    const query = `
      UPDATE products 
      SET 
        name = COALESCE($1, name),
        description = COALESCE($2, description),
        price = COALESCE($3, price),
        stock = COALESCE($4, stock),
        image = $5,
        updated_at = NOW()
      WHERE id = $6
      RETURNING *;
    `;
    const values = [
      name || null,
      description || null,
      price || null,
      stock || null,
      imageValidation.cleanBase64,
      id
    ];
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
  console.log(`Product service running on port ${PORT}`);
});