<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Buku - Perpustakaan Digital</title>
  <link href="https://googleapis.com" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../styles/base.css">
  <link rel="stylesheet" href="../../styles/admin/sidebar.css">
  <link rel="stylesheet" href="../../styles/admin/dashboard.css">    
</head>
<body>
  <div class="announcement-bar">
    Discover Premium PC Components Built for Performance, Speed, and Reliability
  </div>
  <header class="top-header">
    <div class="logo">
        <span class="logo-badge"></span>
        <img src="../../assets/img/Logo.png" alt="">
    </div>
    <div class="header-actions">
      <button class="btn-profile"><i class="bi bi-person-circle"></i> Admin</button>
      <button class="btn-logout"><i class="bi bi-box-arrow-right"></i> Log Out</button>
    </div>
  </header>
    <div class="admin-container">
        <?php require_once __DIR__ . "/../../components/admin/sidebar.php" ?>
        <main class="app-main">
            <?php require_once __DIR__ . "/dashboard/index.php" ?>
        </main>
    </div>
</body>
</html>