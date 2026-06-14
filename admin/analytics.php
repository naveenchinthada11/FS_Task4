<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$stats = getStatistics();
$orders_per_day = getOrdersPerDay();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.js"></script>
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
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border-top: 4px solid #667eea;
        }
        .stat-card h6 {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1><i class="bi bi-graph-up"></i> Analytics</h1>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="stat-card">
                    <h6><i class="bi bi-people-fill"></i> Total Users</h6>
                    <div class="number"><?php echo $stats['total_users']; ?></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="stat-card">
                    <h6><i class="bi bi-bag-fill"></i> Total Orders</h6>
                    <div class="number"><?php echo $stats['total_orders']; ?></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="stat-card">
                    <h6><i class="bi bi-book-fill"></i> Total Books</h6>
                    <div class="number"><?php echo $stats['total_books']; ?></div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="stat-card">
                    <h6><i class="bi bi-currency-dollar"></i> Total Revenue</h6>
                    <div class="number"><?php echo formatPrice($stats['total_revenue']); ?></div>
                </div>
            </div>
        </div>
        
        <div class="chart-card">
            <h5 class="mb-4"><i class="bi bi-graph-up"></i> Orders Per Day (Last 7 Days)</h5>
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chartData = {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . $item['date'] . "'"; }, $orders_per_day)); ?>],
            datasets: [{
                label: 'Orders',
                data: [<?php echo implode(',', array_map(function($item) { return $item['count']; }, $orders_per_day)); ?>],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        };
        
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
