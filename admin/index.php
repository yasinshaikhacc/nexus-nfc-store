<?php
require_once 'includes/header.php';

// Stats
$stats = [
    'users' => 0,
    'orders' => 0,
    'products' => 0,
    'revenue' => 0
];

try {
    $stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['products'] = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $stats['revenue'] = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn() ?: 0;
} catch (PDOException $e) {
}
?>

<header class="glass-header-admin d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold m-0" style="color: var(--text-color);">Dashboard Overview</h2>
        <p class="small m-0" style="color: var(--text-muted);">Welcome back,
            <?= htmlspecialchars($_SESSION['user_name']) ?>
        </p>
    </div>
    <div class="d-flex gap-3">
        <button class="btn btn-outline-primary btn-sm py-2 px-3 rounded-pill">
            <i class="fas fa-calendar me-2"></i> <?= date('M d, Y') ?>
        </button>
    </div>
</header>

<div class="row g-4 mb-5">
    <!-- Card 1: Orders -->
    <div class="col-xl-3 col-sm-6">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="p-3 rounded-4 bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <span class="text-success small fw-bold">+12% <i class="fas fa-arrow-up ms-1"></i></span>
            </div>
            <h5 class="fw-normal mb-1" style="color: var(--text-muted);">Total Orders</h5>
            <h2 class="fw-bold mb-0" style="color: var(--text-color);"><?= number_format($stats['orders']) ?></h2>
        </div>
    </div>

    <!-- Card 2: Revenue -->
    <div class="col-xl-3 col-sm-6">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="p-3 rounded-4 bg-success bg-opacity-10 text-success">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
                <span class="text-success small fw-bold">+8.4% <i class="fas fa-arrow-up ms-1"></i></span>
            </div>
            <h5 class="fw-normal mb-1" style="color: var(--text-muted);">Total Revenue</h5>
            <h2 class="fw-bold mb-0" style="color: var(--text-color);"><?= formatPrice($stats['revenue']) ?></h2>
        </div>
    </div>

    <!-- Card 3: Products -->
    <div class="col-xl-3 col-sm-6">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="p-3 rounded-4 bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-box fa-lg"></i>
                </div>
                <span class="text-muted small fw-bold">Active</span>
            </div>
            <h5 class="fw-normal mb-1" style="color: var(--text-muted);">Products</h5>
            <h2 class="fw-bold mb-0" style="color: var(--text-color);"><?= number_format($stats['products']) ?></h2>
        </div>
    </div>


    <!-- Card 4: Users -->
    <div class="col-xl-3 col-sm-6">
        <div class="stats-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="p-3 rounded-4 bg-info bg-opacity-10 text-info">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <span class="text-success small fw-bold">+2.1k <i class="fas fa-arrow-up ms-1"></i></span>
            </div>
            <h5 class="fw-normal mb-1" style="color: var(--text-muted);">Total Users</h5>
            <h2 class="fw-bold mb-0" style="color: var(--text-color);"><?= number_format($stats['users']) ?></h2>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="row">
    <div class="col-lg-12">
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom"
                style="border-color: var(--glass-border) !important;">
                <h5 class="m-0 fw-bold" style="color: var(--text-color);">Recent Orders</h5>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" data-mdb-dropdown-init><i
                            class="fas fa-ellipsis-v"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li><a class="dropdown-item" href="orders.php">View All Orders</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-10">
                        <tr>
                            <th class="ps-4">ORDER ID</th>
                            <th>CUSTOMER</th>
                            <th>STATUS</th>
                            <th>AMOUNT</th>
                            <th class="pe-4 text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-bold">#ORD-5542</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-primary bg-opacity-10 rounded-circle me-3">
                                        <i class="fas fa-user text-primary small"></i>
                                    </div>
                                    <span class="small fw-bold">John Doe</span>
                                </div>
                            </td>
                            <td><span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3">Paid</span>
                            </td>
                            <td class="fw-bold">$129.00</td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link btn-sm text-primary p-0"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-bold">#ORD-5541</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-warning bg-opacity-10 rounded-circle me-3">
                                        <i class="fas fa-user text-warning small"></i>
                                    </div>
                                    <span class="small fw-bold">Jane Smith</span>
                                </div>
                            </td>
                            <td><span
                                    class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3">Pending</span>
                            </td>
                            <td class="fw-bold">$245.50</td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-link btn-sm text-primary p-0"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>