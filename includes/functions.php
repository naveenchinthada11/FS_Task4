<?php
require_once 'db.php';

// Check if User is Logged In
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if User is Admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if Not Logged In
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/auth/login.php');
        exit;
    }
}

// Redirect if Not Admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . APP_URL . '/index.php');
        exit;
    }
}

// Hash Password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify Password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitize Input
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Validate Email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get User by ID
function getUserById($id) {
    return fetchOne("SELECT id, username, email, role FROM users WHERE id = ?", [$id], "i");
}

// Get Current User
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return getUserById($_SESSION['user_id']);
}

// Get All Books with Pagination
function getBooks($page = 1, $limit = 12, $search = '', $category = '', $sort = 'newest') {
    $offset = ($page - 1) * $limit;
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];
    $types = '';
    
    if ($search) {
        $query .= " AND (title LIKE ? OR author LIKE ?)";
        $search = "%$search%";
        $params[] = $search;
        $params[] = $search;
        $types .= "ss";
    }
    
    if ($category) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    switch ($sort) {
        case 'price_low':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY price DESC";
            break;
        case 'oldest':
            $query .= " ORDER BY created_at ASC";
            break;
        default:
            $query .= " ORDER BY created_at DESC";
    }
    
    $query .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";
    
    return fetchData($query, $params, $types);
}

// Get Total Books Count
function getTotalBooks($search = '', $category = '') {
    $query = "SELECT COUNT(*) as count FROM books WHERE 1=1";
    $params = [];
    $types = '';
    
    if ($search) {
        $query .= " AND (title LIKE ? OR author LIKE ?)";
        $search = "%$search%";
        $params[] = $search;
        $params[] = $search;
        $types .= "ss";
    }
    
    if ($category) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    $result = fetchOne($query, $params, $types);
    return $result['count'] ?? 0;
}

// Get Book by ID
function getBookById($id) {
    return fetchOne("SELECT * FROM books WHERE id = ?", [$id], "i");
}

// Add Book
function addBook($title, $author, $category, $price, $description, $stock) {
    $result = modifyData(
        "INSERT INTO books (title, author, category, price, description, stock) VALUES (?, ?, ?, ?, ?, ?)",
        [$title, $author, $category, $price, $description, $stock],
        "ssssdi"
    );
    return $result;
}

// Update Book
function updateBook($id, $title, $author, $category, $price, $description, $stock) {
    $result = modifyData(
        "UPDATE books SET title=?, author=?, category=?, price=?, description=?, stock=? WHERE id=?",
        [$title, $author, $category, $price, $description, $stock, $id],
        "ssssdii"
    );
    return $result;
}

// Delete Book
function deleteBook($id) {
    return modifyData("DELETE FROM books WHERE id = ?", [$id], "i");
}

// Get User Orders
function getUserOrders($user_id) {
    return fetchData(
        "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
        [$user_id],
        "i"
    );
}

// Get Order with Items
function getOrderWithItems($order_id) {
    $order = fetchOne("SELECT * FROM orders WHERE id = ?", [$order_id], "i");
    if ($order) {
        $items = fetchData(
            "SELECT oi.*, b.title, b.author FROM order_items oi 
             JOIN books b ON oi.book_id = b.id 
             WHERE oi.order_id = ?",
            [$order_id],
            "i"
        );
        $order['items'] = $items;
    }
    return $order;
}

// Create Order
function createOrder($user_id, $items) {
    global $conn;
    
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Insert Order
    $orderResult = modifyData(
        "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')",
        [$user_id, $total_amount],
        "id"
    );
    
    if (!$orderResult['success']) {
        return $orderResult;
    }
    
    $order_id = $orderResult['insert_id'];
    
    // Insert Order Items
    foreach ($items as $item) {
        modifyData(
            "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)",
            [$order_id, $item['book_id'], $item['quantity'], $item['price']],
            "iid"
        );
    }
    
    return ['success' => true, 'order_id' => $order_id];
}

