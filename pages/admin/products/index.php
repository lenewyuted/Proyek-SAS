<?php
require_once __DIR__ . "/../../../repositories/product-repository.php";
$products = getProducts();
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
  <link rel="stylesheet" href="../../../styles/admin/products.css">
</head>

<body>
  <!-- Topbar -->
  <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>
  <div class="admin-container">
    <!-- Sidebar -->
    <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
    <main class="main-content">
      <div class="page-header">
        <h1>Manage Products</h1>
        <p>Add, edit, delete, and manage laptop products.</p>
      </div>
      <div class="action-bar">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Search for parts to build your dream PC...">
        </div>
        <button class="btn-search">Search</button>
        <a href="create.php" class="btn-add-product">
          <i class="bi bi-plus-lg"></i> Add Product
        </a>
      </div>
      <div class="table-card">
        <table class="product-table">
          <thead>
            <tr>
              <th>Picture</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td>
                  <div class="product-img-box">
                    <img src="<?= $product['picture'];?>" alt="Intel Core i9-9900K">
                  </div>
                </td>
                <td class="product-name"><?= $product['name'];?></td>
                <td><span class="badge-category"><?= $product['category_name'];?></span></td>
                <td class="product-price"><?= formatRupiah($product['price']);?></td>
                <td class="product-stock"><?= $product['stock'];?></td>
                <td>
                  <div class="action-buttons">
                    <a href="edit.php?id=1" class="btn-edit">Edit</a>
                    <a href="../../../actions/products/destroy.php?id=<?= $product['id'];?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>