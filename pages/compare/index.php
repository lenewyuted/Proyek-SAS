<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . "/../../repositories/category-repository.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/specification-repository.php";
require_once __DIR__ . "/../../repositories/review-repository.php";

$categories = getAllCategories();
$selectedCategoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : ((int) ($categories[0]['id'] ?? 1));

// Cari nama kategori terpilih untuk judul/subtitle modal
$selectedCategoryName = 'Product';
foreach ($categories as $category) {
    if ((int) $category['id'] === $selectedCategoryId) {
        $selectedCategoryName = $category['name'];
        break;
    }
}

// Ambil produk dan data spesifikasi untuk kategori ini
$products = getProductsByCategoryId($selectedCategoryId);
$specKeys = getCategorySpecificationKeys($selectedCategoryId);
$specsMap = getProductSpecsMapByCategoryId($selectedCategoryId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Comparison - TechHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../../styles/pages/compare.css" />

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>

    <?php
    require_once __DIR__ . "/../../components/landing/header.php";
    ?>

    <!-- MAIN CONTENT -->
    <main class="container compare-page mt-3">
        <!-- PAGE TITLE & BACK LINK -->
        <div class="page-header">
            <div>
                <h1>Product Comparison</h1>
                <p class="com-sub">Compare specifications, features, and prices side by side to pick the best fit.</p>
            </div>
            <a href="../landing-page/index.php" class="back-link">Back <span>&rarr;</span></a>
        </div>

        <!-- CATEGORY SELECTOR BAR -->
        <div class="compare-category-bar">
            <label for="categoryFilter" class="category-filter-label">
                Compared Category:
            </label>
            <select id="categoryFilter" class="compare-category-select" onchange="changeCategory(this.value)">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) $category['id'] === $selectedCategoryId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- ADD PRODUCT SLOTS -->
        <section class="add-products-grid" aria-label="Select products to compare">
            <div class="spec-spacer" aria-hidden="true"></div>

            <!-- SLOT 1 -->
            <button
                type="button"
                class="add-product-card"
                id="slot-card-1"
                data-slot="1"
                onclick="openProductPicker(1)">
                <div class="slot-empty-content">
                    <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                    <span class="add-title">Add Product</span>
                    <span class="add-subtitle">Choose Product</span>
                </div>
                <div class="slot-filled-content d-none">
                    <span class="slot-remove-btn" onclick="clearSlot(1, event)" title="Remove product">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                    <img src="" alt="" class="slot-product-img">
                    <div class="slot-product-title"></div>
                    <div class="slot-product-price"></div>
                    <span class="slot-change-text"><i class="fa-solid fa-arrows-rotate mr-1"></i>Change</span>
                </div>
            </button>

            <!-- SLOT 2 -->
            <button
                type="button"
                class="add-product-card"
                id="slot-card-2"
                data-slot="2"
                onclick="openProductPicker(2)">
                <div class="slot-empty-content">
                    <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                    <span class="add-title">Add Product</span>
                    <span class="add-subtitle">Choose Product</span>
                </div>
                <div class="slot-filled-content d-none">
                    <span class="slot-remove-btn" onclick="clearSlot(2, event)" title="Remove product">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                    <img src="" alt="" class="slot-product-img">
                    <div class="slot-product-title"></div>
                    <div class="slot-product-price"></div>
                    <span class="slot-change-text"><i class="fa-solid fa-arrows-rotate mr-1"></i>Change</span>
                </div>
            </button>

            <!-- SLOT 3 -->
            <button
                type="button"
                class="add-product-card"
                id="slot-card-3"
                data-slot="3"
                onclick="openProductPicker(3)">
                <div class="slot-empty-content ">
                    <span class="plus-circle"><i class="fa-solid fa-plus"></i></span>
                    <span class="add-title">Add Product</span>
                    <span class="add-subtitle">Choose Product</span>
                </div>
                <div class="slot-filled-content d-none">
                    <span class="slot-remove-btn" onclick="clearSlot(3, event)" title="Remove product">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                    <img src="" alt="" class="slot-product-img">
                    <div class="slot-product-title"></div>
                    <div class="slot-product-price"></div>
                    <span class="slot-change-text"><i class="fa-solid fa-arrows-rotate mr-1"></i>Change</span>
                </div>
            </button>
        </section>

        <!-- COMPARISON TABLE -->
        <section class="table-wrapper" aria-label="Comparison specification table">
            <table class="compare-table">
                <tbody>
                    <!-- Baris Nama Produk -->
                    <tr class="row-highlight">
                        <td class="spec-label">Product Name</td>
                        <td id="product_name_1" class="spec-cell font-weight-bold text-primary">-</td>
                        <td id="product_name_2" class="spec-cell font-weight-bold text-primary">-</td>
                        <td id="product_name_3" class="spec-cell font-weight-bold text-primary">-</td>
                    </tr>
                    <!-- Baris Harga -->
                    <tr class="row-highlight">
                        <td class="spec-label">Price</td>
                        <td id="product_price_1" class="spec-cell font-weight-bold">-</td>
                        <td id="product_price_2" class="spec-cell font-weight-bold">-</td>
                        <td id="product_price_3" class="spec-cell font-weight-bold">-</td>
                    </tr>
                    <!-- Baris Spesifikasi Dinamis Sesuai Kategori -->
                    <?php if (!empty($specKeys)): ?>
                        <?php foreach ($specKeys as $specName): ?>
                            <tr>
                                <td class="spec-label"><?= htmlspecialchars($specName) ?></td>
                                <td class="spec-cell" data-spec-name="<?= htmlspecialchars($specName, ENT_QUOTES) ?>" data-slot="1">-</td>
                                <td class="spec-cell" data-spec-name="<?= htmlspecialchars($specName, ENT_QUOTES) ?>" data-slot="2">-</td>
                                <td class="spec-cell" data-spec-name="<?= htmlspecialchars($specName, ENT_QUOTES) ?>" data-slot="3">-</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-info mr-1"></i> No detailed specifications found for category <?= htmlspecialchars($selectedCategoryName) ?>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- MODAL SELECT PRODUCTS -->
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
                                Select <?= htmlspecialchars($selectedCategoryName) ?> Product
                            </h5>
                            <p class="compare-picker-subtitle">
                                Select product for Slot <span id="modalTargetSlotText" class="font-weight-bold text-primary">
                                    1
                                </span>
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
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $ratingSummary = getProductRatingSummary((int) $product['id']);
                                    $avgRating = number_format((float) ($ratingSummary['average_rating'] ?? 0), 1);
                                    $totalReviews = (int) ($ratingSummary['total_reviews'] ?? 0);

                                    $productPayload = [
                                        'id' => (int) $product['id'],
                                        'name' => $product['name'] ?? '',
                                        'price' => (float) ($product['price'] ?? 0),
                                        'formatted_price' => 'Rp. ' . number_format((float) ($product['price'] ?? 0), 0, ',', '.'),
                                        'picture' => $product['picture'] ?? 'default.png'
                                    ];

                                    ?>
                                    <div
                                        class="picker-product-card"
                                        role="button"
                                        tabindex="0"
                                        data-product-id="<?= (int) $product['id'] ?>"
                                        onclick="selectProductForSlot(<?= htmlspecialchars(json_encode($productPayload), ENT_QUOTES, 'UTF-8') ?>)">
                                        <div class="picker-product-img">
                                            <img
                                                src="../../assets/img/<?= htmlspecialchars($product['picture'] ?? 'default.png') ?>"
                                                alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>">
                                        </div>

                                        <h3 class="picker-product-title"><?= htmlspecialchars($product['name'] ?? '') ?></h3>

                                        <div class="picker-product-rating">
                                            <?php if ($totalReviews > 0): ?>
                                                <span><?= htmlspecialchars($avgRating) ?></span>
                                                <i class="fa-solid fa-star"></i>
                                                <small>(<?= $totalReviews ?>)</small>
                                            <?php else: ?>
                                                <span class="no-rating-text">No rating</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="picker-product-footer">
                                            <span class="picker-product-price">
                                                <?= $productPayload['formatted_price'] ?>
                                            </span>
                                            <span class="picker-select-badge">Select</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <p id="noAvailableProductsMsg" class="empty-state d-none">
                                    All products in category <?= htmlspecialchars($selectedCategoryName) ?> have already been selected in other slots.
                                </p>
                            <?php else: ?>
                                <p class="empty-state">No products found in category <?= htmlspecialchars($selectedCategoryName) ?>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- COMPARE BUTTON -->
        <div class="compare-action">
            <button type="button" class="btn-compare-submit" onclick="compareProducts()">
                <i class="fa-solid fa-code-compare mr-2"></i>Compare Now
            </button>
        </div>

    </main>

    <?php
    require_once __DIR__ . "/../../components/landing/footer.php";
    ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>
        // Data spesifikasi produk kategori aktif dari PHP
        const PRODUCT_SPECS = <?= json_encode($specsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        let activeSlot = 1;
        const slotData = {
            1: null,
            2: null,
            3: null
        };

        function changeCategory(categoryId) {
            window.location.href = 'index.php?category_id=' + categoryId;
        }

        function openProductPicker(targetSlot) {
            activeSlot = targetSlot;

            const modalSlotTextElement = document.getElementById('modalTargetSlotText');
            if (modalSlotTextElement) {
                modalSlotTextElement.textContent = targetSlot;
            }
            // console.log("Isi Slot Data", slotData);

            // Kumpulkan ID produk yang sedang dipakai di slot lain
            const usedProductIds = [];
            for (let slotNumber = 1; slotNumber <= 3; slotNumber++) {
                if (slotNumber !== targetSlot && slotData[slotNumber] !== null) {
                    usedProductIds.push(slotData[slotNumber].id);
                }
            }

            // console.log("Slot tujuan:", targetSlot);
            // console.log("ID produk yang sedang dipakai di slot lain:", usedProductIds);
            // console.log("Isi Slot Data Setelah di Push", slotData);

            //  Periksa setiap kartu produk di modal
            const productCards = document.querySelectorAll('.picker-product-card');
            console.log(productCards)
            let totalVisibleProducts = 0;

            productCards.forEach(function(productCard) {
                const productId = parseInt(productCard.getAttribute('data-product-id'));

                // Jika ID produk ini ada di daftar usedProductIds, sembunyikan
                if (usedProductIds.includes(productId)) {
                    productCard.style.display = 'none';
                } else {
                    productCard.style.display = '';
                    totalVisibleProducts++;
                }
            });


            $('#selectProductModal').modal('show');
        }

        function selectProductForSlot(product) {
            slotData[activeSlot] = product;

            // Update tampilan kartu slot
            const card = document.getElementById('slot-card-' + activeSlot);
            if (card) {
                card.classList.add('has-product');
                card.querySelector('.slot-empty-content').classList.add('d-none');

                const filled = card.querySelector('.slot-filled-content');
                filled.classList.remove('d-none');
                filled.querySelector('.slot-product-img').src = '../../assets/img/' + (product.picture || 'default.png');
                filled.querySelector('.slot-product-img').alt = product.name;
                filled.querySelector('.slot-product-title').textContent = product.name;
                filled.querySelector('.slot-product-price').textContent = product.formatted_price;
            }

            $('#selectProductModal').modal('hide');
        }

        function clearSlot(slot, event) {
            if (event) event.stopPropagation();

            slotData[slot] = null;

            // 1. Reset tampilan kartu slot
            const card = document.getElementById('slot-card-' + slot);
            if (card) {
                card.classList.remove('has-product');
                card.querySelector('.slot-empty-content').classList.remove('d-none');

                const filled = card.querySelector('.slot-filled-content');
                filled.classList.add('d-none');
                filled.querySelector('.slot-product-img').src = '';
                filled.querySelector('.slot-product-title').textContent = '';
                filled.querySelector('.slot-product-price').textContent = '';
            }

            // 2. Reset kolom tabel khusus slot ini saja (spec-label kolom pertama tetap aman)
            const nameEl = document.getElementById('product_name_' + slot);
            if (nameEl) nameEl.textContent = '-';

            const priceEl = document.getElementById('product_price_' + slot);
            if (priceEl) priceEl.textContent = '-';

            document.querySelectorAll('.spec-cell[data-slot="' + slot + '"]').forEach(cell => {
                cell.textContent = '-';
            });
        }

        function compareProducts() {
            const filledCount = Object.values(slotData).filter(Boolean).length;
            if (filledCount < 2) {
                alert('Please select at least 2 products to compare.');
                return;
            }

            // Isi seluruh tabel perbandingan untuk tiap slot (1, 2, 3)
            [1, 2, 3].forEach(slot => {
                const product = slotData[slot];
                let specs = {};

                if (product) {
                    document.getElementById('product_name_' + slot).textContent = product.name;
                    document.getElementById('product_price_' + slot).textContent = product.formatted_price;

                    specs = PRODUCT_SPECS[product.id];
                }

                document.querySelectorAll('.spec-cell[data-slot="' + slot + '"]').forEach(cell => {
                    const specName = cell.getAttribute('data-spec-name');
                    cell.textContent = specs[specName] || '-';
                });
            });

            // Scroll ke tabel perbandingan
            const tableSection = document.querySelector('.table-wrapper');
            if (tableSection) {
                tableSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }
    </script>
</body>

</html>