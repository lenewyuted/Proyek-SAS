<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . "/../../repositories/category-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";

$banners = getLatestProducts();
$categories = getCategoriesLandingPage();

// Fetch Data Produk beserta Kategori (Featured: 5 produk, bisa difilter pencarian)
$search = trim($_GET['q'] ?? '');

$queryProducts = "SELECT products.*, categories.name AS category_name 
                  FROM products
                  LEFT JOIN categories ON products.category_id = categories.id";
if ($search !== '') {
    $queryProducts .= " WHERE products.name LIKE :search";
}
$queryProducts .= " ORDER BY products.id ASC LIMIT 5";

$stmtProd = $pdo->prepare($queryProducts);
if ($search !== '') {
    $stmtProd->bindValue(':search', '%' . $search . '%');
}
$stmtProd->execute();
$products = $stmtProd->fetchAll();


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Power Your Build</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Path CSS -->
    <link rel="stylesheet" href="../../asset/css/style.css">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../styles/pages/landing/style.css">
</head>
<body>

    <!-- TOP BAR ANNOUNCEMENT -->
    <div class="top-announcement">
        Discover Premium PC Components Built for Performance, Speed, and Reliability
    </div>

    <!-- MAIN HEADER -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php">
                    <img src="../../asset/images/logo.png" alt="TechHub Logo" id="brandlogo">
                </a>
            </div>

            <div class="search-box">
                <form action="index.php" method="GET">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                           placeholder="Search for parts to build your dream PC...">
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="header-nav-icons">
                <a href="#" aria-label="Recommendation"><i class="fa-regular fa-thumbs-up"></i></a>
                <a href="#" aria-label="Compare"><i class="fa-solid fa-code-compare"></i></a>
                <a href="#" aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
                <a href="#" aria-label="Profile"><i class="fa-regular fa-circle-user"></i></a>
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <section class="hero-banner">
        <div class="banner-wrapper">
            <img src="../../asset/images/banner-legion.jpg" alt="Lenovo Legion Monitor Banner">
            <img src="../../asset/images/banner-legion.jpg" alt="Lenovo Legion Monitor Banner">
            <img src="../../asset/images/banner-legion.jpg" alt="Lenovo Legion Monitor Banner">
            <div class="banner-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
    </section>

    <!-- CATEGORY -->
    <section class="section-container">
        <h2 class="section-title">Category</h2>
        <div class="category-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <a href="#" class="category-card">
                        <div class="category-icon">
                            <img src="../../assets/img/category/<?= htmlspecialchars($cat['picture'] ?? 'cat-cpu.png') ?>"
                                 alt="<?= htmlspecialchars($cat['picture']) ?>">
                        </div>
                        <span class="category-label"><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- FEATURED PRODUCT -->
    <section class="section-container">
        <h2 class="section-title">Featured Product</h2>
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): ?>
                    <div class="product-card">
                        <div class="product-img">
                            <img src="../../assets/img/<?= $prod["picture"]?>" alt="<?= $prod["name"]?>">
                        
                        </div>
                        <h3 class="product-title"><?= htmlspecialchars($prod['name']) ?></h3>
                        <div class="product-rating">
                            <span><?= number_format((float)($prod['rating'] ?? 5), 1) ?></span>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="product-footer">
                            <span class="product-price">Rp.<?= number_format($prod['price'], 0, ',', '.') ?></span>
                            <button class="btn-add-cart" type="button" aria-label="Tambah ke keranjang">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">Produk tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- PROMO BANNER -->
    <section class="section-container">
        <div class="promo-grid">
            <?php foreach ($banners as $b): ?>
                <div class="promo-card">
                    <div class="promo-text">
                        <h3><?= htmlspecialchars($b['name']) ?></h3>
                        <p><?= htmlspecialchars($b['description']) ?></p>
                        <a href="#" class="promo-btn">Learn More</a>
                    </div>
                    <div class="promo-img">
                        <img src="../../assets/img/<?= htmlspecialchars($b['picture']) ?>"
                             alt="<?= htmlspecialchars($b['name']) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- WHY SHOP WITH US -->
    <section class="section-container">
        <h2 class="section-title">Why Shop With Us</h2>
        <div class="why-box">
            <div class="why-item"><i class="fa-regular fa-heart"></i><div><strong>Trusted</strong><small>Original products</small></div></div>
            <div class="why-item"><i class="fa-solid fa-truck-fast"></i><div><strong>Fast Delivery</strong><small>Reliable shipping</small></div></div>
            <div class="why-item"><i class="fa-solid fa-tags"></i><div><strong>Best Price</strong><small>Daily deals</small></div></div>
            <div class="why-item"><i class="fa-solid fa-bag-shopping"></i><div><strong>Easy Shopping</strong><small>Simple checkout</small></div></div>
            <div class="why-item"><i class="fa-solid fa-shield-halved"></i><div><strong>Secure</strong><small>Safe payment</small></div></div>
        </div>
    </section>

    <!-- MAIN FOOTER -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-col brand-info">
                <img src="../../asset/images/logo.png" alt="TechHub Logo" class="footer-logo">
                <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
            </div>

            <div class="footer-col">
                <h4>Shop</h4>
                <ul>
                    <li><a href="#">Processors</a></li>
                    <li><a href="#">Motherboards</a></li>
                    <li><a href="#">Graphics Cards</a></li>
                    <li><a href="#">RAM &amp; Storage</a></li>
                    <li><a href="#">Power Supplies</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">How to Order</a></li>
                    <li><a href="#">Shipping</a></li>
                    <li><a href="#">Payment Methods</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>My Account</h4>
                <ul>
                    <li><a href="#">My Profile</a></li>
                    <li><a href="#">Recommendation</a></li>
                    <li><a href="#">Compare Products</a></li>
                    <li><a href="#">Shopping Cart</a></li>
                    <li><a href="#">Search Products</a></li>
                </ul>
            </div>

            <div class="footer-col social-col">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="#" class="ig" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="fb" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="yt" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="wa" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="tt" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="xt" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 TechHub. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>