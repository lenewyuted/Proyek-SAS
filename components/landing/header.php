<?php
require_once __DIR__ . "/../../repositories/product-repository.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost:3000');
}

require_once __DIR__ . '/../../middleware/auth.php';
$authUser = isLoggedIn() ? getAuthUser() : null;

$userId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : 0;
$cartItems = $_SESSION['cart'] ?? [];

$cartBadgeCount = 0;
if ($userId > 0 && !empty($cartItems) && is_array($cartItems)) {
    foreach ($cartItems as $item) {
        $cartBadgeCount += (int) ($item['quantity'] ?? 1);
    }
}

$searchQuery = trim((string) ($_GET['q'] ?? ''));
$baseUrl = rtrim(BASE_URL, '/');
?>

<!-- Bootstrap 4.6 CSS (Dibawa oleh header agar dropdown & carousel berfungsi konsisten) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<link rel="stylesheet" href="<?= $baseUrl ?>/styles/components/landing/header.css">

<!-- bar bagian atas -->
<div class="top-bar">
    <span>Discover Premium PC Components Built for Performance, Speed, and Reliability</span>
</div>

<!-- main navbar -->
<header class="top-header">
    <div class="logo">
        <a href="<?= $baseUrl ?>/pages/landing-page/index.php">
            <img src="<?= $baseUrl ?>/assets/img/Logo.png" alt="TechHub Logo" class="brand-logo-img">
        </a>
    </div>

    <!-- search bar -->
    <form action="<?= $baseUrl ?>/pages/landing-page/index.php" method="GET" class="search-form">
        <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search for parts to build your dream PC...">
        <button type="submit" class="search-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <!-- tombol aksi kanan -->
    <div class="nav-icons">
        <a href="<?= $baseUrl ?>/pages/recommendation/index.php" class="icon-link" title="Likes">
            <i class="fa-solid fa-thumbs-up"></i>
        </a>

        <a href="<?= $baseUrl ?>/pages/compare/index.php" class="icon-link" title="Compare">
            <i class="fa-solid fa-sliders"></i>
        </a>

        <a href="<?= $baseUrl ?>/pages/user/cart/index.php" class="cart-icon-link">
            <i class="fa-solid fa-cart-shopping"></i>
            <?php if ($cartBadgeCount > 0): ?>
                <span class="cart-badge"><?= $cartBadgeCount ?></span>
            <?php endif; ?>
        </a>

        <!-- Dropdown User Account -->
        <div class="dropdown">
            <a href="#" class="icon-link dropdown-toggle" id="userMenuDropdown" role="button" title="User Account">
                <i class="fa-regular fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right user-dropdown-menu" aria-labelledby="userMenuDropdown">
                <?php if ($authUser): ?>
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-name"><?= htmlspecialchars($authUser['name'] ?? 'User') ?></div>
                        <div class="user-dropdown-email"><?= htmlspecialchars($authUser['email'] ?? '') ?></div>
                        <span class="user-dropdown-badge"><?= strtoupper(htmlspecialchars($authUser['role'] ?? 'MEMBER')) ?></span>
                    </div>
                    <div class="user-dropdown-body">
                        <?php if (($authUser['role'] ?? '') === 'admin'): ?>
                            <a class="dropdown-item" href="<?= $baseUrl ?>/pages/admin/dashboard/index.php">
                                <i class="fa-solid fa-compass user-dropdown-icon"></i> Dashboard Admin
                            </a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="<?= $baseUrl ?>/pages/user/profile/index.php">
                            <i class="fa-solid fa-user-pen user-dropdown-icon"></i> Edit Profil
                        </a>
                        <a class="dropdown-item" href="<?= $baseUrl ?>/pages/user/cart/order-history.php">
                            <i class="fa-solid fa-clock-rotate-left user-dropdown-icon"></i> Order History
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= $baseUrl ?>/actions/auth/logout.php">
                            <i class="fa-solid fa-arrow-right-from-bracket user-dropdown-icon"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-name">Tamu</div>
                        <div class="user-dropdown-email">Silakan login ke akun Anda</div>
                    </div>
                    <div class="user-dropdown-body">
                        <a class="dropdown-item" href="<?= $baseUrl ?>/pages/auth/login.php">
                            <i class="fa-solid fa-right-to-bracket user-dropdown-icon"></i> Masuk
                        </a>
                        <a class="dropdown-item" href="<?= $baseUrl ?>/pages/auth/register.php">
                            <i class="fa-solid fa-user-plus user-dropdown-icon"></i> Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('click', function(e) {
        var dropdown = document.querySelector('.nav-icons .dropdown');
        if (!dropdown) return;

        if (e.target.closest('#userMenuDropdown')) {
            e.preventDefault();
            dropdown.classList.toggle('show');
        } else if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>