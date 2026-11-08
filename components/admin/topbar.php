<?php 
const BASE_URL = "http://localhost:3000"; 
$loggedInUser = $_SESSION["user"] ?? '';
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
    <a href="<?= BASE_URL ?>/pages/admin/profile/edit.php">
            <button class="btn-profile">
                <i class="bi bi-person-circle"></i><?= htmlspecialchars($loggedInUser['name'] ?? '') ?>
            </button>
        </a>
        
        <!-- Tombol Logout menggunakan BASE_URL mutlak -->
        <a onclick="return confirm('Yakin logout ? Tidak menyesal')" href="<?= BASE_URL ?>/actions/auth/logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Log Out
        </a>
  </div>
</header>