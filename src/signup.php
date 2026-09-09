<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container my-5" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card p-5 border-0 position-relative overflow-hidden">
                <!-- Background Glow -->
                <div class="position-absolute top-0 start-50 translate-middle"
                    style="width: 150px; height: 150px; background: var(--secondary-color); filter: blur(80px); opacity: 0.2;">
                </div>

                <div class="position-relative text-center mb-5">
                    <h2 class="fw-bold text-gradient">Create Account</h2>
                    <p class="text-muted small">Join Voltix today</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4 small">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 mb-4 small">
                        <i class="fas fa-check-circle me-2"></i> <?= $success ?> <br>
                        <a href="login.php" class="fw-bold text-success mt-2 d-inline-block">Login Now</a>
                    </div>
                <?php endif; ?>

                <form action="signup.php" method="POST">
                    <!-- Name -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            required placeholder="John Doe" />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            required placeholder="name@example.com" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            required placeholder="Create a password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" for="confirm_password">Confirm
                            Password</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            class="form-control form-control-lg"
                            style="background: var(--input-bg); color: var(--text-color); border: 1px solid var(--input-border);"
                            required placeholder="Confirm your password" />
                    </div>

                    <button type="submit" class="btn btn-voltix-primary btn-lg w-100 mb-4 rounded-pill">
                        Sign Up <i class="fas fa-user-plus ms-2"></i>
                    </button>

                    <div class="text-center">
                        <p class="text-muted small">Already have an account? <a href="login.php"
                                class="text-primary fw-bold">Login</a></p>
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