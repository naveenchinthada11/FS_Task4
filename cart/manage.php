<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$action = sanitize($_GET['action'] ?? '');
$response = ['success' => false];

if ($action === 'add') {
    $book_id = intval($_POST['book_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if ($book_id > 0) {
        $book = getBookById($book_id);
        if ($book && $book['stock'] > 0) {
            $result = addToCart($_SESSION['user_id'], $book_id, $quantity);
            $response = array_merge($result, ['cart_count' => getCartCount($_SESSION['user_id'])]);
        } else {
            $response['error'] = 'Book not available';
        }
    }
}

elseif ($action === 'remove') {
    $cart_id = intval($_POST['cart_id'] ?? 0);
    if ($cart_id > 0) {
        $result = removeFromCart($cart_id, $_SESSION['user_id']);
        $response = array_merge($result, ['cart_count' => getCartCount($_SESSION['user_id'])]);
    }
}

elseif ($action === 'update') {
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($cart_id > 0) {
        $result = updateCartQuantity($cart_id, $quantity, $_SESSION['user_id']);
        $response = array_merge($result, [
            'cart_count' => getCartCount($_SESSION['user_id']),
            'cart_total' => getCartTotal($_SESSION['user_id'])
        ]);
    }
}

elseif ($action === 'get') {
    $items = getCartItems($_SESSION['user_id']);
    $response = [
        'success' => true,
        'items' => $items,
        'total' => getCartTotal($_SESSION['user_id']),
        'count' => getCartCount($_SESSION['user_id'])
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
