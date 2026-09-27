const pool = require('../config/db');

const ProductModel = {
  // GET by ID
  getById: async (id) => {
    const result = await pool.query('SELECT * FROM products WHERE id = $1', [id]);
    return result.rows[0];
  },

  // GET all (bonus)
  getAll: async () => {
    const result = await pool.query('SELECT * FROM products ORDER BY id ASC');
    return result.rows;
  },

  // POST - Create
  create: async ({ name, description, price, stock }) => {
    const result = await pool.query(
      `INSERT INTO products (name, description, price, stock)
       VALUES ($1, $2, $3, $4) RETURNING *`,
      [name, description, price, stock]
    );
    return result.rows[0];
  },

  // PUT - Update
  update: async (id, { name, description, price, stock }) => {
    const result = await pool.query(
      `UPDATE products
       SET name = $1, description = $2, price = $3, stock = $4, updated_at = NOW()
       WHERE id = $5 RETURNING *`,
      [name, description, price, stock, id]
    );
    return result.rows[0];
  },

  // DELETE
  delete: async (id) => {
    const result = await pool.query('DELETE FROM products WHERE id = $1 RETURNING *', [id]);
    return result.rows[0];
  },
};

module.exports = ProductModel;