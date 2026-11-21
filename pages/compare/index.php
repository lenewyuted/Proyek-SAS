<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . "/../../repositories/product-repository.php";

$products = getAllProducts();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perbandingan Produk - TechHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../styles/pages/compare.css" />

    <!-- Bootstrap 4.6 CSS (Dibawa oleh header agar dropdown & komponen konsisten) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

</head>

<body>

    <?php
    require_once __DIR__ . "/../../components/landing/header.php";
    ?>

    <!-- MAIN CONTENT -->
    <main class="container compare-page">
        <!-- PAGE TITLE & BACK LINK -->
        <div class="page-header">
            <h1>Perbandingan Produk</h1>
            <a href="javascript:history.back()" class="back-link">
                <span>Back</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>

        <!-- ADD PRODUCT SLOTS -->
        <section class="add-products-grid" aria-label="Pilih produk untuk dibandingkan">
            <div class="spec-spacer" aria-hidden="true"></div>

            <!-- Trigger pada kartu Add Product (bisa dipasang di slot 1, 2, 3) -->
            <button
                type="button"
                class="add-product-card"
                data-slot="1"
                data-toggle="modal"
                data-target="#selectProductModal">
                <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                <span class="add-title">Add Product</span>
                <span class="add-subtitle">Choose Product</span>
            </button>



            <button type="button" class="add-product-card" data-slot="2" data-toggle="modal"
                data-target="#selectProductModal">
                <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                <span class="add-title">Add Product</span>
                <span class="add-subtitle">Choose Product</span>
            </button>

            <button type="button" class="add-product-card" data-slot="3" data-toggle="modal"
                data-target="#selectProductModal">
                <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                <span class="add-title">Add Product</span>
                <span class="add-subtitle">Choose Product</span>
            </button>
        </section>

        <!-- COMPARISON TABLE (4 Columns x 6 Rows) -->
        <section class="table-wrapper" aria-label="Tabel spesifikasi perbandingan">
            <table class="compare-table">
                <tbody>
                    <tr>
                        <td>Spesifikasi</td>
                        <td id="product_specification_1"></td>
                        <td id="product_specification_2"></td>
                        <td id="product_specification_3"></td>
                    </tr>
                    <tr>
                        <td>Harga</td>
                        <td id="product_price_1"></td>
                        <td id="product_price_2"></td>
                        <td id="product_price_3"></td>
                    </tr>
                    <tr>
                        <td>CPU</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Dukungan Processor</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Chipset</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Form Factor</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <!-- Modal Select Products to Compare -->
            <div
                class="modal fade compare-picker-modal"
                id="selectProductModal"
                tabindex="-1"
                aria-labelledby="selectProductModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content compare-picker-content">
                        <div class="modal-header compare-picker-header">
                            <div>
                                <h5 class="modal-title compare-picker-title" id="selectProductModalLabel">
                                    Select Products to Compare
                                </h5>
                                <p class="compare-picker-subtitle">
                                    Select products from any category to compare
                                </p>
                            </div>
                            <button
                                type="button"
                                class="compare-picker-close"
                                data-dismiss="modal"
                                aria-label="Close">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="modal-body compare-picker-body">
                            <div class="picker-products-grid">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $prod): ?>
                                        <?php
                                        $ratingSummary = function_exists('getProductRatingSummary')
                                            ? getProductRatingSummary((int) $prod['id'])
                                            : ['average_rating' => 0, 'total_reviews' => 0];
                                        $avgRating = number_format((float) ($ratingSummary['average_rating'] ?? 0), 1);
                                        $totalReviews = (int) ($ratingSummary['total_reviews'] ?? 0);
                                        ?>
                                        <div
                                            class="picker-product-card"
                                            role="button"
                                            tabindex="0"
                                            data-id="<?= (int) $prod['id'] ?>"
                                            data-name="<?= htmlspecialchars($prod['name'] ?? '', ENT_QUOTES) ?>"
                                            data-price="<?= (float) ($prod['price'] ?? 0) ?>"
                                            data-category="<?= htmlspecialchars($prod['category_name'] ?? '-', ENT_QUOTES) ?>"
                                            data-brand="<?= htmlspecialchars($prod['brand'] ?? '-', ENT_QUOTES) ?>"
                                            data-stock="<?= htmlspecialchars($prod['stock'] ?? '0', ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($prod['description'] ?? '-', ENT_QUOTES) ?>"
                                            data-picture="<?= htmlspecialchars($prod['picture'] ?? 'default.png', ENT_QUOTES) ?>">
                                            <div class="picker-product-img">
                                                <img
                                                    src="../../assets/img/<?= htmlspecialchars($prod['picture'] ?? 'default.png') ?>"
                                                    alt="<?= htmlspecialchars($prod['name'] ?? 'Product') ?>">
                                            </div>

                                            <h3 class="picker-product-title"><?= htmlspecialchars($prod['name'] ?? '') ?></h3>

                                            <div class="picker-product-rating">
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

                                            <div class="picker-product-footer">
                                                <span class="picker-product-price">
                                                    Rp.<?= number_format((float) ($prod['price'] ?? 0), 0, ',', '.') ?>
                                                </span>
                                                <span class="picker-select-badge">Pilih</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="empty-state">Produk tidak ditemukan.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- COMPARE BUTTON -->
        <div class="compare-action">
            <button type="button" class="btn-compare">Compare</button>
        </div>
    </main>

    <?php
    require_once __DIR__ . "/../../components/landing/footer.php";
    ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>

    </script>
</body>

</html>