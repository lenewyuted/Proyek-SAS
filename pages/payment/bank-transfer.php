<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Bank Transfer Payment</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/payment/bank-transfer.css">
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
                <h1>Bank Transfer Payment</h1>
            </div>
            <a href="checkout.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <form action="process-payment.php" method="POST" enctype="multipart/form-data">
            
            <!-- LANGKAH 1: SELECT BANK -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">1</span>
                    <h2>Select Bank Transfer (Virtual Account)</h2>
                </div>

                <div class="bank-grid">
                    <!-- Bank Mandiri -->
                    <label class="bank-card">
                        <input type="radio" name="bank_choice" value="mandiri" checked>
                        <div class="bank-content">
                            <span class="bank-logo-text mandiri">mandiri</span>
                            <span class="bank-desc">Livin VA<br><small>Instant Process</small></span>
                        </div>
                    </label>

                    <!-- Bank BNI -->
                    <label class="bank-card">
                        <input type="radio" name="bank_choice" value="bni">
                        <div class="bank-content">
                            <span class="bank-logo-text bni">BNI</span>
                            <span class="bank-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>

                    <!-- Bank BCA -->
                    <label class="bank-card">
                        <input type="radio" name="bank_choice" value="bca">
                        <div class="bank-content">
                            <span class="bank-logo-text bca">BCA</span>
                            <span class="bank-desc">Virtual Account<br><small>Free Admin Fee</small></span>
                        </div>
                    </label>

                    <!-- Bank BRI -->
                    <label class="bank-card">
                        <input type="radio" name="bank_choice" value="bri">
                        <div class="bank-content">
                            <span class="bank-logo-text bri">BRi</span>
                            <span class="bank-desc">BRIVA (BRI)<br><small>BRIMO Ready</small></span>
                        </div>
                    </label>
                </div>
            </section>

            <!-- LANGKAH 2: UPLOAD PAYMENT PROOF -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">2</span>
                    <h2>Upload Payment Proof (Optional)</h2>
                </div>

                <div class="upload-dropzone">
                    <input type="file" id="payment_proof" name="payment_proof" class="file-input" hidden>
                    <label for="payment_proof" class="dropzone-label">
                        <div class="upload-icon-box">
                            <i class="fa-regular fa-image"></i>
                        </div>
                        <p><strong>Drag & Drop Transfer Receipt File Here</strong></p>
                        <span class="browse-text">or <span>Browse File from Computer</span></span>
                    </label>
                </div>
            </section>

            <!-- LANGKAH 3: QUICK PAYMENT INSTRUCTIONS -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">3</span>
                    <h2>Quick Payment Instructions</h2>
                </div>

                <div class="instructions-grid">
                    <div class="instruction-box">
                        <strong>Step 1</strong>
                        <h3>Open M-Banking App</h3>
                        <p>Log in to your banking application on your mobile device.</p>
                    </div>

                    <div class="instruction-box">
                        <strong>Step 2</strong>
                        <h3>Select Transfer Menu</h3>
                        <p>Navigate to the Transfer section and choose Virtual Account.</p>
                    </div>

                    <div class="instruction-box">
                        <strong>Step 3</strong>
                        <h3>Select Bank Provider</h3>
                        <p>Choose the destination bank matching your selected Virtual Account.</p>
                    </div>

                    <div class="instruction-box">
                        <strong>Step 4</strong>
                        <h3>Enter VA Number</h3>
                        <p>Paste or input the Virtual Account number displayed above.</p>
                    </div>

                    <div class="instruction-box">
                        <strong>Step 5</strong>
                        <h3>Check Bill Amount</h3>
                        <p>Verify the order total, merchant name, and payment details.</p>
                    </div>

                    <div class="instruction-box">
                        <strong>Step 6</strong>
                        <h3>Confirm & Enter PIN</h3>
                        <p>Authorize the transaction by entering your PIN to complete payment.</p>
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