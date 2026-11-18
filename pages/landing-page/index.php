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
$queryProducts .= " ORDER BY products.id ASC LIMIT 6";

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
        <!-- <div class="banner-wrapper">
            <img src="../../assets/img/banner/Banner-1.png" alt="Lenovo Legion Monitor Banner">

            <div class="banner-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div> -->
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
                    <!-- Kartu membungkus kotak dan label sekaligus agar menjadi satu kesatuan link -->
                    <a href="#" class="category-card">
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

    <!-- FEATURED PRODUCT -->
    <section class="section-container">
        <h2 class="section-title">Featured Product</h2>
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): ?>
                    <a href="../products/show.php?id=<?= $prod['id'] ?>">
                        <div class="product-card">
                            <div class="product-img">
                                <img src="../../assets/img/<?= $prod["picture"] ?>" alt="<?= $prod["name"] ?>">

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