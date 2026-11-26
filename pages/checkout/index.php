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
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
            <a href="../cart/index.php" class="back-link">Back <span>&rarr;</span></a>
        </div>

        <?php if ($flashError): ?>
            <div style="background-color: #f8d7da; color: #842029; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form id="checkoutForm" action="../../actions/checkout/process.php" method="POST" enctype="multipart/form-data" class="checkout-layout">

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
                    <button type="submit" class="btn-checkout-submit" id="btnCheckoutSubmit">
                        <i class="fa-solid fa-lock"></i> Bayar Sekarang
                    </button>
                </section>

            </div>

            <!-- MODAL 1: CREDIT CARD PAYMENT -->
            <div class="btp-backdrop" id="creditCardModal" aria-hidden="true">
                <div class="btp-modal" role="dialog" aria-modal="true" aria-labelledby="ccModalTitle">
                    <div class="btp-modal-header">
                        <h3 id="ccModalTitle">Credit Card Payment</h3>
                        <button type="button" class="btp-close-btn" data-close-modal="creditCardModal" aria-label="Close">&times;</button>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">1</span>
                            <h4 class="btp-section-title">Select Credit Card</h4>
                        </div>

                        <div class="btp-bank-grid" id="ccBrandList">
                            <label class="btp-bank-card active">
                                <input type="radio" name="card_brand" value="visa" checked>
                                <div class="btp-bank-logo visa">VISA</div>
                                <div class="btp-bank-sub">Visa</div>
                                <div class="btp-bank-desc">Worldwide Acceptance</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="card_brand" value="mastercard">
                                <div class="btp-bank-logo mastercard"><i class="fa-solid fa-circle"></i><i class="fa-solid fa-circle" style="color:#f79e1b;margin-left:-6px;"></i></div>
                                <div class="btp-bank-sub">Mastercard</div>
                                <div class="btp-bank-desc">Global Payment</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="card_brand" value="unionpay">
                                <div class="btp-bank-logo unionpay">UnionPay</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="card_brand" value="jcb">
                                <div class="btp-bank-logo jcb">JCB</div>
                                <div class="btp-bank-sub">JCB</div>
                                <div class="btp-bank-desc">Secure &amp; Reliable</div>
                            </label>
                        </div>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">2</span>
                            <h4 class="btp-section-title">Enter Credit Card Details</h4>
                        </div>

                        <div class="pay-form-grid">
                            <div class="pay-field">
                                <label for="card_number">Card Number</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-regular fa-credit-card"></i>
                                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="cardholder_name">Cardholder Name</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-regular fa-user"></i>
                                    <input type="text" id="cardholder_name" name="cardholder_name" placeholder="Cardholder Name" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="expiry_date">Expiry Date</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input type="text" id="expiry_date" name="expiry_date" placeholder="11/26" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="card_cvv">CVV</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="text" id="card_cvv" name="card_cvv" class="cvv-masked-input" maxlength="4" placeholder="3 digits on the back of your card" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">3</span>
                            <h4 class="btp-section-title">Complete Payment</h4>
                        </div>

                        <div class="complete-pay-row">
                            <div class="complete-pay-shipping">
                                <i class="fa-regular fa-credit-card"></i>
                                <div>
                                    <div style="font-size:1.1rem;font-weight:700;color:#0f172a;">Shipping Method</div>
                                    <div class="modal-shipping-text" style="font-size:0.82rem;color:#334155;">Regular Rp35.000</div>
                                </div>
                            </div>
                            <div class="complete-pay-total">
                                <span>Total Payment</span>
                                <strong class="modal-total-text"><?= formatRupiah($totalAmount) ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="btp-modal-footer">
                        <button type="button" class="btp-btn-pay modal-confirm-pay">Pay Now</button>
                    </div>
                </div>
            </div>

            <!-- MODAL 2: BANK TRANSFER PAYMENT -->
            <div class="btp-backdrop" id="bankTransferModal" aria-hidden="true">
                <div class="btp-modal" role="dialog" aria-modal="true" aria-labelledby="btpModalTitle">
                    <div class="btp-modal-header">
                        <h3 id="btpModalTitle">Bank Transfer Payment</h3>
                        <button type="button" class="btp-close-btn" id="btpCloseBtn" aria-label="Close">&times;</button>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">1</span>
                            <h4 class="btp-section-title">Select Bank Transfer (Virtual Account)</h4>
                        </div>

                        <div class="btp-bank-grid" id="btpBankList">
                            <label class="btp-bank-card active">
                                <input type="radio" name="bank_va" value="mandiri" checked>
                                <div class="btp-bank-logo mandiri">mandiri</div>
                                <div class="btp-bank-sub">Livin VA</div>
                                <div class="btp-bank-desc">Instant Process</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="bank_va" value="bni">
                                <div class="btp-bank-logo bni">BNI</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="bank_va" value="bca">
                                <div class="btp-bank-logo bca">BCA</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Free Admin Fee</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="bank_va" value="bri">
                                <div class="btp-bank-logo bri">BRI</div>
                                <div class="btp-bank-sub">BRIVA (BRI)</div>
                                <div class="btp-bank-desc">BRImo Ready</div>
                            </label>
                        </div>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">2</span>
                            <h4 class="btp-section-title">Upload Payment Proof (Optional)</h4>
                        </div>

                        <label for="btpFileInput" class="pay-dropzone-vertical" id="btpUploadZone">
                            <input type="file" name="payment_proof" id="btpFileInput" accept="image/*,.pdf" class="pay-file-hidden">

                            <div class="pay-dropzone-square-icon" id="btpDropzoneIcon">
                                <i class="fa-solid fa-file-arrow-up"></i>
                            </div>

                            <div class="pay-dropzone-main-text" id="btpFileName">
                                Drag &amp; Drop Transfer Receipt File Here
                            </div>

                            <div class="pay-dropzone-sub-text" id="btpFileSubText">
                                or <span class="pay-browse-link">Browse File from Computer</span>
                            </div>
                        </label>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">3</span>
                            <h4 class="btp-section-title">Quick Payment Instructions</h4>
                        </div>

                        <div class="btp-steps-grid">
                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 1</div>
                                <div class="btp-inst-title">Open M-Banking App</div>
                                <p class="btp-inst-desc">Log in to your banking application on your mobile device.</p>
                            </div>

                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 2</div>
                                <div class="btp-inst-title">Select Transfer Menu</div>
                                <p class="btp-inst-desc">Navigate to the Transfer section and choose Virtual Account.</p>
                            </div>

                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 3</div>
                                <div class="btp-inst-title">Select Bank Provider</div>
                                <p class="btp-inst-desc">Choose the destination bank matching your selected Virtual Account.</p>
                            </div>

                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 4</div>
                                <div class="btp-inst-title">Enter VA Number</div>
                                <p class="btp-inst-desc">Paste or input the Virtual Account number displayed above.</p>
                            </div>

                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 5</div>
                                <div class="btp-inst-title">Check Bill Amount</div>
                                <p class="btp-inst-desc">Verify the order total, merchant name, and payment details.</p>
                            </div>

                            <div class="btp-instruction-card">
                                <div class="btp-inst-step">Step 6</div>
                                <div class="btp-inst-title">Confirm &amp; Enter PIN</div>
                                <p class="btp-inst-desc">Authorize the transaction by entering your PIN to complete payment.</p>
                            </div>
                        </div>
                    </div>

                    <div class="btp-modal-footer">
                        <button type="button" class="btp-btn-pay modal-confirm-pay" id="btpConfirmPayBtn">Pay Now</button>
                    </div>
                </div>
            </div>

            <!-- MODAL 3: E-WALLET PAYMENT -->
            <div class="btp-backdrop" id="ewalletModal" aria-hidden="true">
                <div class="btp-modal" role="dialog" aria-modal="true" aria-labelledby="ewModalTitle">
                    <div class="btp-modal-header">
                        <h3 id="ewModalTitle">E-Wallet Payment</h3>
                        <button type="button" class="btp-close-btn" data-close-modal="ewalletModal" aria-label="Close">&times;</button>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">1</span>
                            <h4 class="btp-section-title">Select E-Wallet</h4>
                        </div>

                        <div class="btp-bank-grid" id="ewProviderList">
                            <label class="btp-bank-card active">
                                <input type="radio" name="ewallet_provider" value="dana" checked>
                                <div class="btp-bank-logo dana">DANA</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="ewallet_provider" value="ovo">
                                <div class="btp-bank-logo ovo">OVO</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="ewallet_provider" value="gopay">
                                <div class="btp-bank-logo gopay">gopay</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>

                            <label class="btp-bank-card">
                                <input type="radio" name="ewallet_provider" value="linkaja">
                                <div class="btp-bank-logo linkaja">LinkAja</div>
                                <div class="btp-bank-sub">Virtual Account</div>
                                <div class="btp-bank-desc">Available 24 Hours</div>
                            </label>
                        </div>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">2</span>
                            <h4 class="btp-section-title">Enter Payment Details</h4>
                        </div>

                        <div class="ewallet-split">
                            <div class="pay-field">
                                <label for="ewallet_phone">Phone Number</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="text" id="ewallet_phone" name="ewallet_phone" placeholder="081234567890">
                                </div>
                                <div class="pay-field-hint">The phone number registered to your e-wallet account.</div>
                            </div>

                            <div class="ewallet-guide-box">
                                <strong>Payment Guide :</strong>
                                Step 1: Make sure you have installed the selected e-wallet application.<br>
                                Step 2: Enter the phone number registered to your e-wallet account.<br>
                                Step 3: Click the Pay Now button and follow the instructions in the app.
                            </div>
                        </div>
                    </div>

                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">3</span>
                            <h4 class="btp-section-title">Complete Payment</h4>
                        </div>

                        <div class="complete-pay-row">
                            <div class="complete-pay-placeholder" id="complete-pay-placeholder-ewallet">
                                <div class="btp-bank-logo-js dana" id="ewalletSummaryLogo">DANA</div>
                                <div class="btp-bank-sub-js">0812-3456-7890</div>
                                <div class="btp-bank-desc-js">Available 24 Hours</div>
                            </div>
                            <div class="complete-pay-shipping">
                                <i class="fa-regular fa-credit-card"></i>
                                <div>
                                    <div style="font-size:1.1rem;font-weight:700;color:#0f172a;line-height:1.2;">Shipping Method</div>
                                    <div class="modal-shipping-text" style="font-size:0.82rem;color:#334155;margin-top:2px;">Regular Rp35.000</div>
                                </div>
                            </div>
                            <div class="complete-pay-total">
                                <span>Total Payment</span>
                                <strong class="modal-total-text"><?= formatRupiah($totalAmount) ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="btp-modal-footer">
                        <button type="button" class="btp-btn-pay modal-confirm-pay">Pay Now</button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>
        const subtotal = <?= $cartSubtotal ?>;
        const shippingRadios = document.querySelectorAll('input[name="shipping_method"]');
        const shippingDisplay = document.getElementById('shipping-display');
        const totalDisplay = document.getElementById('total-display');
        const modalShippingTexts = document.querySelectorAll('.modal-shipping-text');
        const modalTotalTexts = document.querySelectorAll('.modal-total-text');

        function formatRupiahJS(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        function syncPaymentSummary(shippingVal) {
            const isExpress = shippingVal === 'express';
            const shippingCost = isExpress ? 45000 : 35000;
            const total = subtotal + shippingCost;
            shippingDisplay.innerText = formatRupiahJS(shippingCost);
            totalDisplay.innerText = formatRupiahJS(total);
            modalShippingTexts.forEach(modalShippingText => {
                modalShippingText.innerText = (isExpress ? 'Express Rp45.000' : 'Regular Rp35.000');
            });
            modalTotalTexts.forEach(modalTotalText => {
                modalTotalText.innerText = formatRupiahJS(total);
            });
        }

        shippingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                syncPaymentSummary(this.value);
            });
        });

        const checkoutForm = document.getElementById('checkoutForm');
        const btnCheckoutSubmit = document.getElementById('btnCheckoutSubmit');

        // 1. Fungsi Tutup Semua Modal
        function closeAllModals() {
            const modals = document.querySelectorAll('.btp-backdrop');
            modals.forEach(modal => modal.classList.remove('show'));
        }

        // 2. Tutup modal jika klik tombol silang (close) atau klik area gelap (backdrop)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btp-backdrop') || e.target.closest('.btp-close-btn, [data-close-modal]')) {
                closeAllModals();
            }
        });

        // 3. Tombol "Bayar Sekarang" membuka modal sesuai metode yang dipilih
        btnCheckoutSubmit.addEventListener('click', function(e) {
            if (!checkoutForm.checkValidity()) return;

            const payment = document.querySelector('input[name="payment_method"]:checked')?.value;

            const modalMap = {
                card: 'creditCardModal',
                bank_transfer: 'bankTransferModal',
                ewallet: 'ewalletModal'
            };

            const targetModal = document.getElementById(modalMap[payment]);
            if (targetModal) {
                e.preventDefault();
                closeAllModals();
                targetModal.classList.add('show');
            }
        });

        // 4. Tombol "Pay Now" di dalam modal untuk kirim (submit) form
        const modalButtons = document.querySelectorAll('.modal-confirm-pay');
        modalButtons.forEach(btn => {
            btn.addEventListener('click', () => checkoutForm.submit());
        });

        // 5. Efek Aktif pada Kartu Bank / E-Wallet yang Dipilih
        const summaryLogo = document.getElementById("ewalletSummaryLogo");

        document.querySelectorAll('.btp-bank-card').forEach(card => {
            card.addEventListener('click', function() {
                const grid = this.closest('.btp-bank-grid');
                const activeCard = grid ? grid.querySelector('.btp-bank-card.active') : null;
                if (activeCard) activeCard.classList.remove('active');
                this.classList.add('active');

                const logoEl = this.querySelector('.btp-bank-logo');
                if (!logoEl || !summaryLogo) return;

                // Ambil nama channel saja (misal: 'dana', 'ovo', 'gopay', 'linkaja') tanpa ikut membawa 'btp-bank-logo'
                const channelClass = Array.from(logoEl.classList).find(cls => cls !== 'btp-bank-logo') || '';

                // Update ringkasan e-wallet hanya jika kartu yang diklik adalah kartu e-wallet
                if (['dana', 'ovo', 'gopay', 'linkaja'].includes(channelClass)) {
                    summaryLogo.className = 'btp-bank-logo-js ' + channelClass;
                    summaryLogo.textContent = logoEl.textContent.trim();
                }
            });
        });
    </script>

    <script>
        (function() {
            const fileInput = document.getElementById('btpFileInput');
            const dropZone = document.getElementById('btpUploadZone');
            const mainText = document.getElementById('btpFileName');
            const subText = document.getElementById('btpFileSubText');
            const iconBox = document.getElementById('btpDropzoneIcon');

            if (!fileInput || !dropZone || !mainText || !subText) return;

            function updateFileDisplay(files) {
                if (files && files.length > 0) {
                    mainText.textContent = files[0].name;
                    subText.innerHTML = 'File selected &bull; <span class="pay-browse-link">Change File</span>';
                    dropZone.classList.add('has-file');
                    if (iconBox) iconBox.innerHTML = '<i class="fa-solid fa-file-circle-check"></i>';
                } else {
                    mainText.textContent = 'Drag & Drop Transfer Receipt File Here';
                    subText.innerHTML = 'or <span class="pay-browse-link">Browse File from Computer</span>';
                    dropZone.classList.remove('has-file');
                    if (iconBox) iconBox.innerHTML = '<i class="fa-solid fa-file-arrow-up"></i>';
                }
            }

            fileInput.addEventListener('change', function() {
                updateFileDisplay(this.files);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('is-dragover');
                });
            });

            dropZone.addEventListener('drop', function(e) {
                const droppedFiles = e.dataTransfer ? e.dataTransfer.files : null;
                if (droppedFiles && droppedFiles.length > 0) {
                    fileInput.files = droppedFiles;
                    updateFileDisplay(droppedFiles);
                }
            });
        })();
    </script>
</body>

</html>