<?php
// Variabel $activePage (opsional) menentukan menu mana yang diberi class "active"
$activePage = $activePage ?? '';
?>
<aside class="sidebar">
    <a href="#" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="#" class="<?= $activePage === 'products' ? 'active' : '' ?>"><i class="fa-solid fa-cube"></i> Products</a>
    <a href="#" class="<?= $activePage === 'categories' ? 'active' : '' ?>"><i class="fa-solid fa-table-cells-large"></i> Categories</a>
    <a href="profile.php" class="<?= $activePage === 'profile' ? 'active' : '' ?>"><i class="fa-regular fa-user"></i> Profile</a>
</aside>
