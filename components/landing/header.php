<?php

$baseUrl = $baseUrl ?? '/Proyek-SAS';
?>
<div class="top-banner">
  Discover Premium PC Components Built for Performance, Speed, and Reliability
</div>

<header class="header">
  <link rel="stylesheet" href="../../styles/components/header.css">
  <a href="<?= $baseUrl ?>/index.php" class="logo">
    <span class="logo-text">TECH<span>HUB</span></span>
    <small>Your Pro Build</small>
  </a>

  <form class="search-box" action="<?= $baseUrl ?>/pages/user/products/search.php" method="get">
    <input type="text" name="q" placeholder="Search for parts to build your dream PC...">
    <button type="submit" aria-label="Cari">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
    </button>
  </form>

  <nav class="header-icons">
    <a href="<?= $baseUrl ?>/pages/user/products/recommendation.php" aria-label="Rekomendasi">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 11v9H3v-9h4zm0 0l4-8c1.5 0 2.5 1 2.2 2.5L12.5 9H19a2 2 0 012 2.3l-1.2 6.5A2 2 0 0117.8 19H7"/></svg>
    </a>
    <a href="<?= $baseUrl ?>/pages/user/compare/index.php" aria-label="Bandingkan">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M12 4v16M7 9l-2 3 2 3M17 9l2 3-2 3"/></svg>
    </a>
    <a href="<?= $baseUrl ?>/pages/user/cart/index.php" aria-label="Keranjang">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h2l2.4 11h10.3L20 7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>
    </a>
    <a href="<?= $baseUrl ?>/pages/user/profile/index.php" aria-label="Akun">
      <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3.5"/><path d="M5.5 18.5c1.5-3 4-4 6.5-4s5 1 6.5 4"/></svg>
    </a>
  </nav>
</header>