<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$orders = fetchData("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");

$success = '';

// Update Order Status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    $result = updateOrderStatus($order_id, $status);
    if ($result['success']) {
        $success = "Order status updated successfully!";
        header('refresh:2;url=manage-orders.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 25px;
            margin-bottom: 20px;
        }
        .order-row {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-row:last-child {
            border-bottom: none;
        }
        .order-info {
            flex: 1;
        }
        .order-id {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .order-details {
            font-size: 14px;
            color: #999;
            margin-top: 5px;
        }
        .status-badge {
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
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-control-sm {
            padding: 5px 8px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1><i class="bi bi-bag"></i> Manage Orders</h1>
        </div>
    </div>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <h5 class="mb-4"><i class="bi bi-receipt"></i> Orders</h5>
            
            <?php if (empty($orders)): ?>
                <p class="text-center text-muted">No orders found.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-row">
                        <div class="order-info">
                            <div class="order-id">Order #<?php echo $order['id']; ?></div>
                            <div class="order-details">
                                <strong><?php echo htmlspecialchars($order['username']); ?></strong> | 
                                <?php echo formatDate($order['created_at']); ?> | 
                                <?php echo formatPrice($order['total_amount']); ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                            <form method="POST" style="display: flex; gap: 5px; margin: 0;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select class="form-control form-control-sm" name="status" required>
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
