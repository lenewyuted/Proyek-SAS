<?php
session_start();
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/user-repository.php";

// 1. Wajib Login: jika belum login, simpan redirect dan arahkan ke login
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['redirect_after_login'] = '../../pages/checkout/index.php';
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk melanjutkan checkout.';
    header('Location: ../auth/login.php');
    exit;
}

// 2. Keranjang tidak boleh kosong
$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    $_SESSION['flash_error'] = 'Keranjang belanja Anda masih kosong.';
    header('Location: ../cart/index.php');
    exit;
}

// Ambil data user lengkap dari DB untuk autofill alamat
$currentUser = getUser($_SESSION['user']['id']);

// Hitung subtotal produk dari DB
$cartSubtotal = 0;
$checkoutItems = [];
foreach ($cartItems as $cartItem) {
    $product = getProduct($cartItem['product_id']);
    if ($product) {
        $itemTotal = $product['price'] * $cartItem['quantity'];
        $cartSubtotal += $itemTotal;
        $checkoutItems[] = [
            'product'  => $product,
            'quantity' => $cartItem['quantity'],
            'subtotal' => $itemTotal,
        ];
    }
}

$defaultShippingCost = 35000;
$totalAmount = $cartSubtotal + $defaultShippingCost;

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
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
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

   <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <!-- MAIN CONTENT / CHECKOUT SECTION -->
    <main class="checkout-main-container">
        <!-- Judul Halaman -->
        <div class="checkout-header-title">
            <div>
                <h1>Checkout</h1>
                <p>Complete your details to finish checkout</p>
            </div>
            <a href="../cart/index.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <?php if ($flashError): ?>
            <div style="background-color: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form action="../../actions/checkout/process.php" method="POST" class="checkout-layout">
            
            <!-- SISI KIRI: SHIPPING ADDRESS -->
            <section class="checkout-card shipping-address-section">
                <h2>Shipping Address</h2>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($currentUser['phone_number'] ?? '') ?>" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($currentUser['address'] ?? '') ?>" placeholder="Nama jalan, nomor rumah, RT/RW" required>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($currentUser['city'] ?? '') ?>" placeholder="Kota / Kabupaten" required>
                </div>

                <div class="form-group">
                    <label for="province">Province</label>
                    <input type="text" id="province" name="province" value="<?= htmlspecialchars($currentUser['province'] ?? '') ?>" placeholder="Provinsi" required>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($currentUser['postal_code'] ?? '') ?>" placeholder="Kode Pos" required>
                </div>
            </section>

            <!-- SISI KANAN: SHIPPING METHOD, PAYMENT METHOD & SUMMARY -->
            <div class="checkout-right-column">
                
                <!-- Shipping Method -->
                <section class="checkout-card">
                    <h2>Shipping Method</h2>
                    
                    <label class="method-option">
                        <input type="radio" name="shipping_method" value="regular" checked>
                        <div class="method-info">
                            <span>Regular (2-5 days)</span>
                            <strong>Rp 35.000</strong>
                        </div>
                    </label>

                    <label class="method-option">
                        <input type="radio" name="shipping_method" value="express">
                        <div class="method-info">
                            <span>Express (1-2 days)</span>
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

                <!-- Order Summary Card -->
                <section class="checkout-card">
                    <h2>Order Summary</h2>
                    <div style="margin-bottom: 15px;">
                        <?php foreach ($checkoutItems as $checkoutItem): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                <span><?= htmlspecialchars($checkoutItem['product']['name']) ?> (x<?= $checkoutItem['quantity'] ?>)</span>
                                <strong><?= formatRupiah($checkoutItem['subtotal']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="border-top: 1px solid #eee; padding-top: 10px; margin-bottom: 8px; display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Subtotal Produk</span>
                        <strong><?= formatRupiah($cartSubtotal) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                        <span>Ongkos Kirim</span>
                        <strong id="shipping-display">Rp 35.000</strong>
                    </div>
                    <div style="border-top: 2px solid #ddd; padding-top: 10px; margin-bottom: 15px; display: flex; justify-content: space-between; font-size: 16px;">
                        <strong>Total Tagihan</strong>
                        <strong style="color: var(--primary-blue, #0d6efd);" id="total-display"><?= formatRupiah($totalAmount) ?></strong>
                    </div>

                    <!-- Tombol Checkout -->
                    <button type="submit" class="btn-checkout-submit">
                        <i class="fa-solid fa-lock"></i> Bayar Sekarang
                    </button>
                </section>

            </div>
        </form>
    </main>

    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

    <script>
        const subtotal = <?= $cartSubtotal ?>;
        const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
        const shippingDisplay = document.getElementById('shipping-display');
        const totalDisplay = document.getElementById('total-display');

        function formatRupiahJS(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        shippingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const shippingCost = this.value === 'express' ? 45000 : 35000;
                const total = subtotal + shippingCost;
                shippingDisplay.innerText = formatRupiahJS(shippingCost);
                totalDisplay.innerText = formatRupiahJS(total);
            });
        });
    </script>
</body>

</html>