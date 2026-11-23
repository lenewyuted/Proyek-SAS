<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . "/../../repositories/category-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/review-repository.php";

$search           = trim((string) ($_GET['q'] ?? ''));
$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$isSearching      = ($search !== '');

$banners    = [];
$categories = [];
$products   = [];

if ($isSearching) {
    $products = getFilteredProducts($pdo, $search, 0, 0);
} else {
    $banners    = getLatestProducts();
    $categories = getCategoriesLandingPage();
    $products   = getFilteredProducts($pdo, '', 0, 6);
}

$categoryProducts     = [];
$selectedCategoryName = '';

if (!$isSearching && $selectedCategory > 0) {
    foreach ($categories as $catItem) {
        if ((int) ($catItem['id'] ?? 0) === $selectedCategory) {
            $selectedCategoryName = (string) ($catItem['name'] ?? '');
            break;
        }
    }

    $categoryProducts = getFilteredProducts($pdo, '', $selectedCategory, 0);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isSearching ? 'Search Results For "' . htmlspecialchars($search) . '" - TechHub' : 'TechHub - Power Your Build' ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Framework & Icon Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../../styles/pages/landing/style.css">
    <link rel="stylesheet" href="../../styles/pages/search-result.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/index.css">
</head>

<body>

    <!-- MAIN HEADER -->
    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <?php if ($isSearching): ?>
        <?php $totalFound = count($products); ?>
        <section class="section-container search-results-section">
            <div class="search-page-topbar">
                <div>
                    <h1 class="search-page-title">
                        Search Results For <span style="color: #004aad;">"<?= htmlspecialchars($search) ?>"</span>
                    </h1>
                    <p class="search-results-count">
                        <?= $totalFound ?> <?= $totalFound === 1 ? 'result' : 'results' ?> found
                    </p>
                </div>

                <a href="index.php" class="search-back-link">
                    Back <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>

            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $prod): ?>
                        <?php
                        $ratingSummary = getProductRatingSummary((int) $prod['id']);
                        $avgRating     = number_format((float) ($ratingSummary['average_rating'] ?? 0), 1);
                        $totalReviews  = (int) ($ratingSummary['total_reviews'] ?? 0);
                        ?>
                        <a href="../products/show.php?id=<?= (int) $prod['id'] ?>">
                            <div class="product-card">
                                <div class="product-img">
                                    <img src="../../assets/img/<?= htmlspecialchars((string) ($prod["picture"] ?? '')) ?>"
                                        alt="<?= htmlspecialchars((string) ($prod["name"] ?? '')) ?>">
                                </div>
                                <h3 class="product-title"><?= htmlspecialchars((string) ($prod['name'] ?? '')) ?></h3>
                                <div class="product-rating">
                                    <?php if ($totalReviews > 0): ?>
                                        <span><?= htmlspecialchars($avgRating) ?></span>
                                        <i class="fa-solid fa-star"></i>
                                        <small>(<?= $totalReviews ?>)</small>
                                    <?php else: ?>
                                        <span>0.0</span>
                                        <i class="fa-regular fa-star"></i>
                                        <small>(0)</small>
                                    <?php endif; ?>
                                </div>
                                <div class="product-footer">
                                    <span class="product-price">Rp.<?= number_format((float) ($prod['price'] ?? 0), 0, ',', '.') ?></span>
                                    <button class="btn-add-cart" type="button" aria-label="Tambah ke keranjang">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">No products found for "<?= htmlspecialchars($search) ?>".</p>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <!-- HERO BANNER -->
        <section class="hero-banner">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="../../assets/img/banner/Banner-1.png" alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="../../assets/img/banner/Banner-2.png" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="../../assets/img/banner/Banner-3.png" alt="Third slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>

        <!-- CATEGORY -->
        <section class="section-container">
            <h2 class="section-title">Category</h2>
            <div class="category-grid">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <?php
                        $catId    = (int) ($cat['id'] ?? 0);
                        $isActive = ($selectedCategory === $catId);
                        ?>
                        <a href="?category=<?= $catId ?>#category-products" class="category-card<?= $isActive ? ' active' : '' ?>">
                            <div class="category-box">
                                <div class="category-icon">
                                    <img src="../../assets/img/category/<?= htmlspecialchars($cat['picture'] ?? 'cat-cpu.png') ?>"
                                        alt="<?= htmlspecialchars($cat['name']) ?>">
                                </div>
                            </div>
                            <span class="category-label"><?= htmlspecialchars($cat['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($selectedCategory > 0): ?>
            <section class="section-container" id="category-products">
                <div class="category-products-header">
                    <h2 class="section-title">
                        <?= htmlspecialchars($selectedCategoryName !== '' ? $selectedCategoryName : 'Category Products') ?>
                    </h2>
                    <a href="index.php" class="category-close-link">Tutup Kategori</a>
                </div>

                <div class="products-grid category-products-grid">
                    <?php if (!empty($categoryProducts)): ?>
                        <?php foreach ($categoryProducts as $catProd): ?>
                            <?php
                            $catRatingSummary = getProductRatingSummary((int) $catProd['id']);
                            $catAvgRating     = number_format($catRatingSummary['average_rating'], 1);
                            $catTotalReviews  = (int) $catRatingSummary['total_reviews'];
                            ?>
                            <a href="../products/show.php?id=<?= (int) $catProd['id'] ?>" class="product-link-item">
                                <div class="product-card category-product-card">
                                    <div class="product-img">
                                        <img src="../../assets/img/<?= htmlspecialchars($catProd["picture"]) ?>" alt="<?= htmlspecialchars($catProd["name"]) ?>">
                                    </div>
                                    <h3 class="product-title"><?= htmlspecialchars($catProd['name']) ?></h3>
                                    <div class="product-rating">
                                        <?php if ($catTotalReviews > 0): ?>
                                            <span><?= htmlspecialchars($catAvgRating) ?></span>
                                            <i class="fa-solid fa-star"></i>
                                            <small>(<?= $catTotalReviews ?>)</small>
                                        <?php else: ?>
                                            <span>0.0</span>
                                            <i class="fa-regular fa-star"></i>
                                            <small>(0)</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-footer">
                                        <span class="product-price">Rp.<?= number_format($catProd['price'], 0, ',', '.') ?></span>
                                        <button class="btn-add-cart" type="button" aria-label="Tambah ke keranjang">
                                            <i class="fa-solid fa-cart-shopping"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-state">Belum ada produk pada kategori ini.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- FEATURED PRODUCT -->
        <section class="section-container">
            <h2 class="section-title">Featured Product</h2>
            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $prod): ?>
                        <?php
                        $ratingSummary = getProductRatingSummary((int) $prod['id']);
                        $avgRating     = number_format($ratingSummary['average_rating'], 1);
                        $totalReviews  = (int) $ratingSummary['total_reviews'];
                        ?>
                        <a href="../products/show.php?id=<?= (int) $prod['id'] ?>">
                            <div class="product-card">
                                <div class="product-img">
                                    <img src="../../assets/img/<?= htmlspecialchars($prod["picture"]) ?>" alt="<?= htmlspecialchars($prod["name"]) ?>">
                                </div>
                                <h3 class="product-title"><?= htmlspecialchars($prod['name']) ?></h3>
                                <div class="product-rating">
                                    <?php if ($totalReviews > 0): ?>
                                        <span><?= htmlspecialchars($avgRating) ?></span>
                                        <i class="fa-solid fa-star"></i>
                                        <small>(<?= $totalReviews ?>)</small>
                                    <?php else: ?>
                                        <span>0.0</span>
                                        <i class="fa-regular fa-star"></i>
                                        <small>(0)</small>
                                    <?php endif; ?>
                                </div>
                                <div class="product-footer">
                                    <span class="product-price">Rp.<?= number_format($prod['price'], 0, ',', '.') ?></span>
                                    <button class="btn-add-cart" type="button" aria-label="Tambah ke keranjang">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
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
    <?php endif; ?>

    <!-- MAIN FOOTER -->
    <?php require_once __DIR__ . "/../../components/landing/footer.php" ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<?php if (!$isSearching): ?>
    <script>
        $('.carousel').carousel({
            interval: 2000
        })
    </script>
<?php endif; ?>

</html>