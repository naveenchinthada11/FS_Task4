<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$cart_items = getCartItems($user_id);
$cart_total = getCartTotal($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (empty($cart_items)) {
        $error = 'Your cart is empty';
    } else {
        // Create order
        $result = createOrder($user_id, $cart_items);
        if ($result['success']) {
            // Clear cart
            clearCart($user_id);
            $success = 'Order placed successfully! Order ID: ' . $result['order_id'];
            $_SESSION['order_message'] = $success;
            header('Location: ' . APP_URL . '/dashboard/');
            exit;
        } else {
            $error = 'Error creating order: ' . $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container" style="min-height: 500px; margin: 40px 0;">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4"><i class="bi bi-credit-card"></i> Checkout</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="order-card">
                    <h4 class="mb-4"><i class="bi bi-receipt"></i> Order Summary</h4>
                    
                    <?php if (empty($cart_items)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Your cart is empty. <a href="<?php echo APP_URL; ?>/">Continue shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="order-items">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="order-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div>
                                        <strong style="color: #667eea;">
                                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                        </strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Subtotal:</span>
                                <span><?php echo formatPrice($cart_total); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Tax:</span>
                                <span><?php echo formatPrice($cart_total * 0.1); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #667eea; border-top: 1px solid #e9ecef; padding-top: 10px;">
                                <span>Total:</span>
                                <span><?php echo formatPrice($cart_total * 1.1); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="order-card">
                    <h4 class="mb-4"><i class="bi bi-person-check"></i> Billing Information</h4>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(getCurrentUser()['username']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars(getCurrentUser()['email']); ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" placeholder="Enter your address" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" placeholder="City" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" placeholder="Postal Code" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" required> I agree to the terms and conditions
                            </label>
                        </div>
                        
                        <?php if (!empty($cart_items)): ?>
                            <button type="submit" name="checkout" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px; font-size: 16px; font-weight: 600;">
                                <i class="bi bi-check-circle"></i> Complete Purchase
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100" disabled>
                                Cart is Empty
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?php echo APP_URL; ?>/dashboard/" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> Back to Cart
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
