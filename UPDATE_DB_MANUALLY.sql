-- ====================================================================
-- MANUAL DATABASE UPDATE SCRIPT
-- Run this if you need to update an existing bookstore database
-- ====================================================================

-- Update admin credentials (NEW: Admin@123)
UPDATE users SET 
    password = '$2y$10$cRL33LnVaBuAinA5KDX.9OjdTe8UFJ1/Y9HkuyXYpY19i4qXrVzzO'
WHERE email = 'admin@bookstore.com';

-- Update book cover images to match uploaded files
UPDATE books SET cover_image = 'the great gatsby.jpeg' WHERE title = 'The Great Gatsby';
UPDATE books SET cover_image = 'to kill a mockingbird.jpeg' WHERE title = 'To Kill a Mockingbird';
UPDATE books SET cover_image = '1984.jpeg' WHERE title = '1984';
UPDATE books SET cover_image = 'sapiens.jpeg' WHERE title = 'Sapiens';
UPDATE books SET cover_image = 'atomic habits.jpeg' WHERE title = 'Atomic Habits';

-- Verify updates
SELECT id, email, title, cover_image FROM users, books LIMIT 10;

-- ====================================================================
-- If cart table doesn't exist, create it:
-- ====================================================================

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);

-- Test: Check if cart table exists
SHOW TABLES LIKE 'cart';
