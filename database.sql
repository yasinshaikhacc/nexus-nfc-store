-- Database Schema for VOLTIX

CREATE DATABASE IF NOT EXISTS voltix_db;
USE voltix_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'manager') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category VARCHAR(100) NOT NULL, -- e.g., 'Mobile', 'Laptop'
    brand VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) DEFAULT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255), -- Main image
    description TEXT,
    specifications JSON, -- Key-value pairs for specs
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'out_of_stock', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'processing',
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL, -- Price at time of purchase
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert a default admin user (Password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@voltix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert some dummy products
INSERT INTO products (name, slug, category, brand, price, stock, image, description, is_featured) VALUES 
('iPhone 15 Pro', 'iphone-15-pro', 'Mobiles', 'Apple', 999.00, 50, 'https://placehold.co/600x400/png?text=iPhone+15', 'The ultimate iPhone.', TRUE),
('Samsung Galaxy S24 Ultra', 'samsung-s24-ultra', 'Mobiles', 'Samsung', 1199.00, 40, 'https://placehold.co/600x400/png?text=Samsung+S24', 'Galaxy AI is here.', TRUE),
('Sony WH-1000XM5', 'sony-wh1000xm5', 'Audio', 'Sony', 399.00, 100, 'https://placehold.co/600x400/png?text=Sony+Headphones', 'Noise canceling perfection.', TRUE),
('MacBook Air M3', 'macbook-air-m3', 'Laptops', 'Apple', 1099.00, 30, 'https://placehold.co/600x400/png?text=Macbook+Air', 'Lean. Mean. M3 machine.', TRUE);
