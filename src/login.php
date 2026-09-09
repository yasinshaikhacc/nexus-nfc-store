<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/db.php';
require_once '../includes/functions.php';

// Logic BEFORE header.php inclusion to prevent "headers already sent"
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                redirect('index.php');
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Now include header which outputs HTML
require_once '../includes/header.php';
?>

<div class="container my-5" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card p-5 border-0 position-relative overflow-hidden">
                <!-- Background Glow -->
                <div class="position-absolute top-0 start-50 translate-middle"
                    style="width: 150px; height: 150px; background: var(--primary-color); filter: blur(80px); opacity: 0.2;">
                </div>

                <div class="position-relative text-center mb-5">
                    <h2 class="fw-bold text-gradient">Welcome Back</h2>
                    <p class="text-muted small">Sign in to continue to Voltix</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4 small">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            value="admin@voltix.com" required placeholder="name@example.com" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border); border-right: none;"
                                required placeholder="Enter your password" />
                            <span class="input-group-text border-0"
                                style="background: var(--input-bg); border: 1px solid var(--input-border); border-left: none; cursor: pointer;"
                                onclick="togglePasswordVisibility('password', 'password-icon')">
                                <i class="fas fa-eye text-muted" id="password-icon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <!-- Checkbox -->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="rememberMe" checked />
                            <label class="form-check-label text-muted small" for="rememberMe"> Remember me </label>
                        </div>
                        <a href="forgot_password.php" class="text-primary small fw-bold">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-voltix-primary btn-lg w-100 mb-4 rounded-pill">
                        Login <i class="fas fa-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center">
                        <p class="text-muted small">Not a member? <a href="signup.php"
                                class="text-primary fw-bold">Create Account</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.form-outline').forEach((formOutline) => {
        new mdb.Input(formOutline).init();
    });
</script>

<?php require_once '../includes/footer.php'; ?>