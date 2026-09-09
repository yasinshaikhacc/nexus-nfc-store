<?php
require_once '../config/db.php';

try {
    // Add reset_token column
    $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL");
    echo "Column 'reset_token' added successfully.<br>";
} catch (PDOException $e) {
    echo "Column 'reset_token' might already exist or error: " . $e->getMessage() . "<br>";
}

try {
    // Add reset_expires column
    $pdo->exec("ALTER TABLE users ADD COLUMN reset_expires DATETIME NULL");
    echo "Column 'reset_expires' added successfully.<br>";
} catch (PDOException $e) {
    echo "Column 'reset_expires' might already exist or error: " . $e->getMessage() . "<br>";
}

echo "Database updated for password reset functionality.";
?>