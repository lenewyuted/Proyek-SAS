<?php
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";
require_once __DIR__ . "/../../../repositories/user-repository.php";
require_once __DIR__ . "/../../../repositories/review-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$perPage = 4;
$currentPage = max(1, intval($_GET['page'] ?? 1));

$search = trim($_GET['search'] ?? "");
$categoryId = trim($_GET['category_id'] ?? "");

$allProducts = getProducts($search, $categoryId, 1, 1000);
if (!is_array($allProducts)) {
  $allProducts = [];
}

$productsWithRating = array_map(function ($prod) {
  $ratingSummary = getProductRatingSummary((int) ($prod['id'] ?? 0));
  $prod['_avg_rating'] = (float) ($ratingSummary['average_rating'] ?? 0);
  $prod['_total_reviews'] = (int) ($ratingSummary['total_reviews'] ?? 0);
  return $prod;
}, $allProducts);

usort($productsWithRating, function ($a, $b) {
  if ($a['_avg_rating'] === $b['_avg_rating']) {
    return $b['_total_reviews'] <=> $a['_total_reviews'];
  }
  return $b['_avg_rating'] <=> $a['_avg_rating'];
});

$offset = ($currentPage - 1) * $perPage;
$products = array_slice($productsWithRating, $offset, $perPage);

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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../../styles/base.css">
  <link rel="stylesheet" href="../../../styles/components/admin/sidebar.css">
  <link rel="stylesheet" href="../../../styles/components/admin/topbar.css">
  <link rel="stylesheet" href="../../../styles/admin/dashboard.css">
</head>

<body>
  <!-- Topbar -->
  <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>
  <div class="admin-container">
    <!-- Sidebar -->
    <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
    <main class="main-content">
      <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome to the TechHub admin panel!</p>
      </div>
      <div class="stats-grid">
        <a href="../products/index.php">
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-info">
              <span class="stat-label">Total Products</span>
              <h2 class="stat-value"><?= $totalProducts ?></h2>
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
              <h2 class="stat-value"><?= $totalCategories ?></h2>
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
              <h2 class="stat-value"><?= $totalUsers ?></h2>
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
        </div>
        <div class="admin-note-card">
          <div class="note-header">
            <h3>Admin Note</h3>
            <span class="note-status" id="noteStatus">Tersimpan otomatis</span>
          </div>
          <textarea id="adminNoteInput" placeholder="Tulis catatan admin di sini..."></textarea>
        </div>
      </div>

      <div class="bottom-grid">
        <!-- Bungkus list produk dengan styling flexbox mendatar -->
        <div class="products-list-grid">
          <?php foreach ($products as $product): ?>
            <div class="product-item-card">
              <div class="product-img-box">
                <img src="/assets/img/<?= htmlspecialchars($product['picture'] ?? '') ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>">
              </div>
              <h4 class="product-name"><?= htmlspecialchars($product['name'] ?? '') ?></h4>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="calendar-card">
          <div class="calendar-header">
            <span class="cal-month" id="calMonthLabel"></span>
            <div class="cal-nav-group">
              <button type="button" class="cal-nav" id="prevMonthBtn" aria-label="Bulan sebelumnya">&lt;</button>
              <button type="button" class="cal-nav" id="nextMonthBtn" aria-label="Bulan berikutnya">&gt;</button>
            </div>
          </div>
          <div class="calendar-grid" id="calendarGrid"></div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // 1. Auto-save Admin Note ke localStorage
    (function() {
      const noteTextarea = document.getElementById('adminNoteInput');
      const noteStatus = document.getElementById('noteStatus');
      const storageKey = 'techhub_admin_note_v1';

      if (noteTextarea) {
        noteTextarea.value = localStorage.getItem(storageKey) || '';

        let statusTimeout;
        noteTextarea.addEventListener('input', function() {
          localStorage.setItem(storageKey, noteTextarea.value);
          if (noteStatus) {
            noteStatus.textContent = 'Menyimpan...';
            clearTimeout(statusTimeout);
            statusTimeout = setTimeout(() => {
              noteStatus.textContent = 'Tersimpan otomatis';
            }, 300);
          }
        });
      }
    })();

    // 2. Kalender dinamis (ringkas)
    (function() {
      const monthLabel = document.getElementById('calMonthLabel');
      const grid = document.getElementById('calendarGrid');
      const prevBtn = document.getElementById('prevMonthBtn');
      const nextBtn = document.getElementById('nextMonthBtn');

      if (!monthLabel || !grid) return;

      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
      let date = new Date();

      function render() {
        const now = new Date();
        const y = date.getFullYear();
        const m = date.getMonth();

        monthLabel.textContent = `${months[m]} ${y}`;

        const firstDay = (new Date(y, m, 1).getDay() + 6) % 7;
        const lastDate = new Date(y, m + 1, 0).getDate();

        let html = days.map(d => `<div class="day-name">${d}</div>`).join('');
        html += '<span></span>'.repeat(firstDay);

        for (let d = 1; d <= lastDate; d++) {
          const isToday = d === now.getDate() && m === now.getMonth() && y === now.getFullYear();
          html += `<span class="day${isToday ? ' active' : ''}">${d}</span>`;
        }

        grid.innerHTML = html;
      }

      prevBtn?.addEventListener('click', () => {
        date.setMonth(date.getMonth() - 1);
        render();
      });

      nextBtn?.addEventListener('click', () => {
        date.setMonth(date.getMonth() + 1);
        render();
      });

      // Update otomatis setiap menit saat pergantian hari
      setInterval(render, 60000);
      render();
    })();
  </script>
</body>

</html>