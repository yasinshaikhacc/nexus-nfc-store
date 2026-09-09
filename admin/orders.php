<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE)
    session_start();
if (!isAdmin())
    redirect('../src/login.php');

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
    } catch (PDOException $e) {
    }
}

// Fetch Orders
$orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC")->fetchAll();

// Calculate Stats
$total_orders = count($orders);
$total_revenue = 0;
$pending_orders = 0;
foreach ($orders as $o) {
    if ($o['payment_status'] === 'paid' || $o['status'] === 'delivered') { // simplistic revenue logic
        $total_revenue += $o['total_amount'];
    }
    if ($o['status'] !== 'delivered' && $o['status'] !== 'cancelled') {
        $pending_orders++;
    }
}
$avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - VOLTIX Admin</title>

    <!-- CSS Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
</head>

<body class="dark-mode">
    <?php require_once 'includes/header.php'; ?>

    <!-- Main Content -->

    <div class="container p-4">
        <header class="glass-header-admin d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-white m-0">Order Management</h2>
                <p class="text-muted small m-0">Track and fulfill customer orders</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-voltix-outline btn-sm py-2 px-4 rounded-pill">
                    <i class="fas fa-file-export me-2"></i> Export CSV
                </button>
            </div>
        </header>

        <!-- Quick Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">TOTAL ORDERS</span>
                        <i class="fas fa-shopping-bag text-primary p-2 bg-primary bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= number_format($total_orders) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">REVENUE</span>
                        <i class="fas fa-dollar-sign text-success p-2 bg-success bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= formatPrice($total_revenue) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">PENDING</span>
                        <i class="fas fa-clock text-warning p-2 bg-warning bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= number_format($pending_orders) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">AVG. ORDER</span>
                        <i class="fas fa-chart-line text-info p-2 bg-info bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= formatPrice($avg_order_value) ?></h3>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th class="border-0 ps-4">ORDER DETAILS</th>
                            <th class="border-0">CUSTOMER</th>
                            <th class="border-0 text-center">DATE</th>
                            <th class="border-0 text-end">TOTAL</th>
                            <th class="border-0 text-center">STATUS</th>
                            <th class="border-0 text-center pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-700 text-white">#<?= $order['id'] ?></div>
                                    <div class="text-muted x-small"><?= $order['payment_method'] ?? 'Stripe' ?></div>
                                </td>
                                <td>
                                    <div class="fw-600 text-white"><?= htmlspecialchars($order['user_name']) ?></div>
                                    <div class="text-muted x-small">ID: USER-<?= $order['user_id'] ?></div>
                                </td>
                                <td class="text-center text-muted">
                                    <?= date('M d, Y', strtotime($order['order_date'])) ?>
                                </td>
                                <td class="text-end fw-800 text-primary">
                                    <?= formatPrice($order['total_amount']) ?>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'shipped' ? 'info' : 'warning') ?> bg-opacity-20 text-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'shipped' ? 'info' : 'warning') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <select name="status"
                                            class="form-select form-select-sm bg-dark text-white border-secondary rounded-pill d-inline-block w-auto"
                                            onchange="this.form.submit()" style="font-size: 0.8rem; width: 130px;">
                                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>
                                                Shipped</option>
                                            <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>
                                                Delivered</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="glass-card d-inline-block p-4 rounded-circle mb-3">
                                        <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <p class="text-muted">No orders found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <?php require_once 'includes/footer.php'; ?>
</body>

</html>