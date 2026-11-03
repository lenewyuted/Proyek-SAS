<?php
require_once __DIR__ . "/../../../middleware/auth.php";
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header('location: index.php');
    exit;
}

$product = getProduct($id);
$categories = getCategories();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors'], $_SESSION['old']);

$product = array_merge($product, $old);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Add New Product</title>

    <!-- Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/admin/products.css">
</head>

<body>
    <!-- Topbar -->
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
        <main class="main-content">
            <div class="page-header-flex" style="margin-bottom: 20px;">
                <div class="header-col">
                    <h1>Edit Product</h1>
                    <p>Update product information, category, and stock.</p>
                </div>
                <div>
                    <a href="index.php" class="back-link" style="padding: 10px 10px;">
                        Back <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <form action="../../../actions/products/update.php" method="POST" enctype="multipart/form-data" class="product-form-create">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input value="<?= htmlspecialchars($product['name']) ?>" type="text" id="name" name="name" placeholder="Enter product name">
                        <?php if (isset($errors['name'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['name'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                    <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= $category['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['category_id'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (Rp)</label>
                        <input value="<?= htmlspecialchars($product['price']) ?>" type="text" id="price" name="price" placeholder="Enter price">
                        <?php if (isset($errors['price'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['price'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input value="<?= htmlspecialchars($product['stock']) ?>" type="number" id="stock" name="stock" placeholder="Enter quantity">
                        <?php if (isset($errors['stock'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['stock'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="description">Product Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Write product description..."><?= htmlspecialchars($product['description']) ?></textarea>
                    <?php if (isset($errors['description'])) : ?>
                        <p style="font-size: 12px; margin-top: 4px; color: red;">
                            <?= $errors['description'] ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="upload-drop-zone" id="preview-container" style="cursor: pointer;">
                    <input type="file" id="picture" name="picture" accept="image/*" style="display: none;">
                    <div id="upload-content" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                        <?php if (!empty($product['picture'])) : ?>
                            <img src="../../../assets/img/<?= htmlspecialchars($product['picture']) ?>"
                                alt="Product Image"
                                style="max-height: 150px; max-width: 100%; object-fit: contain;">
                            <p>Klik untuk mengganti gambar</p>
                        <?php else : ?>
                            <div class="upload-icon-box">
                                <i class="bi bi-image"></i>
                            </div>
                            <h4>Drag & Drop Product Photo Here</h4>
                            <p>or <span class="browse-link">Browse Photo from Computer</span></p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($errors['picture'])) : ?>
                        <p style="font-size: 12px; margin-top: 4px; color: red;">
                            <?= $errors['picture'] ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="form-actions-center">
                    <button name="update" type="submit" class="btn-submit-add">+ Add Product</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        const fileInput = document.getElementById('picture');
        const previewContainer = document.getElementById('preview-container');
        const uploadContent = document.getElementById('upload-content');
        // Agar ketika teks "Browse Photo" atau kotaknya diklik, file explorer terbuka
        previewContainer.addEventListener('click', function() {
            fileInput.click();
        });
        // Mencegah event klik ganda saat klik langsung pada input file
        fileInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        // Event saat user memilih file gambar
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Ganti isi dalam kotak upload dengan gambar preview
                    uploadContent.innerHTML = `
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px;">
                            <img src="${e.target.result}" alt="Preview" style="max-height: 150px; max-width: 100%; object-fit: contain; border-radius: 6px; margin-bottom: 8px;">
                            <span style="font-size: 14px; color: #333; font-weight: 600;">${file.name}</span>
                            <span style="font-size: 12px; color: #666; margin-top: 4px;">Klik untuk mengganti gambar</span>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>