<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - E-Wallet Payment</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/payment/ewallet.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <!-- MAIN CONTENT / PAYMENT SECTION -->
    <main class="payment-main-container">
        <!-- Judul Halaman -->
        <div class="payment-header-title">
            <div>
                <h1>E-Wallet Payment</h1>
            </div>
            <a href="checkout.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <form action="process-ewallet-payment.php" method="POST">
            
            <!-- LANGKAH 1: SELECT E-WALLET -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">1</span>
                    <h2>Select E-Wallet</h2>
                </div>

                <div class="card-provider-grid">
                    <!-- DANA -->
                    <label class="provider-card">
                        <input type="radio" name="ewallet_provider" value="dana" checked>
                        <div class="provider-content">
                            <span class="brand-text dana">DANA</span>
                            <span class="provider-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>

                    <!-- OVO -->
                    <label class="provider-card">
                        <input type="radio" name="ewallet_provider" value="ovo">
                        <div class="provider-content">
                            <span class="brand-text ovo">OVO</span>
                            <span class="provider-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>

                    <!-- Gopay -->
                    <label class="provider-card">
                        <input type="radio" name="ewallet_provider" value="gopay">
                        <div class="provider-content">
                            <span class="brand-text gopay">gopay</span>
                            <span class="provider-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>

                    <!-- LinkAja -->
                    <label class="provider-card">
                        <input type="radio" name="ewallet_provider" value="linkaja">
                        <div class="provider-content">
                            <span class="brand-text linkaja"><span class="linkaja-badge">L</span>LinkAja</span>
                            <span class="provider-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>
                </div>
            </section>

            <!-- LANGKAH 2: ENTER PAYMENT DETAILS -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">2</span>
                    <h2>Enter Payment Details</h2>
                </div>

                <div class="ewallet-details-grid">
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="phone_number" name="phone_number" placeholder="081234567890" required>
                        </div>
                        <span class="form-helper-text">The phone number registered to your e-wallet account.</span>
                    </div>

                    <div class="payment-guide-box">
                        <strong>Payment Guide :</strong>
                        <p>Step 1: Make sure you have installed the selected e-wallet application.</p>
                        <p>Step 2: Enter the phone number registered to your e-wallet account.</p>
                        <p>Step 3: Click the Pay Now button and follow the instructions in the app.</p>
                    </div>
                </div>
            </section>

            <!-- LANGKAH 3: COMPLETE PAYMENT -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">3</span>
                    <h2>Complete Payment</h2>
                </div>

                <div class="complete-payment-grid">
                    <div class="empty-placeholder-box"></div>
                    
                    <div class="summary-shipping-box">
                        <div class="shipping-icon-title">
                            <i class="fa-regular fa-credit-card"></i>
                            <div>
                                <strong>Shipping Method</strong>
                                <p>Express Rp45.000</p>
                            </div>
                        </div>
                    </div>

                    <div class="total-payment-box">
                        <span class="total-title">Total Payment</span>
                        <strong class="total-amount">Rp 13.397.000</strong>
                    </div>
                </div>
            </section>

            <!-- TOMBOL PAY NOW -->
            <button type="submit" class="btn-pay-now">
                Pay Now
            </button>

        </form>
    </main>

    <!-- MAIN FOOTER -->
    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

</body>

</html>