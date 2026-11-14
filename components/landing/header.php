<?php
const BASE_URL = "http://localhost:3000";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="/styles/pages/category.css">
    <link rel="stylesheet" href="/styles/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://www.magnific.com/icon/compare_13372256#fromView=image_search&page=1&position=8&uuid=7d1a2e76-7dc2-4497-a0fd-f005ea8e12a9">
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
            <!-- Langsung arahkan mundur folder menggunakan ../ sesuai kebutuhan struktur proyekmu -->
            <a href="../landing/index.php">
                <img src="../../assets/img/Logo.png" alt="TechHub Logo" class="brand-logo-img">
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

            <!-- Diubah ke ikon compare yang lebih sesuai -->
            <a href="compare.php" class="icon-link" title="Compare">
                <i class="fa-solid fa-sliders"></i>
            </a>

            <a href="cart.php" class="icon-link" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>

            <a href="account.php" class="icon-link" title="User Account">
                <i class="fa-regular fa-user"></i>
            </a>
        </div>

    </header>