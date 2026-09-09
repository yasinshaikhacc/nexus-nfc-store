<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check user
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            // Expires in 1 hour
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            if ($update->execute([$token, $expires, $user['id']])) {
                // SIMULATION: In a real app, send email here.
                // For Localhost demo, we show the link directly.
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

                $message = "<strong>Simulation Mode:</strong><br>A reset link has been generated (Emails don't work on localhost without SMTP).<br><br>
                            <a href='$resetLink' class='btn btn-sm btn-success'>Click here to Reset Password</a>";
            } else {
                $error = "Could not generate token. Try again.";
            }
        } else {
            // Don't reveal if user exists for security, but for UX on localhost:
            $error = "No account found with that email.";
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
                    style="width: 120px; height: 120px; background: var(--warning-color, #ffc107); filter: blur(80px); opacity: 0.15;">
                </div>

                <div class="position-relative text-center mb-5">
                    <h2 class="fw-bold text-gradient">Forgot Password?</h2>
                    <p class="text-muted small">Enter your email to reset your credentials</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4 small">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 mb-4 small">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            required placeholder="name@example.com" />
                    </div>

                    <button type="submit" class="btn btn-voltix-primary btn-lg w-100 mb-4 rounded-pill">
                        Send Reset Link <i class="fas fa-paper-plane ms-2"></i>
                    </button>

                    <div class="text-center">
                        <a href="login.php" class="text-muted small fw-bold"><i class="fas fa-arrow-left me-1"></i> Back
                            to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>