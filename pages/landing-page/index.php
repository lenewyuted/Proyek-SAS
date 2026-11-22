<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . "/../../repositories/category-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/review-repository.php";

$banners          = getLatestProducts();
$categories       = getCategoriesLandingPage();
$search           = trim($_GET['q'] ?? '');
$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;

$queryProducts = "SELECT products.*,
                         categories.name AS category_name,
                         COALESCE(AVG(pr.rating), 0) AS avg_rating,
                         COUNT(pr.id) AS total_reviews
                  FROM products
                  LEFT JOIN categories ON products.category_id = categories.id
                  LEFT JOIN `products-rating` pr ON pr.product_id = products.id";

$params = [];
if ($search !== '') {
    $queryProducts .= " WHERE products.name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$queryProducts .= " GROUP BY products.id
                  ORDER BY avg_rating DESC, total_reviews DESC, products.id ASC
                  LIMIT 6";

$stmtProd = $pdo->prepare($queryProducts);
foreach ($params as $key => $val) {
    $stmtProd->bindValue($key, $val, PDO::PARAM_STR);
}
$stmtProd->execute();
$products = $stmtProd->fetchAll();

$categoryProducts = [];
$selectedCategoryName = '';

if ($selectedCategory > 0) {
    foreach ($categories as $catItem) {
        if ((int) ($catItem['id'] ?? 0) === $selectedCategory) {
            $selectedCategoryName = (string) ($catItem['name'] ?? '');
            break;
        }
    }

    $queryCatProd = "SELECT products.*, categories.name AS category_name
                     FROM products
                     LEFT JOIN categories ON products.category_id = categories.id
                     WHERE products.category_id = :category_id
                     ORDER BY products.id";
    $stmtCatProd = $pdo->prepare($queryCatProd);
    $stmtCatProd->bindValue(':category_id', $selectedCategory, PDO::PARAM_INT);
    $stmtCatProd->execute();
    $categoryProducts = $stmtCatProd->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Power Your Build</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Framework & Icon Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="../../styles/pages/landing/style.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/index.css">
</head>

<body>

    <!-- MAIN HEADER -->
    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

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
                    $catId = (int) ($cat['id'] ?? 0);
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
                    $avgRating = number_format($ratingSummary['average_rating'], 1);
                    $totalReviews = $ratingSummary['total_reviews'];
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
                                    <small>(<?= (int) $totalReviews ?>)</small>
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

    <!-- MAIN FOOTER -->
    <?php require_once __DIR__ . "/../../components/landing/footer.php" ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script>
    $('.carousel').carousel({
        interval: 2000
    })
</script>

</html>