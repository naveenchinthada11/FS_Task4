<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderWithItems($order_id);

if (!$order || $order['user_id'] !== $_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .order-details-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        .order-item-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-item-row:last-child {
            border-bottom: none;
        }
        .item-title {
            font-weight: 600;
            color: #333;
        }
        .item-author {
            font-size: 14px;
            color: #999;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 16px;
        }
        .summary-row.total {
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
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
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="page-header">
        <div class="container">
            <a href="index.php" class="text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Back</a>
            <h1 class="mt-2">Order #<?php echo $order['id']; ?></h1>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="order-details-card">
                    <h5 class="mb-4"><i class="bi bi-receipt"></i> Order Items</h5>
                    
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item-row">
                            <div>
                                <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                <div class="item-author">By <?php echo htmlspecialchars($item['author']); ?></div>
                            </div>
                            <div style="text-align: right;">
                                <div>Qty: <?php echo $item['quantity']; ?></div>
                                <div class="item-title"><?php echo formatPrice($item['price']); ?> each</div>
                                <div style="color: #667eea; font-weight: bold;">
                                    <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 20px;">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span><?php echo formatPrice($order['total_amount']); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>FREE</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span><?php echo formatPrice($order['total_amount']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="order-details-card">
                    <h5 class="mb-4"><i class="bi bi-info-circle"></i> Order Information</h5>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="color: #999; font-size: 12px; text-transform: uppercase;">Order ID</label>
                        <div style="font-weight: bold; color: #333; font-size: 16px;">#<?php echo $order['id']; ?></div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="color: #999; font-size: 12px; text-transform: uppercase;">Status</label>
                        <div style="margin-top: 8px;">
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="color: #999; font-size: 12px; text-transform: uppercase;">Order Date</label>
                        <div style="font-weight: 600; color: #333;">
                            <?php echo formatDate($order['created_at']); ?>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="color: #999; font-size: 12px; text-transform: uppercase;">Total Amount</label>
                        <div style="font-weight: bold; color: #667eea; font-size: 18px;">
                            <?php echo formatPrice($order['total_amount']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
