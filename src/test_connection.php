<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Test</h1>";

$host = '127.0.0.1';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$dbname = 'voltix_db';

// 1. Try connecting to MySQL Server only
try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✔ Connected to MySQL Server successfully.</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>✘ Failed to connect to MySQL Server: " . $e->getMessage() . "</p>";
    echo "<p>Make sure XAMPP MySQL is GREEN.</p>";
    exit;
}

// 2. Try creating the database if it doesn't exist
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p style='color:green'>✔ Database `$dbname` checked/created.</p>";
    $pdo->exec("USE `$dbname`");
} catch (PDOException $e) {
    echo "<p style='color:red'>✘ Failed to select database: " . $e->getMessage() . "</p>";
    exit;
}

// 3. Check if table 'users' exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✔ Table `users` exists.</p>";

        // Check admin user
        $stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@voltix.com'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>✔ Admin user found.</p>";
        } else {
            echo "<p style='color:orange'>⚠ Admin user NOT found. You might need to Sign Up or run SQL import.</p>";
        }
    } else {
        echo "<p style='color:red'>✘ Table `users` MISSING. Database is empty.</p>";
        echo "<p>Attempting to import database.sql...</p>";

        // Try to import SQL
        if (file_exists('../database.sql')) {
            $sql = file_get_contents('../database.sql');
            $pdo->exec($sql);
            echo "<p style='color:green'>✔ Database Imported Successfully!</p>";
        } else {
            echo "<p style='color:red'>✘ database.sql file not found in parent directory.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error checking tables: " . $e->getMessage() . "</p>";
}

echo "<h3><a href='index.php'>Go to Home Page</a></h3>";
?>