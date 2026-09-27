CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price NUMERIC(10, 2) NOT NULL DEFAULT 0,
    stock INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

INSERT INTO products (name, description, price, stock) VALUES
('Laptop ASUS', 'Laptop gaming 16GB RAM', 15000000, 10),
('Mouse Logitech', 'Wireless mouse', 250000, 50),
('Keyboard Mechanical', 'RGB keyboard', 750000, 30);