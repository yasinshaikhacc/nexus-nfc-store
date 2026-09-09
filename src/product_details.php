<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        $product = null;
    }
}

if (!$product) {
    echo "<div class='container my-5 text-center'><h2 class='text-white'>Product not found.</h2><a href='products.php' class='btn btn-primary mt-3'>Back to Products</a></div>";
    require_once '../includes/footer.php';
    exit();
}
?>

<div class="container my-5 pt-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-muted">Products</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">
                <?= htmlspecialchars($product['name']) ?>
            </li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-6">
            <div class="glass-card p-5 border-0 text-center position-relative overflow-hidden">
                <div class="position-absolute top-50 start-50 translate-middle"
                    style="width: 300px; height: 300px; background: var(--primary-color); filter: blur(120px); opacity: 0.1;">
                </div>
                <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded-4 position-relative z-1"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    style="max-height: 500px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
            </div>

            <!-- Thumbnails -->
            <div class="d-flex justify-content-center mt-3 gap-3 opacity-75">
                <div class="p-1 rounded-3 border border-primary glass-card cursor-pointer">
                    <img src="<?= htmlspecialchars($product['image']) ?>" class="rounded-2" width="60" height="60">
                </div>
                <div class="p-1 rounded-3 glass-card cursor-pointer" style="opacity: 0.5">
                    <img src="<?= htmlspecialchars($product['image']) ?>" class="rounded-2" width="60" height="60">
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <h6 class="text-uppercase fw-bold letter-spacing-2 text-primary mb-2">
                    <?= htmlspecialchars($product['brand']) ?>
                </h6>
                <h1 class="display-4 fw-bold mb-3 text-gradient">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-muted small border-start border-secondary ps-3 ms-2">150 Reviews</span>
                    <span class="text-muted small border-start border-secondary ps-3 ms-3">
                        Category: <span class="text-white"><?= htmlspecialchars($product['category']) ?></span>
                    </span>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <h2 class="display-5 fw-bold text-white mb-0 me-3">
                        <?= formatPrice($product['sale_price'] ?? $product['price']) ?>
                    </h2>
                    <?php if ($product['sale_price']): ?>
                        <span class="text-decoration-line-through text-muted fs-4">
                            <?= formatPrice($product['price']) ?>
                        </span>
                        <span
                            class="badge bg-danger ms-3 px-3 py-2 rounded-pill">-<?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>%</span>
                    <?php endif; ?>
                </div>

                <p class="text-muted lead mb-5" style="line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <!-- Actions -->
                <form onsubmit="addToCart(event, <?= $product['id'] ?>)" class="mb-4">
                    <div class="row g-3">
                        <div class="col-4 col-sm-3">
                            <label class="form-label text-muted small fw-bold mb-1">Quantity</label>
                            <input type="number" name="quantity"
                                class="form-control form-control-lg text-center fw-bold"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                value="1" min="1" max="<?= $product['stock'] ?>" />
                        </div>
                        <div class="col-8 col-sm-9 d-flex align-items-end">
                            <button type="submit"
                                class="btn btn-voltix-primary btn-lg w-100 py-3 rounded-pill shadow-lg">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-4 text-muted small">
                    <span><i class="fas fa-check-circle text-success me-2"></i> In Stock</span>
                    <span><i class="fas fa-truck text-primary me-2"></i> Free Shipping</span>
                    <span><i class="fas fa-shield-alt text-warning me-2"></i> 2 Year Warranty</span>
                </div>

                <hr class="my-5" style="border-color: var(--glass-border);">

                <!-- Specs -->
                <?php $specs = json_decode($product['specifications'] ?? '{}', true); ?>
                <?php if ($specs): ?>
                    <h5 class="fw-bold text-white mb-4">Technical Specifications</h5>
                    <div class="row g-3">
                        <?php foreach ($specs as $key => $val): ?>
                            <div class="col-6 mb-2">
                                <div class="p-3 glass-card rounded-4 border-0">
                                    <small
                                        class="text-muted d-block text-uppercase letter-spacing-1 mb-1"><?= htmlspecialchars($key) ?></small>
                                    <span class="fw-bold text-white"><?= htmlspecialchars($val) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="mt-5 pt-5">
        <h3 class="text-white fw-bold mb-4">You might also like</h3>
        <!-- Placeholder for related products -->
        <p class="text-muted">Related products will look just like the main grid.</p>
    </div>
</div>

<script>
    // Initialize MDB inputs
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
    });
</script>

<?php require_once '../includes/footer.php'; ?>