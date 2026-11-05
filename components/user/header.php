<?php
// Komponen header untuk halaman pengguna. Variabel $search (opsional) dipakai untuk mengisi kolom pencarian.
$search = $search ?? '';
?>
<div class="top-announcement">
    Discover Premium PC Components Built for Performance, Speed, and Reliability
</div>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="../home/index.php">
                <img src="../../asset/images/logo.png" alt="TechHub Logo" id="brandlogo">
            </a>
        </div>

        <div class="search-box">
            <form action="../home/index.php" method="GET">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search for parts to build your dream PC...">
                <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="header-nav-icons">
            <a href="../recommendation/index.php" aria-label="Recommendation"><i class="fa-regular fa-thumbs-up"></i></a>
            <a href="#" aria-label="Compare"><i class="fa-solid fa-code-compare"></i></a>
            <a href="#" aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
            <a href="#" aria-label="Profile"><i class="fa-regular fa-circle-user"></i></a>
        </div>
    </div>
</header>
