<?php
require_once 'includes/header.php';

$message = '';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['name']);
    $price = (float) $_POST['price'];
    $category = sanitize($_POST['category']);
    $brand = sanitize($_POST['brand']);
    $description = sanitize($_POST['description']);
    $image = sanitize($_POST['image']); // In real app, handle file upload
    $stock = (int) $_POST['stock'];

    // Slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, slug, category, brand, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $category, $brand, $price, $stock, $image, $description]);
        $message = "Product added successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch Products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// Stats Limits
$total_products = count($products);
$total_stock = 0;
$total_value = 0;
$low_stock = 0;
foreach ($products as $p) {
    $total_stock += $p['stock'];
    $total_value += ($p['price'] * $p['stock']);
    if ($p['stock'] < 10)
        $low_stock++;
}
?>

<header class="glass-header-admin d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold m-0" style="color: var(--text-color);">Products Management</h2>
        <p class="small m-0" style="color: var(--text-muted);">Add, edit and manage your inventory</p>
    </div>
    <div class="d-flex gap-3">
        <button class="btn btn-voltix-primary btn-sm py-2 px-4 rounded-pill" data-mdb-toggle="modal"
            data-mdb-target="#addProductModal">
            <i class="fas fa-plus me-2"></i> Add Product
        </button>
    </div>
</header>

<div class="container p-4">

    <?php if ($message): ?>
        <div class="alert alert-info py-3 px-4 border-0 mb-4 rounded-4 bg-primary bg-opacity-10 text-primary">
            <i class="fas fa-info-circle me-2"></i> <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Quick Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold">TOTAL PRODUCTS</span>
                    <i class="fas fa-box text-primary p-2 bg-primary bg-opacity-10 rounded-circle"></i>
                </div>
                <h3 class="fw-bold m-0 text-white"><?= number_format($total_products) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold">TOTAL VALUE</span>
                    <i class="fas fa-dollar-sign text-success p-2 bg-success bg-opacity-10 rounded-circle"></i>
                </div>
                <h3 class="fw-bold m-0 text-white"><?= formatPrice($total_value) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold">LOW STOCK</span>
                    <i class="fas fa-exclamation-triangle text-warning p-2 bg-warning bg-opacity-10 rounded-circle"></i>
                </div>
                <h3 class="fw-bold m-0 text-white"><?= number_format($low_stock) ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold">TOTAL STOCK</span>
                    <i class="fas fa-cubes text-info p-2 bg-info bg-opacity-10 rounded-circle"></i>
                </div>
                <h3 class="fw-bold m-0 text-white"><?= number_format($total_stock) ?></h3>
            </div>
        </div>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle user-select-none">
                <thead class="bg-light bg-opacity-10">
                    <tr>
                        <th class="ps-4">IMAGE</th>
                        <th>PRODUCT INFO</th>
                        <th>CATEGORY</th>
                        <th class="text-end">UNIT PRICE</th>
                        <th class="text-center">STOCK</th>
                        <th class="text-center pe-4">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?= htmlspecialchars($p['image']) ?>" alt="product"
                                    class="rounded-3 border border-light"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-700" style="color: var(--text-color);">
                                    <?= htmlspecialchars($p['name']) ?>
                                </div>
                                <div class="x-small text-muted"><?= htmlspecialchars($p['brand']) ?> / ID:
                                    PRD-<?= $p['id'] ?></div>
                            </td>
                            <td>
                                <span
                                    class="badge bg-light bg-opacity-10 text-muted p-2 rounded-2 fw-normal border border-light border-opacity-10">
                                    <?= htmlspecialchars($p['category']) ?>
                                </span>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                <?= formatPrice($p['price']) ?>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge rounded-pill bg-<?= $p['stock'] > 10 ? 'success' : ($p['stock'] > 0 ? 'warning' : 'danger') ?> bg-opacity-10 text-<?= $p['stock'] > 10 ? 'success' : ($p['stock'] > 0 ? 'warning' : 'danger') ?>">
                                    <?= $p['stock'] ?> In Stock
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-link btn-sm text-primary p-0"><i
                                            class="fas fa-edit fa-lg"></i></button>
                                    <button class="btn btn-link btn-sm text-danger p-0"><i
                                            class="fas fa-trash fa-lg"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted">No products found in inventory.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--surface-color); color: var(--text-color);">
            <div class="modal-header border-bottom border-light border-opacity-10 p-4">
                <h5 class="modal-title fw-bold">Add New Product</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"
                    style="<?= 'filter: invert(1); opacity: 0.5;' /* rough fix for dark mode modal close */ ?>"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="add_product" value="1">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Product Name</label>
                        <input type="text" name="name" class="form-control"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            placeholder="e.g. Voltix Pro Laptop" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Category</label>
                            <select name="category" class="form-select"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);">
                                <option>Mobiles</option>
                                <option>Laptops</option>
                                <option>Audio</option>
                                <option>Gaming</option>
                                <option>Accessories</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Brand</label>
                            <input type="text" name="brand" class="form-control"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                placeholder="Brand name" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Price (USD)</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Initial Stock</label>
                            <input type="number" name="stock" class="form-control"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Image Asset URL</label>
                        <input type="text" name="image" class="form-control"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            value="https://placehold.co/600x400" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Detailed Description</label>
                        <textarea name="description" class="form-control"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            rows="3" placeholder="Write something about the product..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-voltix-primary w-100 py-3 rounded-3 mt-2">Publish
                        Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>