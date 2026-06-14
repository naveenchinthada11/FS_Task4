<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'newest');

$books = getBooks($page, 12, $search, $category, $sort);
$total_books = getTotalBooks($search, $category);
$total_pages = ceil($total_books / 12);
$all_categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .hero h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .search-box {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
        }
        .search-box input {
            padding: 12px;
            border: none;
            border-radius: 6px;
            flex: 1;
        }
        .search-box button {
            background: white;
            border: none;
            color: #667eea;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #f0f0f0;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .book-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            margin-bottom: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .book-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }
        .book-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            position: relative;
            overflow: hidden;
        }
        .book-image::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path d="M50 50 L50 150 L150 150 L150 50 Z" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/><path d="M60 60 L60 140 L140 140 L140 60" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1"/></svg>');
        }
        .book-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .book-title {
            font-weight: bold;
            color: #333;
            font-size: 16px;
            margin-bottom: 8px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .book-author {
            color: #999;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .book-category {
            display: inline-block;
            background: #f0f0f0;
            color: #667eea;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 12px;
            width: fit-content;
        }
        .book-description {
            color: #666;
            font-size: 13px;
            margin-bottom: 12px;
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .book-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
        }
        .book-price {
            font-weight: bold;
            color: #667eea;
            font-size: 18px;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
        }
        .btn-view:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
            text-decoration: none;
        }
        .pagination {
            margin-top: 40px;
            justify-content: center;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-results i {
            font-size: 64px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1><i class="bi bi-book-fill"></i> Welcome to Our Bookstore</h1>
            <p>Discover thousands of books and find your next favorite read</p>
            
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search books by title or author..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="bi bi-search"></i> Search</button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container" id="books">
        <!-- Filters -->
        <div class="filters">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" placeholder="Title or author..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($all_categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort By</label>
                    <select class="form-control" name="sort">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Apply Filters</button>
                </div>
            </form>
        </div>
        
        <!-- Books Grid -->
        <?php if (empty($books)): ?>
            <div class="no-results">
                <i class="bi bi-inbox"></i>
                <h5 style="margin-top: 20px;">No Books Found</h5>
                <p>Try adjusting your search or filter criteria</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($books as $book): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="book-card">
                            <div class="book-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <?php
                                    $imgSrc = '';
                                    if (!empty($book['cover_image'])) {
                                        $imgSrc = filter_var($book['cover_image'], FILTER_VALIDATE_URL) ? $book['cover_image'] : APP_URL . '/uploads/' . ltrim($book['cover_image'], '/');
                                    }
                                ?>
                                <?php if (!empty($imgSrc)): ?>
                                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-book"></i>
                                <?php endif; ?>
                            </div>
                            <div class="book-content">
                                <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                                <div class="book-author">By <?php echo htmlspecialchars($book['author']); ?></div>
                                <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>
                                <div class="book-description"><?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?></div>
                                <div class="book-footer">
                                    <div class="book-price"><?php echo formatPrice($book['price']); ?></div>
                                    <a href="#" class="btn-view" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $book['id']; ?>">
                                        View <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category); ?>&sort=<?php echo htmlspecialchars($sort); ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category); ?>&sort=<?php echo htmlspecialchars($sort); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($category); ?>&sort=<?php echo htmlspecialchars($sort); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Book Detail Modals -->
    <?php foreach ($books as $book): ?>
        <div class="modal fade" id="bookModal<?php echo $book['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                            <div class="book-image" style="height: 250px; margin-bottom: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <?php
                                                    $modalImg = '';
                                                    if (!empty($book['cover_image'])) {
                                                        $modalImg = filter_var($book['cover_image'], FILTER_VALIDATE_URL) ? $book['cover_image'] : APP_URL . '/uploads/' . ltrim($book['cover_image'], '/');
                                                    }
                                                ?>
                                                <?php if (!empty($modalImg)): ?>
                                                    <img src="<?php echo htmlspecialchars($modalImg); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="bi bi-book" style="font-size: 80px;"></i>
                                                <?php endif; ?>
                                            </div>
                            </div>
                            <div class="col-md-8">
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                                <p><strong>Category:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($book['category']); ?></span></p>
                                <p><strong>Price:</strong> <span style="color: #667eea; font-size: 20px; font-weight: bold;"><?php echo formatPrice($book['price']); ?></span></p>
                                <p><strong>Stock:</strong> 
                                    <?php if ($book['stock'] > 0): ?>
                                        <span class="badge bg-success"><?php echo $book['stock']; ?> Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Description:</strong></p>
                                <p><?php echo htmlspecialchars($book['description']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <?php if (isLoggedIn()): ?>
                            <?php if ($book['stock'] > 0): ?>
                                <div class="input-group" style="width: 150px;">
                                    <input type="number" class="form-control" id="qty_<?php echo $book['id']; ?>" value="1" min="1" max="<?php echo $book['stock']; ?>">
                                    <button class="btn btn-primary add-to-cart-btn" data-book-id="<?php echo $book['id']; ?>" data-book-title="<?php echo htmlspecialchars($book['title']); ?>">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Out of Stock</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-primary">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add to Cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const bookId = this.dataset.bookId;
                const bookTitle = this.dataset.bookTitle;
                const qtyInput = document.getElementById('qty_' + bookId);
                const quantity = parseInt(qtyInput.value);
                
                const formData = new FormData();
                formData.append('book_id', bookId);
                formData.append('quantity', quantity);
                
                fetch('<?php echo APP_URL; ?>/cart/manage.php?action=add', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(bookTitle + ' added to cart!');
                        document.querySelector('[data-bs-dismiss="modal"]').click();
                    } else {
                        alert('Error adding to cart');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>
</body>
</html>
