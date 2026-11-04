<?php
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";
require_once __DIR__ . "/../../../repositories/user-repository.php";

$totalProducts = countProducts();
$totalCategories = countCategories();
$totalUsers = countUsers();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechHub Admin - Dashboard</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://googleapis.com" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../../styles/base.css">
  <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
  <link rel="stylesheet" href="../../../styles/admin/topbar.css">
  <link rel="stylesheet" href="../../../styles/admin/dashboard.css">  
</head>
<body>
  <!-- Topbar -->
  <?php require_once __DIR__ . "/../../../components/admin/topbar.php"?>
  <div class="admin-container">
    <!-- Sidebar -->
    <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
    <main class="main-content">
      <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome to the TechHub admin panel!</p>
      </div>
      <a href="../products/index.php">
        <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="bi bi-box-seam"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Products</span>
            <h2 class="stat-value"><?= $totalProducts?></h2>
            <span class="stat-sub">Items in store</span>
          </div>
        </div>
      </a>
      <a href="../categories/index.php">
        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="bi bi-receipt"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Categories</span>
            <h2 class="stat-value"><?= $totalCategories?></h2>
            <span class="stat-sub">Organized products</span>
          </div>
        </div>
      </a>
      <a href="../users/index.php">
        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="bi bi-grid"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Users</span>
            <h2 class="stat-value"><?= $totalUsers?></h2>
            <span class="stat-sub">Managed users</span>
          </div>
        </div>
      </a>
      <a href="../profile/edit.php">
        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="bi bi-person-vcard"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Profile</span>
            <h2 class="stat-value text-name">Admin</h2>
            <span class="stat-sub">Your account</span>
          </div>
        </div>
      </a>
      </div>
      <div class="middle-grid">
        <div class="welcome-banner">
          <div class="banner-text">
            <h3>Welcome Back, Admin!</h3>
            <p>Semoga harimu menyenangkan.<br>Kelola produk, pesanan, dan pengguna dengan mudah di sini.</p>
            <a href="../products/index.php" class="btn-manage">Mulai Kelola &rarr;</a>
          </div>
          <div class="banner-illustration">
            <img src="../../../assets/images/laptop-illustration.svg" alt="Illustration">
          </div>
        </div>
        <div class="admin-note-card">
          <h3>Admin Note</h3>
          <textarea placeholder="Tulis catatan admin di sini..."></textarea>
        </div>
      </div>
      <div class="bottom-grid">  
        <div class="products-list-grid">
          <div class="product-item-card">
            <div class="product-img-box">
              <img src="../../../assets/images/psu-msi.png" alt="POWER SUPPLY MSI MAG A650BN">
            </div>
            <h4>POWER SUPPLY MSI MAG A650BN</h4>
          </div>
          <div class="product-item-card">
            <div class="product-img-box">
              <img src="../../../assets/images/mobo-msi.png" alt="MSI PRO B650-S WIFI Mainboard">
            </div>
            <h4>MSI PRO B650-S WIFI Mainboard</h4>
          </div>
          <div class="product-item-card">
            <div class="product-img-box">
              <img src="../../../assets/images/monitor-lenovo.png" alt="Lenovo Legion 27Q-2C Monitor 67b">
            </div>
            <h4>Lenovo Legion 27Q-2C Monitor 67b</h4>
          </div>
        </div>
        <div class="calendar-card">
          <div class="calendar-header">
            <button class="cal-nav">&lt;</button>
            <span class="cal-month">Oktober 2026</span>
            <button class="cal-nav">&gt;</button>
          </div>
          <div class="calendar-grid">
            <div class="day-name">Sen</div><div class="day-name">Sel</div><div class="day-name">Rab</div>
            <div class="day-name">Kom</div><div class="day-name">Jum</div><div class="day-name">Sab</div><div class="day-name">Min</div>
            <span class="day muted">28</span><span class="day muted">29</span><span class="day muted">30</span>
            <span class="day">1</span><span class="day">2</span><span class="day">3</span><span class="day">4</span>
            <span class="day">5</span><span class="day">6</span><span class="day">7</span>
            <span class="day">8</span><span class="day">9</span><span class="day">10</span><span class="day">11</span>
            <span class="day">12</span><span class="day">13</span><span class="day">14</span>
            <span class="day">15</span><span class="day">16</span><span class="day">17</span><span class="day">18</span>
            <span class="day">19</span><span class="day">20</span><span class="day">21</span>
            <span class="day">22</span><span class="day">23</span><span class="day">24</span><span class="day">25</span>
            <span class="day">26</span><span class="day">27</span><span class="day">28</span>
            <span class="day">29</span><span class="day active">30</span><span class="day">31</span><span class="day muted">1</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>