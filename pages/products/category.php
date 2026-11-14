<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="/styles/pages/category.css">
    <link rel="stylesheet" href="/styles/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

 <!-- bar bagian atas -->
    <div class="top-bar">
       <span>Discover Premium PC Components Built for Performance, Speed, and Reliability</span>
    </div>

<!-- main navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <img src="/assets/img/Logo.png" alt="">
            </div>
        </a>

        <!-- search bar -->
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search for parts to build your dream PC..." required>
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <!-- tombol aksi kanan -->
        <div class="nav-icons">
            <a href="thumbs-up.php" class="icon-link" title="Likes">
                <i class="fa-solid fa-thumbs-up"></i>
            </a>

            <a href="compare.php" class="icon-link" title="Compare">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>

            <a href="cart.php" class="icon-link" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>

            <a href="account.php" class="icon-link" title="User Account">
                <i class="fa-regular fa-user"></i>
            </a>
        </div>
    </div>
</nav>

<!-- banner hero promo (tengah) -->
<section class="hero-banner">
    <div class="hero-container">

        <img id="banner-img" src="/assets/img/Banner-1.png" alt="Banner Promo" class="hero-img">
        
        <div class="banner-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
        </div>
    </div>
</section>

<!-- bagian category -->
<section class="category-section">
    <div class="category-container">
        <h2>Category</h2>
        <div class="category-grid">
            
            <!-- Item 1 -->
            <div class="category-item">
                <a href="category.php?cat=cpu" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/Intel_Core_i9-9900K.png" alt="CPU"></div>
                </a>
                <div class="category-name">CPU</div>
            </div>

            <!-- Item 2 -->
            <div class="category-item">
                <a href="category.php?cat=motherboard" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/Motherboard (2).png" alt="Motherboard"></div>
                </a>
                <div class="category-name">Motherboard</div>
            </div>

            <!-- Item 3 -->
            <div class="category-item">
                <a href="category.php?cat=ram" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/RAM.png" alt="RAM"></div>
                </a>
                <div class="category-name">RAM</div>
            </div>

            <!-- Item 4 -->
            <div class="category-item">
                <a href="category.php?cat=ssd" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/SSD.png" alt="SSD"></div>
                </a>
                <div class="category-name">SSD</div>
            </div>

            <!-- Item 5 -->
            <div class="category-item">
                <a href="category.php?cat=hdd" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/HDD.png" alt="HDD"></div>
                </a>
                <div class="category-name">HDD</div>
            </div>

            <!-- Item 6 -->
            <div class="category-item">
                <a href="category.php?cat=gpu" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/GPU.png" alt="GPU"></div>
                </a>
                <div class="category-name">GPU</div>
            </div>

            <!-- Item 7 -->
            <div class="category-item">
                <a href="category.php?cat=psu" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/PSU.png" alt="PSU"></div>
                </a>
                <div class="category-name">PSU</div>
            </div>

            <!-- Item 8 -->
            <div class="category-item">
                <a href="category.php?cat=monitor" class="category-card">
                    <div class="cat-icon"><img src="/assets/img/category/Monitor.png" alt="Monitor"></div>
                </a>
                <div class="category-name">Monitor</div>
            </div>

        </div>
    </div>
</section>



