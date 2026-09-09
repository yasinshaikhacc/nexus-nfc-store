<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE)
    session_start();
if (!isAdmin())
    redirect('../src/login.php');

// Fetch Users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

// Stats Limits
$total_users = count($users);
$total_admins = 0;
$new_users = 0;
foreach ($users as $u) {
    if ($u['role'] === 'admin')
        $total_admins++;
    if (strtotime($u['created_at']) > strtotime('-30 days'))
        $new_users++;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - VOLTIX Admin</title>

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
                <h2 class="fw-800 text-white m-0">User Accounts</h2>
                <p class="text-muted small m-0">Manage system users and access levels</p>
            </div>
            <div class="d-flex gap-3">
                <div
                    class="input-group input-group-sm rounded-pill bg-dark border border-light border-opacity-10 px-3 py-2">
                    <span class="bg-transparent border-0 text-muted"><i class="fas fa-search pt-1"></i></span>
                    <input type="text" class="form-control bg-transparent border-0 text-white shadow-none"
                        placeholder="Search users...">
                </div>
            </div>
        </header>

        <!-- Quick Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">TOTAL USERS</span>
                        <i class="fas fa-users text-primary p-2 bg-primary bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= number_format($total_users) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">ADMINISTRATORS</span>
                        <i class="fas fa-user-shield text-danger p-2 bg-danger bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= number_format($total_admins) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold">NEW (30 DAYS)</span>
                        <i class="fas fa-user-plus text-success p-2 bg-success bg-opacity-10 rounded-circle"></i>
                    </div>
                    <h3 class="fw-bold m-0 text-white"><?= number_format($new_users) ?></h3>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-muted small">
                            <th class="border-0 ps-4">ID</th>
                            <th class="border-0">PERSONAL INFO</th>
                            <th class="border-0">EMAIL ADDRESS</th>
                            <th class="border-0 text-center">ROLE</th>
                            <th class="border-0 text-center">JOINED DATE</th>
                            <th class="border-0 text-center pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="text-muted fw-bold ps-4">#<?= $u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px;">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div class="text-white fw-600"><?= htmlspecialchars($u['name']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted"><?= htmlspecialchars($u['email']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?> bg-opacity-10 text-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?> px-3 py-2">
                                        <?= ucfirst($u['role']) ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted small">
                                    <?= date('M d, Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-link btn-sm text-primary p-0 mx-1"><i
                                            class="fas fa-edit"></i></button>
                                    <button class="btn btn-link btn-sm text-danger p-0 mx-1"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="glass-card d-inline-block p-4 rounded-circle mb-3">
                                        <i class="fas fa-users fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <p class="text-muted">No users found.</p>
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