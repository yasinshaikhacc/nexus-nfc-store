<?php
// reset_admin.php
// UPLOAD THIS TO YOUR WEB SERVER (htdocs folder)
// VISIT IT: http://voltix-store.great-site.net/reset_admin.php
// DELETE IT IMMEDIATELY AFTER USE!

require_once 'config/db.php';

$email = 'admin@voltix.com';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        echo "<h1>✅ Admin Password Reset Successful!</h1>";
        echo "<p>You can now log in with <b>admin123</b>.</p>";
    } else {
        // Create user if not exists
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin User', $email, $hashed_password, 'admin']);
        echo "<h1>✅ Admin User Created Successfully!</h1>";
        echo "<p>Login: <b>admin@voltix.com</b><br>Pass: <b>admin123</b></p>";
    }
} catch (PDOException $e) {
    echo "<h1>❌ Error</h1>" . $e->getMessage();
}

echo "<br><br><b style='color:red;'>CRITICAL: Delete this file (reset_admin.php) from your server right now for security!</b>";
?>