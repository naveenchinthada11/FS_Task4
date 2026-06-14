<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$orders = getUserOrders($_SESSION['user_id']);
$cart_items = getCartItems($_SESSION['user_id']);
$cart_total = getCartTotal($_SESSION['user_id']);
// Featured books for dashboard
$featured_books = getBooks(1, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border-left: 4px solid #5b6bd1;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .stats-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .stats-card h5 {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stats-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .order-id {
            font-weight: bold;
            color: #333;
        }
        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #cfe2ff;
            color: #084298;
        }
        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-delivered {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .order-items {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        /* Books grid */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 18px;
        }
        .book-card {
            background: white;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .book-card:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(0,0,0,0.09); }
        .book-cover { height: 160px; width: 100%; object-fit: cover; border-radius: 6px; margin-bottom: 10px; }
        .book-title { font-size: 14px; font-weight: 600; color: #333; }
        .book-author { font-size: 12px; color: #777; }
        .no-orders {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
        }
        .btn-view:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
            text-decoration: none;
        }
        .cart-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .qty-btn {
            padding: 3px 8px;
            font-size: 12px;
        }
        .qty-input {
            padding: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="dashboard-header">
        <div class="container">
            <h1><i class="bi bi-speedometer2"></i> My Dashboard</h1>
            <p class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <h5><i class="bi bi-bag"></i> Total Orders</h5>
                    <div class="number"><?php echo count($orders); ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h5><i class="bi bi-check-circle"></i> Delivered</h5>
                    <div class="number">
                        <?php echo count(array_filter($orders, function($o) { return $o['status'] === 'delivered'; })); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h5><i class="bi bi-hourglass-split"></i> Pending</h5>
                    <div class="number">
                        <?php echo count(array_filter($orders, function($o) { return $o['status'] === 'pending'; })); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h5><i class="bi bi-currency-dollar"></i> Total Spent</h5>
                    <div class="number">
                        <?php echo formatPrice(array_sum(array_column($orders, 'total_amount'))); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="mb-4"><i class="bi bi-cart"></i> Shopping Cart</h2>
                
                <?php if (empty($cart_items)): ?>
                    <div class="order-card no-orders">
                        <i class="bi bi-cart-x" style="font-size: 48px; color: #ddd;"></i>
                        <p class="mt-3">Your cart is empty. <a href="<?php echo APP_URL; ?>/">Continue shopping</a></p>
                    </div>
                <?php else: ?>
                    <div class="order-card">
                        <div class="order-items">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="order-item cart-item-row" id="cart-item-<?php echo $item['id']; ?>">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                                        <small class="text-muted">By <?php echo htmlspecialchars($item['author']); ?></small>
                                    </div>
                                    <div style="display: flex; gap: 15px; align-items: center;">
                                        <div>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary qty-btn" data-cart-id="<?php echo $item['id']; ?>" data-action="decrease">-</button>
                                                <input type="number" class="form-control text-center qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                                                <button class="btn btn-outline-secondary qty-btn" data-cart-id="<?php echo $item['id']; ?>" data-action="increase">+</button>
                                            </div>
                                        </div>
                                        <div>
                                            <strong style="color: #667eea; font-weight: bold;">
                                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                            </strong>
                                        </div>
                                        <button class="btn btn-sm btn-danger remove-cart-btn" data-cart-id="<?php echo $item['id']; ?>">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <strong style="font-size: 18px;">Total: <span style="color: #667eea;"><?php echo formatPrice($cart_total); ?></span></strong>
                                <button class="btn btn-primary" id="checkout-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <i class="bi bi-credit-card"></i> Proceed to Checkout
                                </button>
                            </div>
                            <a href="<?php echo APP_URL; ?>/" class="btn btn-outline-secondary" style="width: 100%; margin-top: 10px;">
                                <i class="bi bi-shop"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="mb-4"><i class="bi bi-receipt"></i> My Orders</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="order-card no-orders">
                        <i class="bi bi-inbox" style="font-size: 48px; color: #ddd;"></i>
                        <p class="mt-3">No orders yet. <a href="<?php echo APP_URL; ?>/">Start shopping</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                                    <small class="text-muted"><?php echo formatDate($order['created_at']); ?></small>
                                </div>
                                <div>
                                    <span class="order-status status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-items">
                                <?php 
                                $order_items = fetchData(
                                    "SELECT oi.*, b.title FROM order_items oi 
                                     JOIN books b ON oi.book_id = b.id 
                                     WHERE oi.order_id = ?",
                                    [$order['id']],
                                    "i"
                                );
                                ?>
                                <?php foreach ($order_items as $item): ?>
                                    <div class="order-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['title']); ?></strong><br>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <div>
                                            <strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong>Total: <?php echo formatPrice($order['total_amount']); ?></strong>
                                </div>
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="mb-4"><i class="bi bi-book"></i> Featured Books</h2>
                <?php if (empty($featured_books)): ?>
                    <div class="order-card no-orders">
                        <i class="bi bi-book-half" style="font-size: 48px; color: #ddd;"></i>
                        <p class="mt-3">No books available right now. <a href="<?php echo APP_URL; ?>/">Visit catalog</a></p>
                    </div>
                <?php else: ?>
                    <div class="books-grid">
                        <?php foreach ($featured_books as $book): ?>
                            <div class="book-card">
                                <?php $img = $book['cover_image'] ? rawurlencode($book['cover_image']) : 'placeholder.png'; ?>
                                <img src="<?php echo APP_URL; ?>/uploads/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-cover">
                                <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                                <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                                <div style="margin-top:8px;">
                                    <strong style="color:#5b6bd1;"><?php echo formatPrice($book['price']); ?></strong>
                                </div>
                                <div style="margin-top:8px;">
                                    <a href="<?php echo APP_URL; ?>/" class="btn btn-sm btn-outline-secondary">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Remove from cart
        document.querySelectorAll('.remove-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Remove this item from cart?')) {
                    const cartId = this.dataset.cartId;
                    const formData = new FormData();
                    formData.append('cart_id', cartId);
                    
                    fetch('<?php echo APP_URL; ?>/cart/manage.php?action=remove', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                }
            });
        });
        
        // Update quantity
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const cartId = this.dataset.cartId;
                const action = this.dataset.action;
                const input = this.parentElement.querySelector('.qty-input');
                let quantity = parseInt(input.value);
                
                if (action === 'increase') {
                    quantity++;
                } else if (action === 'decrease' && quantity > 1) {
                    quantity--;
                }
                
                const formData = new FormData();
                formData.append('cart_id', cartId);
                formData.append('quantity', quantity);
                
                fetch('<?php echo APP_URL; ?>/cart/manage.php?action=update', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });
        
        // Checkout
        document.getElementById('checkout-btn')?.addEventListener('click', function() {
            window.location.href = '<?php echo APP_URL; ?>/checkout/';
        });
    </script>
</body>
</html>
