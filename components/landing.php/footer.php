<?php

$baseUrl = $baseUrl ?? '/Proyek-SAS';
?>
<footer class="footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <span class="logo-text">TECH<span>HUB</span></span>
      <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
    </div>

    <div>
      <h4>Shop</h4>
      <ul>
        <li><a href="#">Processors</a></li>
        <li><a href="#">Motherboards</a></li>
        <li><a href="#">Graphics Cards</a></li>
        <li><a href="#">RAM &amp; Storage</a></li>
        <li><a href="#">Power Supplies</a></li>
      </ul>
    </div>

    <div>
      <h4>Support</h4>
      <ul>
        <li><a href="#">How to Order</a></li>
        <li><a href="#">Shipping</a></li>
        <li><a href="#">Payment Methods</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="<?= $baseUrl ?>/pages/user/contact/index.php">Contact Us</a></li>
      </ul>
    </div>

    <div>
      <h4>My Account</h4>
      <ul>
        <li><a href="<?= $baseUrl ?>/pages/user/profile/index.php">My Profile</a></li>
        <li><a href="<?= $baseUrl ?>/pages/user/products/recommendation.php">Recommendation</a></li>
        <li><a href="<?= $baseUrl ?>/pages/user/compare/index.php">Compare Products</a></li>
        <li><a href="<?= $baseUrl ?>/pages/user/cart/index.php">Shopping Cart</a></li>
        <li><a href="<?= $baseUrl ?>/pages/user/products/search.php">Search Products</a></li>
      </ul>
    </div>

    <div>
      <h4>Follow Us</h4>
      <div class="socials">
        <a href="#">Instagram</a>
        <a href="#">Facebook</a>
        <a href="#">YouTube</a>
        <a href="#">WhatsApp</a>
        <a href="#">TikTok</a>
        <a href="#">X</a>
      </div>
    </div>
  </div>
  <p class="copyright">© <?= date('Y') ?> TechHub. All rights reserved.</p>
</footer>