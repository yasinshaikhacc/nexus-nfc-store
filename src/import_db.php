<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'voltix_db';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Dropping database if exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname` ");

    echo "Creating database...\n";
    $pdo->exec("CREATE DATABASE `$dbname` ");
    $pdo->exec("USE `$dbname` ");

    echo "Importing database.sql...\n";
    $sqlFile = dirname(__DIR__) . '/database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "Import Completed Successfully!\n";
    } else {
        echo "Error: database.sql not found at $sqlFile\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>