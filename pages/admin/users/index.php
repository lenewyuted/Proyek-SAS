<?php
require_once __DIR__ . "/../../../repositories/user-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$perPage = 2;
$currentPage = max(1, intval($_GET['page'] ?? 1));

$search = trim($_GET['search'] ?? "");

$users = getUsers($search, $currentPage, $perPage);

$totalUsers = countOfUsers($search);
$totalPages = ceil($totalUsers / $perPage);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechHub Admin - Manage Users</title>

  <!-- Font & icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS -->
  <link rel="stylesheet" href="../../../styles/base.css">
  <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
  <link rel="stylesheet" href="../../../styles/admin/topbar.css">
  <link rel="stylesheet" href="../../../styles/admin/users.css"> <!-- Memakai style products agar tabelnya seragam -->
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
        <h1>Manage Users</h1>
        <p>Add, edit, delete, and manage system users</p>
      </div>
      <form method="GET" action="" class="action-bar">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input name="search" value="<?= htmlspecialchars($search) ?>" type="text" placeholder="Search for user name or email...">
        </div>
        <button class="btn-search">Search</button>
        <a href="create.php" class="btn-add-product">
          <i class="bi bi-plus-lg"></i> Add User
        </a>
      </form>
      <div class="table-card">
        <table class="product-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th style="padding-left: 93px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <!-- Baris tabel dapat diklik untuk melihat detail, kecuali tombol aksi -->
              <tr>
                <td class="product-name"><?= htmlspecialchars($user['name']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><span class="badge-category"><?= htmlspecialchars($user['role'] ?? 'User'); ?></span></td>
                <td>
                  <div class="action-buttons">
                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn-edit" onclick="event.stopPropagation();">Edit</a>
                    <a href="../../../actions/users/destroy.php?id=<?= $user['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus user ini?')">Delete</a>
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
          <a href="?<?= http_build_query(['search' => $search, 'page' => $currentPage - 1]) ?>">
            <span class="pagination-btn"><i class="bi bi-chevron-left"></i></span>
          </a>
        <?php else: ?>
          <span class="pagination-btn is-disabled"><i class="bi bi-chevron-left"></i></span>
        <?php endif; ?>

        <!-- Tombol Next -->
        <?php if ($currentPage < $totalPages): ?>
          <a href="?<?= http_build_query(['search' => $search, 'page' => $currentPage + 1]) ?>">
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