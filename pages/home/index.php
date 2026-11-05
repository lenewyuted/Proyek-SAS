<?php
require_once __DIR__ . "/../../repositories/category-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";

$search = trim($_GET['q'] ?? '');

$categories = getCategories(8);
$products = getProducts($search, 5);

$banners = [
    ['title' => 'Monitor LED Xiaomi A27i', 'desc' => 'High refresh rate, smoother motion', 'btn' => 'Explore It !', 'image' => 'monitor-xiaomi.png', 'fallback' => 'cat-monitor.png'],
    ['title' => 'WD Blue PC 2TB 3.5" HDD', 'desc' => 'Expand PC storage now!', 'btn' => 'Shop Now !', 'image' => 'wd-blue.png', 'fallback' => 'cat-hdd.png'],
    ['title' => 'iGame RTX 5060 8GB', 'desc' => 'Level up your gaming!', 'btn' => 'Learn More !', 'image' => 'igame-rtx.png', 'fallback' => 'cat-gpu.png'],
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Power Your Build</title>

    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <!-- Header -->
    <?php require_once __DIR__ . "/../../components/user/header.php" ?>

    <section class="hero-banner">
        <div class="banner-wrapper">
            <img src="../../asset/images/banner-legion.jpg" alt="Lenovo Legion Monitor Banner">
            <div class="banner-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <section class="section-container">
        <h2 class="section-title">Category</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="#" class="category-card">
                    <div class="category-icon">
                        <img src="../../asset/images/<?= htmlspecialchars($category['icon'] ?? 'cat-cpu.png') ?>"
                            alt="<?= htmlspecialchars($category['name']) ?>">
                    </div>
                    <span class="category-label"><?= htmlspecialchars($category['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-container">
        <h2 class="section-title">Featured Product</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <?php require __DIR__ . "/../../components/user/product-card.php" ?>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
                <p class="empty-state">Produk tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-container">
        <div class="promo-grid">
            <?php foreach ($banners as $banner): ?>
                <div class="promo-card">
                    <div class="promo-text">
                        <h3><?= htmlspecialchars($banner['title']) ?></h3>
                        <p><?= htmlspecialchars($banner['desc']) ?></p>
                        <a href="#" class="promo-btn"><?= htmlspecialchars($banner['btn']) ?></a>
                    </div>
                    <div class="promo-img">
                        <img src="../../asset/images/<?= htmlspecialchars($banner['image']) ?>"
                            alt="<?= htmlspecialchars($banner['title']) ?>"
                            onerror="this.onerror=null;this.src='../../asset/images/<?= htmlspecialchars($banner['fallback']) ?>'">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-container">
        <h2 class="section-title">Why Shop With Us</h2>
        <div class="why-box">
            <div class="why-item"><i class="fa-regular fa-heart"></i>
                <div><strong>Trusted</strong><small>Original products</small></div>
            </div>
            <div class="why-item"><i class="fa-solid fa-truck-fast"></i>
                <div><strong>Fast Delivery</strong><small>Reliable shipping</small></div>
            </div>
            <div class="why-item"><i class="fa-solid fa-tags"></i>
                <div><strong>Best Price</strong><small>Daily deals</small></div>
            </div>
            <div class="why-item"><i class="fa-solid fa-bag-shopping"></i>
                <div><strong>Easy Shopping</strong><small>Simple checkout</small></div>
            </div>
            <div class="why-item"><i class="fa-solid fa-shield-halved"></i>
                <div><strong>Secure</strong><small>Safe payment</small></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php require_once __DIR__ . "/../../components/user/footer.php" ?>
</body>

</html>
