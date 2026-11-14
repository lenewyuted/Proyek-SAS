<?php 
// Jika menggunakan komponen header/footer terpisah
// require_once __DIR__ . "/../../../components/landing/header.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Checkout</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/checkout.css">
    <!-- Pastikan file CSS header & footer juga dilink jika tidak menggunakan require -->
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

   <?php require_once __DIR__ . "/../../../components/landing/header.php"; ?>

    <!-- MAIN CONTENT / CHECKOUT SECTION -->
    <main class="checkout-main-container">
        <!-- Judul Halaman -->
        <div class="checkout-header-title">
            <div>
                <h1>Checkout</h1>
                <p>Complete your details to finish checkout</p>
            </div>
            <a href="cart.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <form action="process-checkout.php" method="POST" class="checkout-layout">
            
            <!-- SISI KIRI: SHIPPING ADDRESS -->
            <section class="checkout-card shipping-address-section">
                <h2>Shipping Address</h2>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="Lenpit QWERTY" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="081234567890" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="Jalan Merdeka No.123" required>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="Pontianak" required>
                </div>

                <div class="form-group">
                    <label for="province">Province</label>
                    <input type="text" id="province" name="province" value="Kalimantan Barat" required>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="78132" required>
                </div>
            </section>

            <!-- SISI KANAN: SHIPPING METHOD & PAYMENT METHOD -->
            <div class="checkout-right-column">
                
                <!-- Shipping Method -->
                <section class="checkout-card">
                    <h2>Shipping Method</h2>
                    
                    <label class="method-option">
                        <input type="radio" name="shipping_method" value="regular" checked>
                        <div class="method-info">
                            <span>Regular (2–5 days)</span>
                            <strong>Rp 35.000</strong>
                        </div>
                    </label>

                    <label class="method-option">
                        <input type="radio" name="shipping_method" value="express">
                        <div class="method-info">
                            <span>Express (1–2 days)</span>
                            <strong>Rp 45.000</strong>
                        </div>
                    </label>
                </section>

                <!-- Payment Method -->
                <section class="checkout-card">
                    <h2>Payment Method</h2>
                    
                    <label class="method-option">
                        <input type="radio" name="payment_method" value="card" checked>
                        <div class="method-info">
                            <span>Credit / Debit Card</span>
                        </div>
                    </label>

                    <label class="method-option">
                        <input type="radio" name="payment_method" value="bank_transfer">
                        <div class="method-info">
                            <span>Bank Transfer (BCA, BNI, BRI, Mandiri)</span>
                        </div>
                    </label>

                    <label class="method-option">
                        <input type="radio" name="payment_method" value="ewallet">
                        <div class="method-info">
                            <span>E-Wallet (OVO, GoPay, DANA, LinkAja)</span>
                        </div>
                    </label>
                </section>

                <!-- Tombol Checkout -->
                <button type="submit" class="btn-checkout-submit">
                    Checkout
                </button>

            </div>
        </form>
    </main>

     <?php require_once __DIR__ . "/../../../components/landing/footer.php"; ?>

</body>

</html>