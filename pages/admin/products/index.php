<?php
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";

$perPage = 4;
$currentPage = max(1, intval($_GET['page'] ?? 1));


$search = trim($_GET['search'] ?? "");
$categoryId = trim($_GET['category_id'] ?? "");
if (!ctype_digit($categoryId)) { //menegecek apakah sebuah variabelnya nilainya angka atau bukan 
  $categoryId = "";
}

$categories = getCategories();
$products = getProducts($search, $categoryId, $currentPage, $perPage);

$totalProducts = countOfProducts($search, $categoryId);
$totalPages = ceil($totalProducts / $perPage);
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
  <link rel="stylesheet" href="../../../styles/admin/pagination.css">
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
        <p>Add, edit, delete, and manage laptop products</p>
      </div>
      <form method="GET" action="" class="action-bar">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input name="search" value="<?= $search ?>" type="text" placeholder="Search for product name...">
        </div>
        <button class="btn-search">Search</button>
        <a href="create.php" class="btn-add-product">
          <i class="bi bi-plus-lg"></i> Add Product
        </a>
      </form>
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
              <!-- Tambahkan atribut onclick dan cursor pointer pada style tr -->
              <tr onclick="window.location.href='show.php?id=<?= $product['id'] ?>'" style="cursor: pointer;">
                <td>
                  <div class="product-img-box">
                    <img src="/assets/img/<?= $product['picture'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                  </div>
                </td>
                <td class="product-name"><?= $product['name']; ?></td>
                <td><span class="badge-category"><?= $product['category_name']; ?></span></td>
                <td class="product-price"><?= formatRupiah($product['price']); ?></td>
                <td class="product-stock"><?= $product['stock']; ?></td>
                <td>
                  <div class="action-buttons">
                    <!-- Tambahkan event onclick.stopPropagation() agar tombol Edit & Delete tidak ikut membuka show.php -->
                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn-edit" onclick="event.stopPropagation();">Edit</a>
                    <a href="../../../actions/products/destroy.php?id=<?= $product['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus produk ini?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <!-- Tombol Previous -->
        <?php if ($currentPage > 1): ?>
          <a href="?<?= http_build_query(['search' => $search, 'category_id' => $categoryId, 'page' => $currentPage - 1]) ?>">
            <span class="pagination-btn"><i class="bi bi-chevron-left"></i></span>
          </a>
        <?php else: ?>
          <span class="pagination-btn is-disabled"><i class="bi bi-chevron-left"></i></span>
        <?php endif; ?>

        <!-- Tombol Next -->
        <?php if ($currentPage < $totalPages): ?>
          <a href="?<?= http_build_query(['search' => $search, 'category_id' => $categoryId, 'page' => $currentPage + 1]) ?>">
            <span class="pagination-btn"><i class="bi bi-chevron-right"></i></span>
          </a>
        <?php else: ?>
          <span class="pagination-btn is-disabled"><i class="bi bi-chevron-right"></i></span>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>

</html>