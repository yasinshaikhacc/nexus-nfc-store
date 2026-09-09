<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../src/login.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch Admin Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error = "Passwords do not match.";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashed, $user_id]);
                $success = "Profile and password updated successfully!";
            }
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);
            $success = "Profile updated successfully!";
        }

        // Refresh admin data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $admin = $stmt->fetch();

    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Voltix</title>
    <!-- CSS Dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
    <style>
        /* Specific profile styles that extend the global theme */
        .profile-card-header {
            background: linear-gradient(135deg, rgba(var(--primary-color-rgb), 0.1), rgba(var(--secondary-color-rgb), 0.05));
            border-bottom: 1px solid var(--glass-border);
            padding: 3rem 2rem;
            text-align: center;
        }

        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .avatar-ring {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            border: 2px dashed var(--primary-color);
            animation: spin 10s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="dark-mode">
    <?php require_once 'includes/header.php'; ?>

    <!-- Main Content -->

    <div class="container p-4">
        <header class="glass-header-admin d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-800 text-white m-0">Admin Profile</h2>
                <p class="text-muted small m-0">Manage your personal account settings</p>
            </div>
            <div class="d-flex gap-3">
                <button class="btn btn-voltix-outline btn-sm py-2 px-3 rounded-pill">
                    <i class="fas fa-calendar me-2"></i> <?= date('M d, Y') ?>
                </button>
            </div>
        </header>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Main Profile Card -->
                <div class="glass-card overflow-hidden mb-4">
                    <div class="profile-card-header">
                        <div class="avatar-container">
                            <div class="avatar-ring"></div>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($admin['name']) ?>&background=00f3ff&color=050505&size=128"
                                class="rounded-circle shadow-lg position-relative" width="120" style="z-index: 2;">
                            <span
                                class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary p-2 border border-dark"
                                style="z-index: 3;">
                                <i class="fas fa-camera"></i>
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1">
                            <?= htmlspecialchars($admin['name']) ?>
                        </h2>
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill letter-spacing-1">ADMINISTRATOR</span>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        <?php if ($success): ?>
                            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success mb-4 rounded-4">
                                <i class="fas fa-check-circle me-2"></i> <?= $success ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 rounded-4">
                                <i class="fas fa-exclamation-triangle me-2"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2" for="name">Display
                                        Name</label>
                                    <input type="text" id="name" name="name"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3"
                                        value="<?= htmlspecialchars($admin['name']) ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2" for="email">Email
                                        Address</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3"
                                        value="<?= htmlspecialchars($admin['email']) ?>" required />
                                </div>

                                <div class="col-12 mt-5">
                                    <h5
                                        class="text-white opacity-50 mb-4 small text-uppercase letter-spacing-1 border-bottom border-light border-opacity-10 pb-2">
                                        <i class="fas fa-lock me-2"></i>Security
                                    </h5>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2" for="new_password">New
                                        Password</label>
                                    <input type="password" id="new_password" name="new_password"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3" />
                                    <div class="small text-muted mt-2">Leave blank to keep current password</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-2"
                                        for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        class="form-control bg-dark border-light border-opacity-10 text-white py-3" />
                                </div>

                                <div class="col-12 mt-4 pt-2">
                                    <button type="submit"
                                        class="btn btn-voltix-primary btn-lg px-5 py-3 rounded-pill shadow-lg w-100">
                                        Update Profile <i class="fas fa-check-circle ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Side Info Column -->
            <div class="col-lg-4">
                <div class="glass-card p-4 mb-4">
                    <h6 class="text-primary mb-3 text-uppercase fw-bold small letter-spacing-1"><i
                            class="fas fa-shield-alt me-2"></i>Security Info</h6>
                    <div class="d-flex justify-content-between mb-3 border-bottom border-light border-opacity-10 pb-2">
                        <span class="text-muted small">Account Type</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Super Administrator</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom border-light border-opacity-10 pb-2">
                        <span class="text-muted small">Last Modified</span>
                        <span class="text-white small"><?= date('M d, H:i') ?></span>
                    </div>
                    <div class="mt-4">
                        <button
                            class="btn btn-outline-light btn-sm w-100 rounded-pill opacity-75 hover-opacity-100">Manage
                            2FA</button>
                    </div>
                </div>

                <div class="glass-card p-4">
                    <h6 class="text-primary mb-3 text-uppercase fw-bold small letter-spacing-1"><i
                            class="fas fa-history me-2"></i>Recent Activity</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-2 bg-success bg-opacity-10 rounded-circle me-3">
                            <i class="fas fa-sign-in-alt text-success small"></i>
                        </div>
                        <div>
                            <p class="text-white small mb-0 fw-bold">Successful Login</p>
                            <span class="text-muted extra-small">2 hours ago • IP: 127.0.0.1</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-primary bg-opacity-10 rounded-circle me-3">
                            <i class="fas fa-edit text-primary small"></i>
                        </div>
                        <div>
                            <p class="text-white small mb-0 fw-bold">Updated Product #102</p>
                            <span class="text-muted extra-small">Yesterday • IP: 127.0.0.1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once 'includes/footer.php'; ?>
</body>

</html>