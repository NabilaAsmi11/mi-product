CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL DEFAULT 0,
    stock INTEGER NOT NULL DEFAULT 0,
    image TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Data dummy dengan Base64 gambar 1x1 pixel yang valid
INSERT INTO products (name, description, price, stock, image) VALUES
('Keyboard Mekanik', 'Keyboard mechanical dengan RGB', 750000, 10, 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADU1EQVR42mNkYPhfDwAChWGA60e6kgAAAABJRU5BrkJggg=='),
('Mouse Gaming', 'Mouse dengan sensor presisi tinggi', 250000, 20, 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADU1EQVR42mNkYPhfDwAChWGA60e6kgAAAABJRU5BrkJggg==');