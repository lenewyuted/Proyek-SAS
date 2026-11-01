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

    <!-- Topbar Modular -->
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>

    <!-- Main Container -->
    <div class="admin-container">

        <!-- Sidebar Modular -->
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>

        <!-- Main Content Area -->
        <main class="main-content">

            <!-- Page Header & Back Link -->
            <div class="page-header-flex" style="margin-bottom: 20px;">
                <div class="header-col">
                    <h1>Add New Product</h1>
                    <p>Add a new product to your inventory.</p>
                </div>
                <div>
                    <a href="index.php" class="back-link" style="padding: 10px 10px;">
                        Back <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            </div>

            <!-- Form Container -->
            <form action="../../../actions/products/store.php" method="POST" enctype="multipart/form-data" class="product-form-create">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter product name" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category_id" name="category_id" placeholder="Select category" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (Rp)</label>
                        <input type="text" id="price" name="price" placeholder="Enter price" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" placeholder="Enter quantity" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="description">Product Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Write product description..."></textarea>
                </div>

                <!-- Upload Image Drag & Drop Area -->
                <div class="upload-drop-zone">
                    <input type="file" id="picture" name="picture" class="file-input-hidden" accept="image/*">
                    <div class="upload-icon-box">
                        <i class="bi bi-image"></i>
                    </div>
                    <h4>Drag & Drop Product Photo Here</h4>
                    <p>or <label for="picture" class="browse-link">Browse Photo from Computer</label></p>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-actions-center">
                    <button name="store" type="submit" class="btn-submit-add">+ Add Product</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>

            </form>

        </main>
    </div>

</body>

</html>