<?php
// includes/functions.php


function safe_session_start()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

safe_session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url)
{
    header("Location: $url");
    exit();
}

function sanitize($input)
{
    return htmlspecialchars(trim($input));
}

function formatPrice($price)
{
    return '$' . number_format($price, 2);
}

// Flash messages
function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>