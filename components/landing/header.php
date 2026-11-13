<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="/styles/pages/category.css">
    <link rel="stylesheet" href="/styles/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../styles/index.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
</head>

<body>

    <!-- bar bagian atas -->
    <div class="top-bar">
        <span>Discover Premium PC Components Built for Performance, Speed, and Reliability</span>
    </div>

    <!-- main navbar -->
    <header class="top-header">
            <div class="logo">
                <a href="index.php">
                    <img src="/../../assets/img/Logo.png" alt="TechHub Logo" class="brand-logo-img">
                </a>
            </div>

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
       
    </header>

