<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../repositories/order-repository.php";

sessionStart();

// Nomor order: dari URL (?order=...) atau session; jika kosong dibuat otomatis
$order = $_GET['order'] ?? ($_SESSION['last_order'] ?? '');
$order = preg_replace('/[^A-Za-z0-9\-]/', '', $order); // hanya huruf, angka, dan tanda strip yang diterima

if ($order === '') {
    $order = generateOrderNumber();
}

$_SESSION['last_order'] = $order;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - TechHub</title>

    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <!-- Header -->
    <?php require_once __DIR__ . "/../../components/user/header.php" ?>

    <main class="payment-success">
        <div class="success-icon"><i class="fa-solid fa-check"></i></div>

        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-text">
            Your order has been paid successfully.<br>
            Thank you for shopping with TechHub!
        </p>

        <div class="order-box">
            <h2>Order Number</h2>
            <p><?= htmlspecialchars($order) ?></p>
        </div>

        <a href="../home/index.php" class="btn-continue">Continue Shopping</a>
    </main>

    <!-- Footer -->
    <?php require_once __DIR__ . "/../../components/user/footer.php" ?>
</body>

</html>
