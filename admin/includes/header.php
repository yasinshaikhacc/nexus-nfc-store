<?php
// admin/includes/header.php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Ensure Admin
if (!isAdmin()) {
    header("Location: ../src/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltix Admin</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">

    <style>
        :root {
            --sidebar-width: 280px;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            background: var(--surface-color);
            border-right: 1px solid var(--glass-border);
            padding: 2rem 1.5rem;
            z-index: 1050;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 20px 0 50px rgba(0, 0, 0, 0.3);
            /* Deep shadow for depth */
        }

        /* LAYOUT FIX WRAPPER & ANIMATION */
        .admin-layout-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--bg-color);
            transition: all 0.3s ease;
        }

        .admin-content {
            animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Nav Links */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.2rem !important;
            margin-bottom: 0.5rem;
            border-radius: 12px !important;
            color: var(--text-muted) !important;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            /* Smooth border transition */
        }

        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(var(--primary-color-rgb), 0.05) !important;
            transform: translateX(5px);
        }

        .nav-link:hover i {
            transform: scale(1.2);
        }

        .nav-link.active {
            color: var(--bg-color) !important;
            background: var(--primary-color) !important;
            /* Solid active state for contrast */
            box-shadow: 0 4px 15px var(--shadow-color);
            font-weight: 700;
        }

        .nav-link.active i {
            color: var(--bg-color) !important;
        }

        .sidebar-heading {
            padding: 1.5rem 1.2rem 0.5rem;
            font-size: 0.70rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary-color);
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 0.5rem;
        }

        .sidebar-heading::before,
        .sidebar-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--glass-border), transparent);
            margin: 0 10px;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-layout-wrapper {
                margin-left: 0;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }
        }

        /* Stats & Glass Cards - PREMIUM UPGRADE */
        .stats-card,
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            /* Bouncy transition */
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover,
        .glass-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2), 0 0 15px var(--shadow-color);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            pointer-events: none;
        }

        .glass-header-admin {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Table overrides for light mode */
        .table {
            color: var(--text-color) !important;
        }

        .table thead th {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        /* LAYOUT FIX WRAPPER */
        .admin-layout-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--bg-color);
            transition: all 0.3s ease;
        }

        /* RESET NESTED MARGINS */
        .admin-layout-wrapper .admin-content {
            margin-left: 0 !important;
            /* Override original margin */
            padding: 2.5rem;
            max-width: 1600px;
            /* Professional constraint */
            margin-right: auto;
            margin-left: auto !important;
            /* Center the content container */
            width: 100%;
        }

        @media (max-width: 992px) {
            .admin-layout-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body> <!-- Removed hardcoded class="dark-mode" so it defaults to system or JS preference -->

    <!-- Sidebar -->
    <nav class="admin-sidebar shadow-sm">
        <div class="mb-5 px-2">
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">
                <i class="fas fa-bolt me-2"></i>VOLTIX
            </h3>
            <span class="badge bg-soft-primary text-primary mt-2">ADMIN PANEL</span>
        </div>

        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link" href="../src/index.php">
                    <i class="fas fa-arrow-left"></i> Back to Site
                </a>
            </li>

            <li class="sidebar-heading">Overview</li>

            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>

            <li class="sidebar-heading">Management</li>

            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">
                    <i class="fas fa-box"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>" href="orders.php">
                    <i class="fas fa-shopping-bag"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>" href="users.php">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>

            <li class="sidebar-heading">Settings</li>

            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'profile.php' ? 'active' : '' ?>" href="profile.php">
                    <i class="fas fa-user-cog"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>" href="settings.php">
                    <i class="fas fa-sliders-h"></i> System
                </a>
            </li>
        </ul>

        <div class="mt-4 pt-4 border-top border-light">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small">Theme</span>
                <button id="themeToggle" class="btn btn-sm btn-link text-reset p-0">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            <a class="nav-link text-danger px-0" href="../src/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Mobile Header -->
    <nav class="navbar d-lg-none glass-header-admin rounded-0 mb-0">
        <div class="container-fluid">
            <button class="btn btn-link text-reset p-0"
                onclick="document.querySelector('.admin-sidebar').classList.toggle('show')">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <span class="fw-bold">VOLTIX ADMIN</span>
            <div class="d-flex align-items-center">
                <button id="themeToggleMobile" class="btn btn-link text-reset p-0 me-3">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="admin-layout-wrapper">