const ProductModel = require('../models/productModel');

const ProductController = {
  // GET /products/:id
  getProductById: async (req, res) => {
    try {
      const { id } = req.params;
      const product = await ProductModel.getById(id);

      if (!product) {
        return res.status(404).json({
          success: false,
          message: `Product with id ${id} not found`,
        });
      }

      res.status(200).json({
        success: true,
        message: 'Product retrieved successfully',
        data: product,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ success: false, message: err.message });
    }
  },

  // GET /products (bonus)
  getAllProducts: async (req, res) => {
    try {
      const products = await ProductModel.getAll();
      res.status(200).json({
        success: true,
        message: 'Products retrieved successfully',
        data: products,
      });
    } catch (err) {
      res.status(500).json({ success: false, message: err.message });
    }
  },

  // POST /products
  createProduct: async (req, res) => {
    try {
      const { name, description, price, stock } = req.body;

      if (!name || price === undefined) {
        return res.status(400).json({
          success: false,
          message: 'Field "name" and "price" are required',
        });
      }

      const product = await ProductModel.create({ name, description, price, stock });

      res.status(201).json({
        success: true,
        message: 'Product created successfully',
        data: product,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ success: false, message: err.message });
    }
  },

  // PUT /products/:id
  updateProduct: async (req, res) => {
    try {
      const { id } = req.params;
      const { name, description, price, stock } = req.body;

      const existing = await ProductModel.getById(id);
      if (!existing) {
        return res.status(404).json({
          success: false,
          message: `Product with id ${id} not found`,
        });
      }

      const updated = await ProductModel.update(id, {
        name: name ?? existing.name,
        description: description ?? existing.description,
        price: price ?? existing.price,
        stock: stock ?? existing.stock,
      });

      res.status(200).json({
        success: true,
        message: 'Product updated successfully',
        data: updated,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ success: false, message: err.message });
    }
  },

  // DELETE /products/:id
  deleteProduct: async (req, res) => {
    try {
      const { id } = req.params;
      const deleted = await ProductModel.delete(id);

      if (!deleted) {
        return res.status(404).json({
          success: false,
          message: `Product with id ${id} not found`,
        });
      }

      res.status(200).json({
        success: true,
        message: 'Product deleted successfully',
        data: deleted,
      });
    } catch (err) {
      console.error(err);
      res.status(500).json({ success: false, message: err.message });
    }
  },
};

module.exports = ProductController;