<section class="products-section">
    <div class="products-container">
        <h2>Products</h2>
        
        <div class="products-grid">
            <!-- product 1 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/HDD WD hardisk.png" alt="HDD WD Hardisk">
                </div>
                <h3 class="product-title">HDD WD hardisk sata internal 500</h3>
                <div class="product-rating">
                    <span class="rating-num">4.6</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 176.000</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 2 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Seagate.png" alt="Seagate Internal SATA">
                </div>
                <h3 class="product-title">Seagate Internal SATA</h3>
                <div class="product-rating">
                    <span class="rating-num">4.8</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 854.700</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 3 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Hardisk 500 gb.png" alt="Hardisk 500 GB">
                </div>
                <h3 class="product-title">Hardisk 500 gb Master komputer</h3>
                <div class="product-rating">
                    <span class="rating-num">4.8</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 328.000</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 4 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Hdd Eksternal.png" alt="Hdd Eksternal Toshiba">
                </div>
                <h3 class="product-title">Hdd Eksternal Toshiba 2TB</h3>
                <div class="product-rating">
                    <span class="rating-num">4.5</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 173.630</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 5 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/HDD EXTERNAL.png" alt="HDD EXTERNAL RX7">
                </div>
                <h3 class="product-title">HDD EXTERNAL RX7 Elements</h3>
                <div class="product-rating">
                    <span class="rating-num">4.5</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 352.000</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 6 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/HPE Server HDD.png" alt="HPE Server HDD">
                </div>
                <h3 class="product-title">HPE Server HDD 2TB SATA 3</h3>
                <div class="product-rating">
                    <span class="rating-num">5.0</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 6.680.300</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 7 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Harddisk External.png" alt="Harddisk External Wd">
                </div>
                <h3 class="product-title">Harddisk External Wd Element 1TB</h3>
                <div class="product-rating">
                    <span class="rating-num">4.8</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 799.000</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 8 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Seagate Skyhawk.png" alt="Seagate Skyhawk">
                </div>
                <h3 class="product-title">Seagate Skyhawk Ai St20000ve002</h3>
                <div class="product-rating">
                    <span class="rating-num">4.9</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 20.629.953</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 9 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Hardisk External.png" alt="Hardisk External 1TB">
                </div>
                <h3 class="product-title">Hardisk External 1TB WD Elements</h3>
                <div class="product-rating">
                    <span class="rating-num">4.1</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 475.000</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>

            <!-- product 10 -->
            <div class="product-card">
                <div class="product-img">
                    <img src="/assets/img/products/Adata Pro.png" alt="Adata Pro Usb">
                </div>
                <h3 class="product-title">Adata Pro Usb External Hdd</h3>
                <div class="product-rating">
                    <span class="rating-num">5.0</span>
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="product-bottom">
                    <span class="product-price">Rp 8.490.116</span>
                    <button class="cart-btn" title="Add to Cart"><i class="fa-solid fa-cart-shopping"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    const banners = [
        "/assets/img/banner/Banner-1.png",
        "/assets/img/banner/Banner-2.png",
        "/assets/img/banner/Banner-3.png"
    ];

    let currentIndex = 0;
    const bannerImg = document.getElementById("banner-img");
    const dots = document.querySelectorAll(".dot");

    function showBanner(index) {
        currentIndex = index;
        bannerImg.src = banners[currentIndex];
        
        dots.forEach((dot, i) => {
            if (i === currentIndex) {
                dot.classList.add("active");
            } else {
                dot.classList.remove("active");
            }
        });
    }

    function nextBanner() {
        currentIndex = (currentIndex + 1) % banners.length;
        showBanner(currentIndex);
    }

    function currentSlide(index) {
        showBanner(index);
        resetTimer();
    }

    let bannerInterval = setInterval(nextBanner, 3000);

    function resetTimer() {
        clearInterval(bannerInterval);
        bannerInterval = setInterval(nextBanner, 3000);
    }
</script>

<footer class="main-footer">
    <div class="footer-container">
        
        <div class="footer-col brand-info">
            <img src="/assets/img/Logo.png" alt="TechHub Logo" class="footer-logo">
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
                <a href="#" class="social-card ig" aria-label="Instagram">
                    <div class="social-icon"><i class="fa-brands fa-instagram"></i></div>
                </a>
                <a href="#" class="social-card fb" aria-label="Facebook">
                    <div class="social-icon"><i class="fa-brands fa-facebook"></i></div>
                </a>
                <a href="#" class="social-card yt" aria-label="YouTube">
                    <div class="social-icon"><i class="fa-brands fa-youtube"></i></div>
                </a>
                <a href="#" class="social-card wa" aria-label="WhatsApp">
                    <div class="social-icon"><i class="fa-brands fa-whatsapp"></i></div>
                </a>
                <a href="#" class="social-card tt" aria-label="TikTok">
                    <div class="social-icon"><i class="fa-brands fa-tiktok"></i></div>
                </a>
                <a href="#" class="social-card xt" aria-label="X">
                    <div class="social-icon"><i class="fa-brands fa-x"></i></div>
                </a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 TechHub. All rights reserved.</p>
    </div>
</footer>

</body>
</html>