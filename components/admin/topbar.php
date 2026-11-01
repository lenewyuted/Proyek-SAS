<?php


$baseUrl = $baseUrl ?? '/Proyek-SAS';


$adminName = $_SESSION['user']['name'] ?? 'Admin';

$adminPassword = $_SESSION['user']['password'] ??'';
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<style>
  .admin-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 24px;
    background: #fff;
    border-bottom: 1px solid #e3ecff;
    font-family: 'Kanit', sans-serif;
  }
  .admin-topbar .brand {
    display: flex;
    align-items: baseline;
    gap: 8px;
    text-decoration: none;
  }
  .admin-topbar .brand-name { font-size: 20px; font-weight: 600; color: #0a4bb5; }
  .admin-topbar .brand-name span { color: #000; }
  .admin-topbar .brand-tag {
    font-size: 11px;
    background: #e3ecff;
    color: #0a4bb5;
    padding: 2px 8px;
    border-radius: 10px;
  }
  .admin-topbar .page-title { font-size: 16px; color: #333; }
  .admin-topbar .right { display: flex; align-items: center; gap: 14px; }
  .admin-topbar .user { font-size: 14px; color: #111; }
  .admin-topbar .btn-logout {
    background: #c62828;
    color: #fff;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 4px;
    font-size: 13px;
  }
  .admin-topbar .btn-logout:hover { background: #a11f1f; }
</style>

<header class="admin-topbar">
  <a href="<?= $baseUrl ?>/pages/admin/dashboard.php" class="brand">
    <span class="brand-name">TECH<span>HUB</span></span>
    <span class="brand-tag">Admin</span>
  </a>

  <div class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></div>

  <div class="right">
    <span class="user"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
    <a href="<?= $baseUrl ?>/actions/auth/logout.php" class="btn-logout">Logout</a>
  </div>
</header>