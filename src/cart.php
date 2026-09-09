<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = (int) $_POST['product_id'];

    if ($_POST['action'] === 'add') {
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }

        setFlashMessage('success', 'Product added to cart!');
    } elseif ($_POST['action'] === 'update') {
        $quantity = (int) $_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        setFlashMessage('info', 'Cart updated!');
    } elseif ($_POST['action'] === 'remove') {
        unset($_SESSION['cart'][$product_id]);
        setFlashMessage('warning', 'Item removed from cart!');
    }

    // AJAX Check
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Calculate totals
        $new_cart_count = 0;
        $total_price = 0;
        $item_subtotal = 0;

        if (!empty($_SESSION['cart'])) {
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
            $products = $stmt->fetchAll();
            foreach ($products as $p) {
                $qty = $_SESSION['cart'][$p['id']];
                $price = $p['sale_price'] ?? $p['price'];
                $sub = $price * $qty;
                $total_price += $sub;
                $new_cart_count += $qty;
                if ($p['id'] == $product_id) {
                    $item_subtotal = $sub;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $new_cart_count,
            'item_subtotal' => formatPrice($item_subtotal),
            'total_price' => formatPrice($total_price),
            'total_with_tax' => formatPrice($total_price * 1.1),
            'tax' => formatPrice($total_price * 0.1)
        ]);
        exit();
    }

    // Standard redirect to avoid form resubmission
    redirect('cart.php');
}

require_once '../includes/header.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch Cart Products
$cartProducts = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    try {
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
        $products = $stmt->fetchAll();

        foreach ($products as $p) {
            $p['qty'] = $_SESSION['cart'][$p['id']];
            $p['subtotal'] = ($p['sale_price'] ?? $p['price']) * $p['qty'];
            $totalPrice += $p['subtotal'];
            $cartProducts[] = $p;
        }
    } catch (PDOException $e) {
        // Handle error
    }
}
?>

<div class="container my-5 pt-4">
    <h2 class="fw-bold mb-4 text-gradient">Shopping Cart</h2>

    <?php
    $flash = getFlashMessage();
    if ($flash):
        ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show border-0 bg-<?= $flash['type'] ?> bg-opacity-10 text-<?= $flash['type'] ?> rounded-4 mb-4 shadow-sm"
            role="alert">
            <i class="fas fa-info-circle me-2"></i> <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (!empty($cartProducts)): ?>
            <div class="col-lg-8">
                <div class="glass-card border-0 overflow-hidden">
                    <div class="card-body p-4">
                        <?php foreach ($cartProducts as $item): ?>
                            <div
                                class="row align-items-center mb-4 border-bottom border-light border-opacity-10 pb-4 last:border-0">
                                <div class="col-md-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded-4 shadow-sm"
                                        alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <h5 class="fw-bold text-white mb-1">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </h5>
                                    <p class="text-muted small mb-0 text-uppercase letter-spacing-1">
                                        <?= htmlspecialchars($item['brand']) ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center justify-content-md-center">
                                        <form action="cart.php" method="POST"
                                            class="d-flex align-items-center glass-card rounded-pill p-1"
                                            onsubmit="handleCartAction(event)">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-link text-white px-2 py-1 shadow-none"
                                                    onclick="updateQty(this, -1)"><i class="fas fa-minus small"></i></button>
                                                <input type="number" name="quantity" value="<?= $item['qty'] ?>" min="1"
                                                    class="form-control text-center text-white bg-transparent border-0 qty-input p-0 fw-bold"
                                                    style="width: 40px;"
                                                    onchange="this.form.dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}))">
                                                <button type="button" class="btn btn-link text-white px-2 py-1 shadow-none"
                                                    onclick="updateQty(this, 1)"><i class="fas fa-plus small"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <h5 class="fw-bold text-gradient mb-2">
                                        <?= formatPrice($item['subtotal']) ?>
                                    </h5>
                                    <form action="cart.php" method="POST" onsubmit="handleCartAction(event)">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit"
                                            class="btn btn-link text-danger p-0 text-decoration-none small opacity-75 hover-opacity-100">
                                            <i class="fas fa-trash-alt me-1"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass-card border-0 p-4">
                    <h5 class="fw-bold text-white mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Subtotal</span>
                        <span class="text-white"><?= formatPrice($totalPrice) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Shipping</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Tax (Estimated)</span>
                        <span class="text-white"><?= formatPrice($totalPrice * 0.1) ?></span>
                    </div>
                    <hr class="border-light border-opacity-10 my-4">
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5 text-white">Total</strong>
                        <strong class="fs-4 text-gradient"><?= formatPrice($totalPrice * 1.1) ?></strong>
                    </div>

                    <a href="checkout.php" class="btn btn-voltix-primary w-100 btn-lg mb-3 rounded-pill shadow-lg">
                        Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <a href="products.php" class="btn btn-outline-light w-100 rounded-pill">Continue Shopping</a>
                </div>
            </div>

        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="glass-card d-inline-block p-5 rounded-circle mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted opacity-50"></i>
                </div>
                <h3 class="fw-bold text-white mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4 lead">Looks like you haven't added anything to your cart yet.</p>
                <a href="products.php" class="btn btn-voltix-primary btn-lg rounded-pill px-5">
                    Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateQty(btn, delta) {
        const input = btn.parentElement.querySelector('.qty-input');
        let newValue = parseInt(input.value) + delta;
        if (newValue < 1) newValue = 1;
        input.value = newValue;
        input.dispatchEvent(new Event('change'));
    }
</script>

<?php require_once '../includes/footer.php'; ?>