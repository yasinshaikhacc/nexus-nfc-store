<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$where = "WHERE status = 'active'";
$params = [];

if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products $where ORDER BY created_at DESC");
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>

<div class="container my-5 py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Our Products</h2>
        <p class="text-muted">Explore our premium collection of digital devices.</p>
    </div>

    <!-- Category Filter (Simple) -->
    <div class="d-flex justify-content-center mb-5 overflow-auto pb-2">
        <?php
        $cats = ['Mobiles', 'Laptops', 'Audio', 'Wearables', 'Gaming', 'Accessories'];
        ?>
        <a href="products.php"
            class="btn btn-voltix-outline rounded-pill mx-1 <?= $category == '' ? 'active' : '' ?>">All</a>
        <?php foreach ($cats as $cat): ?>
            <a href="products.php?category=<?= $cat ?>"
                class="btn btn-voltix-outline rounded-pill mx-1 <?= $category == $cat ? 'active' : '' ?>">
                <?= $cat ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0">
                        <!-- Discount Badge -->
                        <?php if ($product['sale_price']): ?>
                            <div class="position-absolute top-0 start-0 m-3 z-3">
                                <span class="badge bg-danger rounded-pill shadow-sm">SALE</span>
                            </div>
                        <?php endif; ?>

                        <div class="overflow-hidden p-3 text-center">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top rounded-4"
                                alt="<?= htmlspecialchars($product['name']) ?>" style="height: 220px; object-fit: contain;">
                        </div>

                        <div class="card-body">
                            <h6 class="text-secondary text-uppercase small letter-spacing-1 mb-1">
                                <?= htmlspecialchars($product['category']) ?>
                            </h6>
                            <h5 class="card-title text-truncate mb-2">
                                <?= htmlspecialchars($product['name']) ?>
                            </h5>

                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <div>
                                    <span class="price-tag">
                                        <?= formatPrice($product['sale_price'] ?? $product['price']) ?>
                                    </span>
                                    <?php if ($product['sale_price']): ?>
                                        <small class="text-muted text-decoration-line-through ms-1">
                                            <?= formatPrice($product['price']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <form action="cart.php" method="post" class="d-grid" onsubmit="handleCartAction(event)">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-voltix-primary btn-sm">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products found in this category.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>