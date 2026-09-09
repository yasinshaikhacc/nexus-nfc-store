<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$user = null;
$orders = [];

try {
    // Fetch User Info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Fetch Orders
    $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmtOrders->execute([$user_id]);
    $orders = $stmtOrders->fetchAll();

    // Handle Profile Update
    $success = '';
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $new_pass = $_POST['new_password'];

        if (!empty($new_pass)) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $hashed, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);
        }
        $success = "Profile updated successfully!";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }

} catch (PDOException $e) {
    $error = "System error occurred.";
}
?>

<div class="container my-5 pt-4">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card glass-card border-0 p-3 h-100">
                <div class="text-center mb-4 pt-3">
                    <div class="position-relative d-inline-block">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random&size=128"
                            class="rounded-circle shadow-lg mb-3" width="100"
                            style="border: 3px solid var(--primary-color);">
                        <span
                            class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle"></span>
                    </div>
                    <h5 class="fw-bold text-gradient mb-1">
                        <?= htmlspecialchars($user['name']) ?>
                    </h5>
                    <p class="text-muted small">
                        <?= htmlspecialchars($user['email']) ?>
                    </p>
                </div>
                <div class="list-group list-group-light list-group-flush gap-2" id="profileTabs" role="tablist">
                    <a class="list-group-item list-group-item-action rounded-3 border-0 active d-flex align-items-center px-3"
                        id="tab-dashboard" data-mdb-toggle="pill" href="#pills-dashboard" role="tab"
                        aria-controls="pills-dashboard" aria-selected="true"
                        style="background-color: rgba(var(--primary-color-rgb), 0.1); color: var(--primary-color);">
                        <i class="fas fa-th-large me-3"></i> Dashboard
                    </a>
                    <a class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center px-3 text-muted"
                        id="tab-orders" data-mdb-toggle="pill" href="#pills-orders" role="tab"
                        aria-controls="pills-orders" aria-selected="false">
                        <i class="fas fa-shopping-bag me-3"></i> My Orders
                        <span class="badge bg-secondary ms-auto"><?= count($orders) ?></span>
                    </a>
                    <a class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center px-3 text-muted"
                        id="tab-settings" data-mdb-toggle="pill" href="#pills-settings" role="tab"
                        aria-controls="pills-settings" aria-selected="false">
                        <i class="fas fa-user-cog me-3"></i> Settings
                    </a>
                    <a href="logout.php"
                        class="list-group-item list-group-item-action rounded-3 border-0 d-flex align-items-center px-3 text-danger mt-4">
                        <i class="fas fa-sign-out-alt me-3"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if ($success): ?>
                <div
                    class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 mb-4 d-flex align-items-center shadow-sm">
                    <i class="fas fa-check-circle fa-lg me-3"></i>
                    <div><?= $success ?></div>
                </div>
            <?php endif; ?>

            <div class="tab-content" id="pills-tabContent">
                <!-- Dashboard Tab -->
                <div class="tab-pane fade show active" id="pills-dashboard" role="tabpanel"
                    aria-labelledby="tab-dashboard">
                    <h2 class="fw-bold mb-4 text-gradient">Account Overview</h2>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="card glass-card h-100 border-0 overflow-hidden position-relative">
                                <div class="card-body p-4 position-relative z-1">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div class="p-3 bg-primary bg-opacity-10 rounded-4 text-primary">
                                            <i class="fas fa-shopping-basket fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-muted fw-normal mb-2">Total Orders</h5>
                                    <h2 class="display-6 fw-bold mb-0" style="color: var(--text-color);">
                                        <?= count($orders) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card glass-card h-100 border-0 overflow-hidden position-relative">
                                <div class="card-body p-4 position-relative z-1">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div class="p-3 bg-success bg-opacity-10 rounded-4 text-success">
                                            <i class="fas fa-wallet fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-muted fw-normal mb-2">Total Spent</h5>
                                    <h2 class="display-6 fw-bold mb-0" style="color: var(--text-color);">
                                        <?php
                                        $total = 0;
                                        foreach ($orders as $o)
                                            $total += $o['total_amount'];
                                        echo formatPrice($total);
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card glass-card h-100 border-0 overflow-hidden position-relative">
                                <div class="card-body p-4 position-relative z-1">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div class="p-3 bg-warning bg-opacity-10 rounded-4 text-warning">
                                            <i class="fas fa-star fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="text-muted fw-normal mb-2">Reward Points</h5>
                                    <h2 class="display-6 fw-bold mb-0" style="color: var(--text-color);">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="fw-bold mb-3" style="color: var(--text-color);">Recent Activity</h3>
                    <div class="card glass-card border-0">
                        <div class="card-body p-5 text-center text-muted">
                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No recent activity to show.</p>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="pills-orders" role="tabpanel" aria-labelledby="tab-orders">
                    <h2 class="fw-bold mb-4 text-gradient">My Orders</h2>
                    <div class="card glass-card border-0 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead style="background-color: rgba(var(--primary-color-rgb), 0.05);">
                                    <tr>
                                        <th class="ps-4 py-3 text-muted fw-bold small border-0">ORDER ID</th>
                                        <th class="py-3 text-muted fw-bold small border-0">DATE</th>
                                        <th class="py-3 text-muted fw-bold small border-0">STATUS</th>
                                        <th class="py-3 text-muted fw-bold small border-0">TOTAL</th>
                                        <th class="pe-4 py-3 text-end text-muted fw-bold small border-0">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orders)): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr style="color: var(--text-color);">
                                                <td class="ps-4 py-3 fw-bold">#<?= $order['id'] ?></td>
                                                <td class="py-3"><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                                <td class="py-3">
                                                    <span
                                                        class="badge rounded-pill bg-<?= $order['status'] == 'paid' ? 'success' : ($order['status'] == 'processing' ? 'warning' : 'secondary') ?> bg-opacity-20 text-<?= $order['status'] == 'paid' ? 'success' : ($order['status'] == 'processing' ? 'warning' : 'secondary') ?> px-3 py-2">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="py-3 fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                                                <td class="pe-4 py-3 text-end">
                                                    <button class="btn btn-sm btn-outline-primary rounded-pill">View</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3 d-block"></i>
                                                <p class="text-muted">No orders found.</p>
                                                <a href="products.php" class="btn btn-voltix-primary mt-2">Start
                                                    Shopping</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="pills-settings" role="tabpanel" aria-labelledby="tab-settings">
                    <h2 class="fw-bold mb-4 text-gradient">Profile Settings</h2>
                    <div class="card glass-card border-0 p-4">
                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                        value="<?= htmlspecialchars($user['name']) ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                                        value="<?= htmlspecialchars($user['email']) ?>" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">New Password <small
                                            class="fw-normal">(Leave blank to keep current)</small></label>
                                    <input type="password" name="new_password" class="form-control form-control-lg"
                                        style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);" />
                                </div>
                                <div class="col-12 mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-voltix-primary btn-lg px-5">Save
                                        Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once '../includes/footer.php'; ?>