// Get All Users
function getAllUsers() {
    return fetchData("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
}

// Get Admin Statistics
function getStatistics() {
    $totalUsers = fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $totalBooks = fetchOne("SELECT COUNT(*) as count FROM books");
    $totalOrders = fetchOne("SELECT COUNT(*) as count FROM orders");
    $totalRevenue = fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('confirmed', 'shipped', 'delivered')");
    
    return [
        'total_users' => $totalUsers['count'] ?? 0,
        'total_books' => $totalBooks['count'] ?? 0,
        'total_orders' => $totalOrders['count'] ?? 0,
        'total_revenue' => $totalRevenue['total'] ?? 0
    ];
}

// Get Orders Per Day (Last 7 Days)
function getOrdersPerDay() {
    return fetchData(
        "SELECT DATE(created_at) as date, COUNT(*) as count FROM orders 
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
         GROUP BY DATE(created_at) ORDER BY date DESC"
    );
}

// Get Categories
function getCategories() {
    return fetchData("SELECT DISTINCT category FROM books ORDER BY category ASC");
}

// Delete User
function deleteUser($id) {
    return modifyData("DELETE FROM users WHERE id = ? AND role != 'admin'", [$id], "i");
}

// Update Order Status
function updateOrderStatus($order_id, $status) {
    return modifyData("UPDATE orders SET status = ? WHERE id = ?", [$status, $order_id], "si");
}

// Format Price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Format Date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Get Categories List
function getCategoriesList() {
    return ['Fiction', 'Non-Fiction', 'Self-Help', 'Science', 'History', 'Biography'];
}

// Cart Functions
function addToCart($user_id, $book_id, $quantity = 1) {
    // Check if item already in cart
    $existing = fetchOne("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?", [$user_id, $book_id], "ii");
    
    if ($existing) {
        // Update quantity
        $new_quantity = $existing['quantity'] + $quantity;
        return modifyData("UPDATE cart SET quantity = ? WHERE id = ?", [$new_quantity, $existing['id']], "ii");
    } else {
        // Add new item
        return modifyData("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)", [$user_id, $book_id, $quantity], "iii");
    }
}

function getCartItems($user_id) {
    return fetchData(
        "SELECT c.*, b.title, b.price, b.author, b.stock FROM cart c 
         JOIN books b ON c.book_id = b.id 
         WHERE c.user_id = ? 
         ORDER BY c.added_at DESC",
        [$user_id],
        "i"
    );
}

function getCartTotal($user_id) {
    $result = fetchOne("SELECT SUM(c.quantity * b.price) as total FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?", [$user_id], "i");
    if (!$result) return 0;
    return $result['total'] ?? 0;
}

function getCartCount($user_id) {
    $result = fetchOne("SELECT COUNT(*) as count FROM cart WHERE user_id = ?", [$user_id], "i");
    if (!$result) return 0;
    return $result['count'] ?? 0;
}

function removeFromCart($cart_id, $user_id) {
    // Verify ownership before deleting
    $cart = fetchOne("SELECT id FROM cart WHERE id = ? AND user_id = ?", [$cart_id, $user_id], "ii");
    if ($cart) {
        return modifyData("DELETE FROM cart WHERE id = ?", [$cart_id], "i");
    }
    return ['success' => false, 'error' => 'Cart item not found'];
}

function updateCartQuantity($cart_id, $quantity, $user_id) {
    if ($quantity <= 0) {
        return removeFromCart($cart_id, $user_id);
    }
    
    // Verify ownership before updating
    $cart = fetchOne("SELECT id FROM cart WHERE id = ? AND user_id = ?", [$cart_id, $user_id], "ii");
    if ($cart) {
        return modifyData("UPDATE cart SET quantity = ? WHERE id = ?", [$quantity, $cart_id], "ii");
    }
    return ['success' => false, 'error' => 'Cart item not found'];
}

function clearCart($user_id) {
    return modifyData("DELETE FROM cart WHERE user_id = ?", [$user_id], "i");
}
?>
