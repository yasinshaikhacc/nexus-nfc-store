<?php
require_once __DIR__ . '/functions.php';
// Check if a user is logged in for navbar logic
$user_logged_in = isLoggedIn();
$is_admin = isAdmin();

// Calculate Cart Count
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += (int) $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLTIX | Power Your Digital Life</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700;800&family=Playfair+Display:wght@700;900&display=swap"
        rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time(); ?>">
    <!-- Custom JS -->
    <script src="../assets/js/script.js?v=<?= time(); ?>" defer></script>

    <style>
        /* Ensure footer sticks to bottom if content is short */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container flex-nowrap">
            <!-- Navbar brand (LEFT) -->
            <div class="d-flex flex-column align-items-start">
                <a class="navbar-brand m-0" href="index.php">VOLTIX</a>
                <!-- Mobile Only Theme Toggle (Below Logo) -->
                <div class="d-lg-none mt-1">
                    <button id="themeToggleMobile" class="theme-toggle-btn navbar-toggle-styled"
                        style="width: 35px; height: 35px; font-size: 1rem;">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>

            <div class="ms-auto d-flex align-items-center">
                <!-- Mobile Only Sign Up (Always Visible) -->
                <?php if (!$user_logged_in): ?>
                    <a href="signup.php" class="btn btn-voltix-primary btn-sm me-2 d-lg-none">Sign Up</a>
                <?php endif; ?>

                <!-- Toggle button -->
                <button class="navbar-toggler border-0 p-2" type="button" id="mobileMenuToggle">
                    <i class="fas fa-bars text-white"></i>
                </button>
            </div>

            <!-- Collapsible wrapper (Sidebar on Mobile) -->
            <div class="mobile-sidebar" id="navbarSupportedContent">
                <div class="sidebar-header d-lg-none">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <h5 class="text-white mb-0">Menu</h5>
                        <div class="d-flex align-items-center">
                            <button id="themeToggleSidebar" class="theme-toggle-btn navbar-toggle-styled me-3"
                                style="width: 35px; height: 35px; font-size: 1rem;">
                                <i class="fas fa-moon"></i>
                            </button>
                            <button id="closeSidebar" class="btn-close btn-close-white"></button>
                        </div>
                    </div>
                </div>

                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-center">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>"
                            href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>"
                            href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>"
                            href="contact.php">Support</a>
                    </li>

                    <!-- Desktop Only Icons (Cart & Theme) -->
                    <li class="nav-item ms-4 d-none d-lg-flex align-items-center">
                        <a class="text-reset position-relative me-3 d-flex align-items-center" href="cart.php">
                            <i class="fas fa-shopping-cart" style="color: var(--text-color); font-size: 1.2rem;"></i>
                            <span class="badge rounded-pill badge-notification bg-danger" id="cart-count-badge"
                                style="font-size: 0.6rem; transform: translate(50%, -50%); <?= $cart_count > 0 ? '' : 'display:none' ?>">
                                <?= $cart_count ?>
                            </span>
                        </a>
                        <button id="themeToggle" class="theme-toggle-btn">
                            <i class="fas fa-moon" style="font-size: 1.2rem;"></i>
                        </button>
                    </li>


                    <!-- User Auth / Profile -->
                    <li class="nav-item ms-4">
                        <?php if ($user_logged_in): ?>
                            <div class="dropdown">
                                <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#"
                                    id="navbarDropdownMenuAvatar" role="button" data-mdb-dropdown-init
                                    aria-expanded="false">
                                    <span
                                        class="me-2 text-white d-none d-lg-block"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                                    <i class="fas fa-user-circle fa-2x text-white"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuAvatar">
                                    <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                    <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                    <?php if ($is_admin): ?>
                                        <li><a class="dropdown-item" href="../admin/index.php">Admin Dashboard</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <!-- Desktop Only Auth Buttons -->
                            <div class="d-none d-lg-flex align-items-center">
                                <a href="login.php" class="btn btn-outline-voltix btn-rounded btn-sm me-2">Login</a>
                                <a href="signup.php" class="btn btn-voltix-primary btn-sm">Sign Up</a>
                            </div>
                            <!-- Mobile Only Login (Sign Up is outside) -->
                            <div class="d-lg-none mt-3">
                                <a href="login.php" class="btn btn-outline-voltix w-100 mb-2">Login</a>
                            </div>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Navbar -->

    <!-- Spacer for fixed navbar -->
    <main style="margin-top: 70px; margin-bottom: 70px;">

        <!-- Bottom Navigation (Mobile Only) -->
        <nav class="navbar fixed-bottom navbar-dark bg-dark d-lg-none bottom-nav">
            <div class="container-fluid d-flex justify-content-around">
                <a href="index.php"
                    class="nav-link text-center <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-home fa-lg mb-1"></i>
                    <small class="d-block">Home</small>
                </a>
                <a href="products.php"
                    class="nav-link text-center <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">
                    <i class="fas fa-box fa-lg mb-1"></i>
                    <small class="d-block">Shop</small>
                </a>
                <a href="cart.php"
                    class="nav-link text-center position-relative <?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart fa-lg mb-1"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.6rem; transform: translate(-50%, 5px) !important;">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                    <small class="d-block">Cart</small>
                </a>
                <a href="<?= $user_logged_in ? 'profile.php' : 'login.php' ?>"
                    class="nav-link text-center <?= in_array(basename($_SERVER['PHP_SELF']), ['profile.php', 'login.php']) ? 'active' : '' ?>">
                    <i class="fas <?= $user_logged_in ? 'fa-user' : 'fa-sign-in-alt' ?> fa-lg mb-1"></i>
                    <small class="d-block"><?= $user_logged_in ? 'Profile' : 'Login' ?></small>
                </a>
            </div>
        </nav>


        <script>
            // Theme Toggle Logic
            const toggleBtn = document.getElementById('themeToggle');
            const icon = toggleBtn.querySelector('i');
            const body = document.body;

            // Check local storage
            const currentTheme = localStorage.getItem('theme');
            if (currentTheme === 'light') {
                body.classList.add('light-mode');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }

            toggleBtn.addEventListener('click', () => {
                body.classList.toggle('light-mode');

                if (body.classList.contains('light-mode')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'light');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'dark');
                }
            });
            // Mobile Sidebar Toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const mobileSidebar = document.querySelector('.mobile-sidebar');

            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', () => {
                    mobileSidebar.classList.add('open');
                });
            }

            if (closeSidebar) {
                closeSidebar.addEventListener('click', () => {
                    mobileSidebar.classList.remove('open');
                });
            }

            // Sync Mobile Theme Toggle
            const themeToggleMobile = document.getElementById('themeToggleMobile');
            const themeToggleSidebar = document.getElementById('themeToggleSidebar');

            if (themeToggleMobile) {
                themeToggleMobile.addEventListener('click', () => {
                    document.getElementById('themeToggle').click();
                });
            }
            if (themeToggleSidebar) {
                themeToggleSidebar.addEventListener('click', () => {
                    document.getElementById('themeToggle').click();
                });
            }

            // Global AJAX Cart Handler
            async function handleCartAction(event) {
                event.preventDefault();
                const form = event.target;
                const submitBtn = form.querySelector('button[type="submit"]');

                // Prevent multiple clicks
                if (submitBtn) submitBtn.disabled = true;

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const result = await response.json();
                    if (result.success) {
                        // Update Cart Badges
                        const badges = document.querySelectorAll('.badge-notification, .bottom-nav .badge');
                        badges.forEach(badge => {
                            badge.textContent = result.cart_count;
                            badge.style.display = result.cart_count > 0 ? 'inline-block' : 'none';
                        });

                        // If on Cart Page, update prices
                        const productRow = form.closest('.row.align-items-center');
                        if (productRow) {
                            const subtotalEl = productRow.querySelector('.text-end h5');
                            if (subtotalEl) subtotalEl.textContent = result.item_subtotal;
                        }

                        // Update Summary Card
                        const summaryCard = document.querySelector('.col-lg-4 .card-body');
                        if (summaryCard && result.total_price) {
                            const summaryLines = summaryCard.querySelectorAll('.d-flex.justify-content-between span.text-white, .d-flex.justify-content-between strong.text-gradient');
                            if (summaryLines.length >= 3) {
                                summaryLines[0].textContent = result.total_price; // Subtotal
                                summaryLines[1].textContent = result.tax; // Tax
                                summaryLines[2].textContent = result.total_with_tax; // Total
                            }
                        }

                        // Handle Removal
                        const action = formData.get('action');
                        if (action === 'remove' && productRow) {
                            productRow.style.transition = 'all 0.3s ease';
                            productRow.style.opacity = '0';
                            productRow.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                productRow.remove();
                                if (result.cart_count === 0) location.reload();
                            }, 300);
                        }

                        // Visual feedback for Add to Cart
                        if (action === 'add') {
                            if (submitBtn) {
                                const originalText = submitBtn.innerHTML;
                                submitBtn.innerHTML = '<i class="fas fa-check"></i> Added!';
                                submitBtn.classList.add('btn-success');
                                setTimeout(() => {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.classList.remove('btn-success');
                                    submitBtn.disabled = false;
                                }, 1500);
                            }
                        } else if (submitBtn) {
                            submitBtn.disabled = false;
                        }
                    }
                } catch (error) {
                    console.error('Error updating cart:', error);
                    if (submitBtn) submitBtn.disabled = false;
                    // Fallback to standard submission if AJAX fails
                    form.submit();
                }
            }
        </script>