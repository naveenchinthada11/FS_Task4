<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$books = getBooks($page);
$total_books = getTotalBooks();
$total_pages = ceil($total_books / 12);

$success = '';
$errors = [];

// Delete Book
if (isset($_GET['delete'])) {
    $book_id = (int)$_GET['delete'];
    $result = deleteBook($book_id);
    if ($result['success']) {
        $success = "Book deleted successfully!";
        header('refresh:2;url=manage-books.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin</title>
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
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 6px;
            padding: 10px 20px;
        }
        .btn-add:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
            text-decoration: none;
        }
        .btn-edit {
            background: #28a745;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-edit:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        .btn-delete {
            background: #dc3545;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-delete:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }
        table {
            margin-top: 20px;
        }
        .table-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .pagination {
            margin-top: 20px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1><i class="bi bi-book"></i> Manage Books</h1>
                </div>
                <a href="add-book.php" class="btn btn-add"><i class="bi bi-plus-circle"></i> Add New Book</a>
            </div>
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
            <h5 class="mb-4"><i class="bi bi-table"></i> Books List</h5>
            
            <?php if (empty($books)): ?>
                <p class="text-center text-muted">No books found.</p>
            <?php else: ?>
                <table class="table table-hover">
                    <thead class="table-header">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo htmlspecialchars(substr($book['title'], 0, 30)); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['category']); ?></td>
                                <td><?php echo formatPrice($book['price']); ?></td>
                                <td><?php echo $book['stock']; ?></td>
                                <td>
                                    <a href="edit-book.php?id=<?php echo $book['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="?delete=<?php echo $book['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
