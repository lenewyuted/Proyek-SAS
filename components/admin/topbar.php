<?php 
const BASE_URL = "http://localhost:3000"; 
$user = $_SESSION["user"] ?? '';
?>

<link rel="stylesheet" href="../../styles/base.css">
<div class="announcement-bar">
  Discover Premium PC Components Built for Performance, Speed, and Reliability
</div>
<header class="top-header">
  <div class="logo">
    <span class="logo-badge"></span>
    <img src="<?= BASE_URL ?>/assets/img/Logo.png" alt="Logo">
  </div>
  <div class="header-actions">
    <button class="btn-profile"><i class="bi bi-person-circle"></i><?= $user['name'] ?? ''?></button>
    <a onclick="return confirm('Yakin logout ? Tidak menyesal')" href="/actions/auth/logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Log Out</a>
  </div>
</header>