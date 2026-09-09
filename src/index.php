<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Fetch Featured Products
$featured_products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE is_featured = 1 LIMIT 4");
    $featured_products = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silent fail or log
}
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="hero-title animate__animated animate__fadeInDown">VOLTIX — Power Your Digital Life</h1>
                <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                    Discover the latest in premium electronics. From next-gen smartphones to immersive audio.
                </p>
                <a href="products.php"
                    class="btn btn-voltix-primary btn-lg animate__animated animate__zoomIn animate__delay-1s">
                    Explore Electronics <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Floating Elements (Decorative) -->
    <div style="position: absolute; top: 20%; left: 5%; opacity: 0.1; transform: rotate(-15deg);">
        <i class="fas fa-laptop fa-6x text-white"></i>
    </div>
    <div style="position: absolute; bottom: 20%; right: 5%; opacity: 0.1; transform: rotate(15deg);">
        <i class="fas fa-mobile-alt fa-6x text-white"></i>
    </div>
</div>



<!-- Featured Products -->
<div class="container my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold text-white mb-0 section-title">Featured Products</h2>
        <a href="products.php" class="btn btn-outline-light btn-rounded">View All</a>
    </div>

    <div class="row g-4">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
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
                <p class="text-muted">No featured products found. Please run the database setup.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Why Choose Voltix -->
<div class="py-5" style="background-color: var(--bg-color);">
    <div class="container my-4">
        <h2 class="text-center mb-5 fw-bold text-white section-title">Why Choose Voltix?</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-rocket fa-3x mb-4 text-warning"></i>
                    <h4>Fast Delivery</h4>
                    <p>Get your gadgets delivered lightning fast with our premium express shipping network.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-shield-alt fa-3x mb-4 text-success"></i>
                    <h4>Secure Payments</h4>
                    <p>Your transactions are 100% secure with our military-grade encrypted payment gateways.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-headset fa-3x mb-4 text-info"></i>
                    <h4>24/7 Support</h4>
                    <p>Our expert technical support team is here to help you anytime, anywhere, 365 days a year.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>