<link rel="stylesheet" href="/styles/components/header.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- bar bagian atas -->
    <div class="top-bar">
       <span>Discover Premium PC Components Built for Performance, Speed, and Reliability</span>
    </div>

<!-- main navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <img src="/assets/img/Logo.png" alt="">
            </div>
        </a>

<!-- search bar -->
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search for parts to build your dream PC..." required>
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <!-- tombol aksi kanan -->
        <div class="nav-icons">
            <a href="thumbs-up.php" class="icon-link" title="Likes">
                <i class="fa-solid fa-thumbs-up"></i>
            </a>

            <a href="compare.php" class="icon-link" title="Compare">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>

            <a href="cart.php" class="icon-link" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>

            <a href="account.php" class="icon-link" title="User Account">
                <i class="fa-regular fa-user"></i>
            </a>
        </div>
        
    </div>
</nav>
</body>