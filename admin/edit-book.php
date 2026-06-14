<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$book = getBookById($book_id);

if (!$book) {
    header('Location: manage-books.php');
    exit;
}

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $author = sanitize($_POST['author'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    
    if (empty($title) || empty($author) || empty($category) || $price <= 0) {
        $errors[] = "All fields are required";
    }
    
    if (empty($errors)) {
        $result = updateBook($book_id, $title, $author, $category, $price, $description, $stock);
        if ($result['success']) {
            $success = "Book updated successfully!";
            $book = getBookById($book_id);
        } else {
            $errors[] = "Update failed. Please try again.";
        }
    }
}

$categories = getCategoriesList();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Admin</title>
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
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: bold;
            width: 100%;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
        }
        .btn-back {
            color: #667eea;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="admin-header">
        <div class="container">
            <h1><i class="bi bi-pencil-square"></i> Edit Book</h1>
        </div>
    </div>
    
    <div class="container">
        <div class="form-card">
            <a href="manage-books.php" class="btn-back"><i class="bi bi-arrow-left"></i> Back</a>
            
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Author</label>
                    <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $book['category'] === $cat ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control" name="price" step="0.01" value="<?php echo htmlspecialchars($book['price']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" class="form-control" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($book['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle"></i> Update Book
                </button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
