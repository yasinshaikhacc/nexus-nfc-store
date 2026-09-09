<?php
require_once '../config/db.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$orders = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log error
}
?>

<div class="container my-5 py-5">
    <h2 class="text-white mb-4">My Orders</h2>

    <div class="row">
        <div class="col-12">
            <?php if (!empty($orders)): ?>
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0 align-middle">
                                <thead class="bg-black">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#
                                                <?= $order['id'] ?>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($order['order_date'])) ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-<?= $order['status'] == 'delivered' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= formatPrice($order['total_amount']) ?>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-light">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5 border border-secondary rounded-3">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-white">No orders found</h4>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="products.php" class="btn btn-voltix-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>