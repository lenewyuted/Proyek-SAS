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
    <!-- Bootstrap 4.6 CSS (Dibawa oleh header agar dropdown & carousel berfungsi konsisten) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">


    <style>
        .btp-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999;
        }

        .btp-backdrop.show {
            display: flex;
        }

        .btp-modal {
            width: 100%;
            max-width: 980px;
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 14px;
            padding: 22px 24px 26px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .btp-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .btp-modal-header h3 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            color: #0b132b;
        }

        .btp-close-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            color: #334155;
            padding: 4px 8px;
        }

        .btp-section {
            border: 1.5px solid #0056d2;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 18px;
            background: #fff;
        }

        .btp-section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .btp-step-badge {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #0056d2;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .btp-section-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0b132b;
        }

        .btp-bank-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .btp-bank-card {
            border: 1.5px solid #0056d2;
            border-radius: 10px;
            padding: 14px;
            cursor: pointer;
            margin: 0;
            transition: all 0.15s ease;
            background: #fff;
            display: block;
        }

        .btp-bank-card input[type="radio"] {
            display: none;
        }

        .btp-bank-card.active,
        .btp-bank-card:hover {
            background: #eef4ff;
            box-shadow: 0 0 0 1px #0056d2;
        }

        .btp-bank-logo {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .btp-bank-logo.mandiri {
            color: #003d79;
            font-style: italic;
        }

        .btp-bank-logo.bni {
            color: #e65100;
        }

        .btp-bank-logo.bca {
            color: #0056d2;
            font-style: italic;
        }

        .btp-bank-logo.bri {
            color: #00529c;
        }

        .btp-bank-logo.visa {
            color: #1a1f71;
            font-style: italic;
        }

        .btp-bank-logo.mastercard {
            color: #eb001b;
        }

        .btp-bank-logo.unionpay {
            color: #c8102e;
        }

        .btp-bank-logo.jcb {
            color: #0b4ea2;
        }

        .btp-bank-logo.dana {
            color: #0086e0;
        }

        .btp-bank-logo.ovo {
            color: #4c2a86;
        }

        .btp-bank-logo.gopay {
            color: #00aa13;
        }

        .btp-bank-logo.linkaja {
            color: #e31e24;
        }

        .btp-bank-sub {
            font-size: 0.85rem;
            color: #334155;
            line-height: 1.25;
        }

        .btp-bank-desc {
            font-size: 0.82rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        .btp-dropzone {
            border: 1.5px dashed #334155;
            border-radius: 10px;
            padding: 26px 16px;
            text-align: center;
            background: #fff;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .btp-dropzone.dragover {
            background: #eef4ff;
            border-color: #0056d2;
        }

        .btp-dropzone-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 12px;
            border-radius: 12px;
            background: #e6effc;
        }

        .btp-dropzone-text {
            font-weight: 600;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .btp-dropzone-sub {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 4px;
        }

        .btp-browse-link {
            background: none;
            border: none;
            padding: 0;
            color: #0056d2;
            text-decoration: underline;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .btp-steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .btp-instruction-card {
            background: #e6effc;
            border-radius: 10px;
            padding: 16px;
        }

        .btp-inst-step {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
        }

        .btp-inst-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .btp-inst-desc {
            margin: 0;
            font-size: 0.82rem;
            color: #334155;
            line-height: 1.4;
        }

        /* Credit Card & E-Wallet Modal Fields */
        .pay-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px 18px;
        }

        .pay-field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .pay-input-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1.5px solid #0056d2;
            border-radius: 8px;
            padding: 9px 12px;
            background: #fff;
        }

        .pay-input-wrap i {
            color: #334155;
            font-size: 0.9rem;
        }

        .pay-input-wrap input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            color: #0f172a;
            background: transparent;
        }

        .pay-field-hint {
            font-size: 0.78rem;
            color: #334155;
            margin-top: 6px;
        }

        .ewallet-split {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .ewallet-guide-box {
            background: #e6effc;
            border: 1.5px solid #0056d2;
            border-radius: 10px;
            padding: 16px;
            font-size: 0.82rem;
            color: #0f172a;
            line-height: 1.45;
        }

        .ewallet-guide-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .complete-pay-row {
            display: grid;
            grid-template-columns: 1fr 1.3fr auto;
            gap: 14px;
            align-items: center;
        }

        .complete-pay-placeholder {
            background: #e6effc;
            border-radius: 10px;
            height: 62px;
        }

        .complete-pay-shipping {
            background: #e6effc;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 62px;
        }

        .complete-pay-shipping i {
            font-size: 1.25rem;
            color: #0056d2;
        }

        .complete-pay-total {
            border: 1.5px solid #0056d2;
            border-radius: 10px;
            padding: 10px 18px;
            text-align: center;
            background: #fff;
        }

        .complete-pay-total span {
            display: block;
            font-size: 0.78rem;
            color: #0056d2;
            font-weight: 600;
        }

        .complete-pay-total strong {
            font-size: 1rem;
            color: #0b132b;
            font-weight: 800;
        }

        .btp-modal-footer {
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }

        .btp-btn-pay {
            background: #0052cc;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 10px 36px;
            min-width: 180px;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btp-btn-pay:hover {
            background: #003fa3;
        }

        @media (max-width: 900px) {
            .btp-bank-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .btp-steps-grid,
            .pay-form-grid,
            .ewallet-split,
            .complete-pay-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {

            .btp-bank-grid,
            .btp-steps-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="cardholder_name">Cardholder Name</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-regular fa-user"></i>
                                    <input type="text" id="cardholder_name" name="cardholder_name" placeholder="Cardholder Name">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="expiry_date">Expiry Date</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input type="text" id="expiry_date" name="expiry_date" placeholder="11/26">
                                </div>
                            </div>

                            <div class="pay-field">
                                <label for="card_cvv">CVV</label>
                                <div class="pay-input-wrap">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" id="card_cvv" name="card_cvv" placeholder="3 digits on the back of your card">
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
                            <div class="complete-pay-placeholder"></div>
                            <div class="complete-pay-shipping">
                                <i class="fa-regular fa-credit-card"></i>
                                <div>
                                    <div style="font-size:0.8rem;font-weight:700;color:#0f172a;">Shipping Method</div>
                                    <div class="modal-shipping-text" style="font-size:0.8rem;color:#334155;">Regular Rp35.000</div>
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

                        <div class="btp-dropzone" id="btpDropzone">
                            <input type="file" name="payment_proof" id="btpFileInput" accept="image/*,.pdf" hidden>
                            <div class="btp-dropzone-icon"></div>
                            <div class="btp-dropzone-text" id="btpDropzoneText">Drag &amp; Drop Transfer Receipt File Here</div>
                            <div class="btp-dropzone-sub">
                                or <button type="button" class="btp-browse-link" id="btpBrowseBtn">Browse File from Computer</button>
                            </div>
                        </div>
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
                            <div class="complete-pay-placeholder"></div>
                            <div class="complete-pay-shipping">
                                <i class="fa-regular fa-credit-card"></i>
                                <div>
                                    <div style="font-size:0.8rem;font-weight:700;color:#0f172a;">Shipping Method</div>
                                    <div class="modal-shipping-text" style="font-size:0.8rem;color:#334155;">Regular Rp35.000</div>
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
            modalShippingTexts.forEach(el => {
                el.innerText = (isExpress ? 'Express Rp45.000' : 'Regular Rp35.000');
            });
            modalTotalTexts.forEach(el => {
                el.innerText = formatRupiahJS(total);
            });
        }

        shippingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                syncPaymentSummary(this.value);
            });
        });

        const checkoutForm = document.getElementById('checkoutForm');
        const btnCheckoutSubmit = document.getElementById('btnCheckoutSubmit');

        const modals = {
            card: document.getElementById('creditCardModal'),
            bank_transfer: document.getElementById('bankTransferModal'),
            ewallet: document.getElementById('ewalletModal')
        };

        function closeAllModals() {
            Object.values(modals).forEach(m => {
                if (!m) return;
                m.classList.remove('show');
                m.setAttribute('aria-hidden', 'true');
            });
        }

        // Toggle kartu pilihan pada tiap grid (bank_transfer, card, ewallet)
        ['#btpBankList', '#ccBrandList', '#ewProviderList'].forEach(selector => {
            const cards = document.querySelectorAll(selector + ' .btp-bank-card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    cards.forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    const r = card.querySelector('input[type="radio"]');
                    if (r) r.checked = true;
                });
            });
        });

        // Dropzone bukti transfer (dipertahankan)
        const dropzone = document.getElementById('btpDropzone');
        const fileInput = document.getElementById('btpFileInput');
        const browseBtn = document.getElementById('btpBrowseBtn');
        const dropzoneText = document.getElementById('btpDropzoneText');

        browseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                dropzoneText.textContent = 'Selected: ' + fileInput.files[0].name;
            }
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                dropzoneText.textContent = 'Selected: ' + e.dataTransfer.files[0].name;
            }
        });

        // Tutup modal lewat tombol close atau klik backdrop
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', closeAllModals);
        });
        document.getElementById('btpCloseBtn').addEventListener('click', closeAllModals);

        Object.values(modals).forEach(m => {
            m.addEventListener('click', (e) => {
                if (e.target === m) closeAllModals();
            });
        });

        // Tombol Bayar Sekarang membuka modal sesuai payment_method terpilih
        btnCheckoutSubmit.addEventListener('click', function(e) {
            if (!checkoutForm.checkValidity()) {
                return;
            }

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value;
            const targetModal = modals[selectedPayment];

            if (targetModal) {
                e.preventDefault();
                closeAllModals();
                targetModal.classList.add('show');
                targetModal.setAttribute('aria-hidden', 'false');
            }
        });

        // Tombol Pay Now di dalam modal menutup modal dan submit form
        document.querySelectorAll('.modal-confirm-pay').forEach(btn => {
            btn.addEventListener('click', () => {
                closeAllModals();
                checkoutForm.submit();
            });
        });
    </script>
</body>

</html>