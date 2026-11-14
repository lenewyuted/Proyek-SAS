<?php
session_start();
require_once __DIR__ . "/../../repositories/product-repository.php";
$product = getProduct($_GET['id']);

$flashSuccess = $_SESSION['flash_success'] ?? "";

unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Product Description</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/products/show.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="top-bar">
        Discover Premium PC Components Built for Performance, Speed, and Reliability[cite: 3]
    </div>

    <!-- MAIN HEADER -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="../landing/index.php">
                    <img src="../../assets/img/Logo.png" alt="TechHub Logo">
                </a>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search for parts to build your dream PC...">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
            <div class="header-actions">
                <a href="#"><i class="fa-regular fa-thumbs-up"></i></a>
                <a href="#"><i class="fa-solid fa-sliders"></i></a>
                <a href="#"><i class="fa-solid fa-cart-shopping"></i></a>
                <a href="#"><i class="fa-regular fa-user"></i></a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT / PRODUCT DESCRIPTION SECTION -->
    <div><?= $flashSuccess ?></div>
    <main class="product-main-container">
        <!-- Judul Halaman -->
        <div class="product-header-title">

            <div>
                <h1>Product Description</h1>
            </div>
            <a href="../landing/index.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- DETAIL PRODUK UTAMA -->
        <div class="product-detail-grid">
            <!-- Gambar Produk -->
            <div class="product-image-card">
                <img src="/assets/img/<?= $product['picture']; ?>" alt="ASUS ROG Strix B650">
            </div>

            <!-- Informasi Produk -->
            <div class="product-info-card">
                <h2 class="product-title"><?= $product['name']; ?></h2>
                <p class="product-description">
                    <?= $product['description']; ?>
                </p>

                <div class="product-rating-overview">
                    <span class="rating-number">5.0</span>
                    <div class="stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>

                <div class="product-price-box">
                    <span class="price-amount">Rp.2.499.000</span>
                </div>

                <div class="purchase-action-row">
                    <form action="../../actions/cart/add.php" method="POST">
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn">-</button>
                            <span class="qty-value">1</span>
                            <button type="button" class="qty-btn">+</button>
                        </div>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button name="add" type="submit" class="btn-add-to-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAGIAN RATING & ULASAN -->
        <section class="rating-reviews-section">
            <h3 class="section-heading">Rating</h3>

            <div class="total-rating-box">
                <span class="total-rating-score">5.0/5.0</span>
                <div class="stars large">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>

            <!-- Ulasan 1 -->
            <div class="review-item">
                <div class="reviewer-header">
                    <div class="reviewer-avatar">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <span class="reviewer-name">Lynzz Leyn</span>
                </div>
                <div class="review-box">
                    <p>Great performance! Paired with Ryzen 7 7800X3D, VRM temperatures stay cool during heavy gaming. Wi-Fi 6E works fast and the M.2 heatsink slot helps a lot. Fast shipping and secure packaging.[cite: 3]</p>
                </div>
            </div>

            <!-- Ulasan 2 -->
            <div class="review-item">
                <div class="reviewer-header">
                    <div class="reviewer-avatar">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <span class="reviewer-name">Whoss Song</span>
                </div>
                <div class="review-box">
                    <p>Solid build quality, thick heatsinks, MSI BIOS is easy to understand. DDR5 Boost feature makes EXPO RAM run smoothly without issues. Highly recommended for mid-to-high-end gaming PC builds![cite: 3]</p>
                </div>
            </div>
        </section>

    </main>

    <!-- MAIN FOOTER -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-col brand-col">
                <img src="../../assets/img/Logo.png" alt="TechHub Logo">
                <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
            </div>
            <div class="footer-col">
                <h3>Shop</h3>
                <ul>
                    <li><a href="#">Processors</a></li>
                    <li><a href="#">Motherboards</a></li>
                    <li><a href="#">Graphics Cards</a></li>
                    <li><a href="#">RAM & Storage</a></li>
                    <li><a href="#">Power Supplies</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">How to Order</a></li>
                    <li><a href="#">Shipping</a></li>
                    <li><a href="#">Payment Methods</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>My Account</h3>
                <ul>
                    <li><a href="#">My Profile</a></li>
                    <li><a href="#">Recommendation</a></li>
                    <li><a href="#">Compare Products</a></li>
                    <li><a href="#">Shopping Cart</a></li>
                    <li><a href="#">Search Products</a></li>
                </ul>
            </div>
            <div class="footer-col social-col">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 TechHub. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>