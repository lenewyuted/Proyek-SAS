<?php
require_once __DIR__ . "/../../../middleware/auth.php";
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";
requireRole(["admin"]);


$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('location: index.php');
    exit;
}

$product = getProduct($id);

// cari nama kategori dari category_id
$categoryName = '';
foreach (getCategories("", "", 1, 100) as $category) {
    if ($category['id'] == $product['category_id']) {
        $categoryName = $category['name'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechHub Admin - Manage Products</title>

  <!-- Font & icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="../../../styles/base.css">
  <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
  <link rel="stylesheet" href="../../../styles/admin/topbar.css">
  <link rel="stylesheet" href="../../../styles/admin/products.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>

    <div class="admin-container">
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>

        <main class="main-content">
            <!-- Header Halaman -->
            <div class="page-header-flex" style="margin-bottom: 25px;">
                <div class="header-col">
                    <h1>Detail Product</h1>
                    <p>View product details and information</p>
                </div>
                <div>
                    <a href="index.php" class="back-link">
                        Back &rarr;
                    </a>
                </div>
            </div>

            <!-- Kontainer Tata Letak Utama -->
            <div class="product-show-container">
                
                <!-- Kolom Kiri: Informasi Produk -->
                <div class="product-info-section">
                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Product Name</label>
                            <div class="show-field-value"><?= htmlspecialchars($product['name']) ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Category</label>
                            <div class="show-field-value"><?= htmlspecialchars($categoryName) ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Price (Rp)</label>
                            <div class="show-field-value"><?= number_format($product['price'], 0, ',', '.') ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Stock</label>
                            <div class="show-field-value"><?= htmlspecialchars($product['stock']) ?></div>
                        </div>
                    </div>

                    <div class="show-field-group">
                        <label>Product Description</label>
                        <div class="show-field-value textarea-value">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Kartu Gambar Produk -->
                <div class="product-image-card">
                    <h4>Product Image</h4>
                    <div class="image-preview-box">
                        <?php if (!empty($product['picture'])): ?>
                            <img src="../../../assets/img/<?= htmlspecialchars($product['picture']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <i class="bi bi-image no-image-icon"></i>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>