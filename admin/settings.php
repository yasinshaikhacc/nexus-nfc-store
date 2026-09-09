<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../src/login.php');
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulate saving
    $success = "System settings updated successfully!";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Voltix</title>
    <!-- CSS Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
</head>

<body class="dark-mode">
    <?php require_once 'includes/header.php'; ?>

    <!-- Main Content -->

    <div class="container p-4">
        <header class="glass-header-admin d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-white m-0">System Settings</h2>
                <p class="text-muted small m-0">Configure your website and server properties</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-voltix-outline btn-sm py-2 px-3 rounded-pill">
                    <i class="fas fa-calendar me-2"></i> <?= date('M d, Y') ?>
                </button>
            </div>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-4 rounded-4">
                <i class="fas fa-check-circle me-2"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- General Settings -->
                    <div class="glass-card mb-4">
                        <div class="p-4 p-lg-5">
                            <div
                                class="d-flex align-items-center mb-4 pb-4 border-bottom border-light border-opacity-10">
                                <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary me-3">
                                    <i class="fas fa-globe fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-white mb-0 fw-bold">General Configuration</h4>
                                    <p class="text-muted small mb-0">Core website identity and contact info</p>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2">Site Name</label>
                                    <input type="text" id="site_name"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3"
                                        value="Voltix Store" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2">Support Email</label>
                                    <input type="email" id="contact_email"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3"
                                        value="support@voltix.com" />
                                </div>
                                <div class="col-12">
                                    <div
                                        class="p-3 rounded-4 bg-dark bg-opacity-50 border border-light border-opacity-10">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="maintenance" />
                                            <label class="form-check-label text-white ms-2" for="maintenance">
                                                <strong>Maintenance Mode</strong><br>
                                                <span class="text-muted small">Disable the store for customers while
                                                    performing updates</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="glass-card mb-4">
                        <div class="p-4 p-lg-5">
                            <div
                                class="d-flex align-items-center mb-4 pb-4 border-bottom border-light border-opacity-10">
                                <div class="p-3 bg-warning bg-opacity-10 rounded-circle text-warning me-3">
                                    <i class="fas fa-shield-halved fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-white mb-0 fw-bold">Security & Authentication</h4>
                                    <p class="text-muted small mb-0">Manage system access and safety protocols</p>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="reg_enable" checked />
                                        <label class="form-check-label text-white ms-2" for="reg_enable">Allow New
                                            User Registration</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="two_factor" />
                                        <label class="form-check-label text-white ms-2" for="two_factor">Enforce
                                            Two-Factor Authentication (2FA)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-voltix-primary btn-lg px-5 py-3 rounded-pill shadow-lg w-100 mb-5">
                        Apply System Changes <i class="fas fa-save ms-2"></i>
                    </button>
                </div>

                <div class="col-lg-4">
                    <div class="glass-card mb-4">
                        <div class="p-4">
                            <div class="text-center py-3">
                                <div class="p-4 bg-success bg-opacity-10 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-server fa-2x text-success shadow-sm"></i>
                                </div>
                                <h5 class="text-white mb-1 fw-bold">Server Status</h5>
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success p-2 small">
                                    <i class="fas fa-circle me-1 small"></i> Operational
                                </span>
                            </div>
                            <hr class="border-light border-opacity-10 my-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">PHP Version</span>
                                <span class="text-white fw-bold badge bg-dark bg-opacity-50"><?= PHP_VERSION ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Environment</span>
                                <span class="text-white fw-bold badge bg-dark bg-opacity-50">Localhost</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Database</span>
                                <span class="text-white fw-bold badge bg-dark bg-opacity-50">MySQL 8.0</span>
                            </div>
                            <div class="mt-4 pt-2">
                                <button
                                    class="btn btn-outline-light btn-sm w-100 py-3 rounded-pill opacity-75 hover-opacity-100">
                                    <i class="fas fa-sync-alt me-2"></i> Refresh Stats
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-4 bg-primary bg-opacity-5 border border-primary border-opacity-25">
                        <h6 class="text-primary mb-3 fw-bold small text-uppercase letter-spacing-1">Support & Help
                        </h6>
                        <p class="text-muted small mb-4">Need help configuring your server? Our documentation is
                            here for you.</p>
                        <a href="#" class="btn btn-voltix-outline btn-sm w-100 py-2 rounded-pill">
                            <i class="fas fa-book me-2"></i> Documentation
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <?php require_once 'includes/footer.php'; ?>
</body>

</html>