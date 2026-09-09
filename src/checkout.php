<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    setFlashMessage('info', 'Please login to complete your purchase.');
    redirect('login.php');
}

if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Calculate Total
$totalAmount = 0;
$cartItems = [];
if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
    foreach ($products as $p) {
        $subtotal = ($p['sale_price'] ?? $p['price']) * $cart[$p['id']];
        $totalAmount += $subtotal;
        $p['qty'] = $cart[$p['id']];
        $cartItems[] = $p;
    }
}
$tax = $totalAmount * 0.1;
$finalTotal = $totalAmount + $tax;

// Process Order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = sanitize($_POST['address'] . ', ' . $_POST['city'] . ', ' . $_POST['zip']);
    $payment_method = $_POST['paymentMethod'];

    try {
        $pdo->beginTransaction();

        // Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, payment_status) VALUES (?, ?, ?, ?, 'paid')");
        $stmt->execute([$user_id, $finalTotal, $address, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Create Order Items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($cartItems as $item) {
            $price = $item['sale_price'] ?? $item['price'];
            $stmtItem->execute([$order_id, $item['id'], $item['qty'], $price]);

            // Reduce Stock (Optional but good)
            $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmtStock->execute([$item['qty'], $item['id']]);
        }

        $pdo->commit();

        // Clear Cart
        $_SESSION['cart'] = [];

        redirect('profile.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Order processing failed: " . $e->getMessage();
    }
}
?>

<div class="container my-5 pt-4">
    <h2 class="text-white mb-4">Checkout</h2>

    <div class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Your cart</span>
                <span class="badge bg-primary rounded-pill">
                    <?= count($cart) ?>
                </span>
            </h4>
            <ul class="list-group list-group-flush mb-3 rounded-3">
                <?php foreach ($cartItems as $item): ?>
                    <li
                        class="list-group-item d-flex justify-content-between lh-sm bg-transparent text-white border-bottom border-secondary">
                        <div>
                            <h6 class="my-0">
                                <?= htmlspecialchars($item['name']) ?>
                            </h6>
                            <small class="text-muted">Qty:
                                <?= $item['qty'] ?>
                            </small>
                        </div>
                        <span class="text-muted">
                            <?= formatPrice(($item['sale_price'] ?? $item['price']) * $item['qty']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>

                <li class="list-group-item d-flex justify-content-between bg-transparent text-white border-0">
                    <span>Total (USD)</span>
                    <strong class="text-gradient">
                        <?= formatPrice($finalTotal) ?>
                    </strong>
                </li>
            </ul>
        </div>

        <div class="col-md-7 col-lg-8">
            <h4 class="mb-3 text-white">Billing address</h4>
            <form class="needs-validation" method="POST">

                <div class="col-12 mb-3">
                    <label for="address" class="form-label text-white-50">Address</label>
                    <input type="text" class="form-control" name="address" placeholder="1234 Main St" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label text-white-50">City</label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="col-md-3">
                        <label for="zip" class="form-label text-white-50">Zip</label>
                        <input type="text" class="form-control" name="zip" required>
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <h4 class="mb-3 text-white">Payment</h4>

                <div class="my-3">
                    <div class="form-check">
                        <input id="credit" name="paymentMethod" type="radio" class="form-check-input"
                            value="credit_card" checked required>
                        <label class="form-check-label text-white" for="credit">Credit card</label>
                    </div>
                    <div class="form-check">
                        <input id="debit" name="paymentMethod" type="radio" class="form-check-input" value="debit_card"
                            required>
                        <label class="form-check-label text-white" for="debit">Debit card</label>
                    </div>
                    <div class="form-check">
                        <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" value="paypal"
                            required>
                        <label class="form-check-label text-white" for="paypal">PayPal</label>
                    </div>
                </div>

                <!-- Mock Payment Form -->
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cc-name" class="form-label text-white-50">Name on card</label>
                        <input type="text" class="form-control" id="cc-name" placeholder="" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cc-number" class="form-label text-white-50">Credit card number</label>
                        <input type="text" class="form-control" id="cc-number" placeholder="" required>
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <button class="btn btn-voltix-primary btn-lg w-100" type="submit">Place Order</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>