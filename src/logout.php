<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/functions.php';

// Ensure session is started (safe_session_start called in functions.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_unset();
session_destroy();

redirect('login.php');
?>