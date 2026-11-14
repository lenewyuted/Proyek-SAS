<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Payment Successful</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/payment/success.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <!-- MAIN CONTENT / PAYMENT SUCCESS SECTION -->
    <main class="success-main-container">
        <div class="success-content-wrapper">
            
            <!-- Ikon Centang Hijau -->
            <div class="success-icon-circle">
                <i class="fa-solid fa-check"></i>
            </div>

            <!-- Judul & Subjudul -->
            <h1 class="success-title">Payment Successful!</h1>
            <p class="success-subtitle">
                Your order has been paid successfully.<br>
                Thank you for shopping with LaptopPro!
            </p>

            <!-- Kotak Nomor Pesanan -->
            <div class="order-number-box">
                <span class="order-label">Order Number</span>
                <strong class="order-code">LP202509201234</strong>
            </div>

            <!-- Tombol Continue Shopping -->
            <a href="../landing/index.php" class="btn-continue-shopping">
                Continue Shopping
            </a>

        </div>
    </main>

    <!-- MAIN FOOTER -->
    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>
</body>

</html>