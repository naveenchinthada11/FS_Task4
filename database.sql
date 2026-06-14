-- Create Database
CREATE DATABASE IF NOT EXISTS bookstore;
USE bookstore;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Books Table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    stock INT DEFAULT 0,
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_price (price)
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);

-- Order Items Table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
);

-- Cart Table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);

-- Sample Admin User (password: Admin@123)
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@bookstore.com', '$2y$10$cRL33LnVaBuAinA5KDX.9OjdTe8UFJ1/Y9HkuyXYpY19i4qXrVzzO', 'admin');

-- Sample Books
INSERT INTO books (title, author, category, price, description, stock, cover_image) VALUES 
('The Alchemist', 'Paulo Coelho', 'Fiction', 11.99, 'A journey of self-discovery', 40, 'the_alchemist.jpeg'),
('Clean Code', 'Robert C. Martin', 'Non-Fiction', 29.99, 'A handbook of agile software craftsmanship', 30, 'clean_code.jpeg'),
('The Pragmatic Programmer', 'Andrew Hunt & David Thomas', 'Non-Fiction', 35.00, 'From journeyman to master', 25, 'pragmatic_programmer.jpeg'),
('Thinking, Fast and Slow', 'Daniel Kahneman', 'Science', 18.50, 'Insights into human decision making', 20, 'thinking_fast_and_slow.jpeg'),
('The Hobbit', 'J.R.R. Tolkien', 'Fiction', 14.99, 'An unexpected journey', 60, 'the_hobbit.jpeg');
