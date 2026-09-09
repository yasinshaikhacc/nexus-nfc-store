<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if (empty($password) || empty($confirm_password)) {
        $error = "Please enter and confirm your new password.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Verify Token
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            if ($update->execute([$hashed, $user['id']])) {
                $success = "Password updated successfully.";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "Invalid or expired reset link.";
        }
    }
}
?>

<div class="container my-5" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card p-5 border-0 position-relative overflow-hidden">
                <!-- Background Glow -->
                <div class="position-absolute top-0 start-50 translate-middle"
                    style="width: 120px; height: 120px; background: var(--primary-color); filter: blur(80px); opacity: 0.15;">
                </div>

                <div class="position-relative text-center mb-5">
                    <h2 class="fw-bold text-gradient">Reset Password</h2>
                    <p class="text-muted small">Create a new secure password</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4 small">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 mb-4 small">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success ?><br>
                        <a href="login.php" class="fw-bold text-success mt-2 d-inline-block">Login Now</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold" for="password">New Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control form-control-lg"
                                    style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border); border-right: none;"
                                    required placeholder="Enter new password" />
                                <span class="input-group-text border-0"
                                    style="background: var(--input-bg); border: 1px solid var(--input-border); border-left: none; cursor: pointer;"
                                    onclick="togglePasswordVisibility('password', 'password-icon')">
                                    <i class="fas fa-eye text-muted" id="password-icon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold" for="confirm_password">Confirm
                                Password</label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="form-control form-control-lg"
                                    style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border); border-right: none;"
                                    required placeholder="Confirm new password" />
                                <span class="input-group-text border-0"
                                    style="background: var(--input-bg); border: 1px solid var(--input-border); border-left: none; cursor: pointer;"
                                    onclick="togglePasswordVisibility('confirm_password', 'confirm-password-icon')">
                                    <i class="fas fa-eye text-muted" id="confirm-password-icon"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-voltix-primary btn-lg w-100 mb-4 rounded-pill">
                            Reset Password <i class="fas fa-lock ms-2"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>