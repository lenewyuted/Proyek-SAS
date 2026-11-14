<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Credit Card Payment</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/payment/credit-card.css">
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
                <h1>Credit Card Payment</h1>
            </div>
            <a href="checkout.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <form action="process-credit-payment.php" method="POST">
            
            <!-- LANGKAH 1: SELECT CREDIT CARD -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">1</span>
                    <h2>Select Credit Card</h2>
                </div>

                <div class="card-provider-grid">
                    <!-- Visa -->
                    <label class="provider-card">
                        <input type="radio" name="card_provider" value="visa" checked>
                        <div class="provider-content">
                            <span class="brand-text visa">VISA</span>
                            <span class="provider-desc">Visa<br><small>Worldwide Acceptance</small></span>
                        </div>
                    </label>

                    <!-- Mastercard -->
                    <label class="provider-card">
                        <input type="radio" name="card_provider" value="mastercard">
                        <div class="provider-content">
                            <div class="mastercard-circles">
                                <span class="circle red"></span>
                                <span class="circle orange"></span>
                            </div>
                            <span class="provider-desc">Mastercard<br><small>Global Payment</small></span>
                        </div>
                    </label>

                    <!-- UnionPay -->
                    <label class="provider-card">
                        <input type="radio" name="card_provider" value="unionpay">
                        <div class="provider-content">
                            <span class="brand-text unionpay">UnionPay</span>
                            <span class="provider-desc">Virtual Account<br><small>Available 24 Hours</small></span>
                        </div>
                    </label>

                    <!-- JCB -->
                    <label class="provider-card">
                        <input type="radio" name="card_provider" value="jcb">
                        <div class="provider-content">
                            <div class="jcb-logo">
                                <span class="jcb-j">J</span><span class="jcb-c">C</span><span class="jcb-b">B</span>
                            </div>
                            <span class="provider-desc">JCB<br><small>Secure & Reliable</small></span>
                        </div>
                    </label>
                </div>
            </section>

            <!-- LANGKAH 2: ENTER CREDIT CARD DETAILS -->
            <section class="payment-card">
                <div class="step-title">
                    <span class="step-number">2</span>
                    <h2>Enter Credit Card Details</h2>
                </div>

                <div class="card-details-grid">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <div class="input-with-icon">
                            <i class="fa-regular fa-credit-card"></i>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cardholder_name">Cardholder Name</label>
                        <div class="input-with-icon">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="cardholder_name" name="cardholder_name" value="Lenpit QWERTY" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <div class="input-with-icon">
                            <i class="fa-regular fa-calendar"></i>
                            <input type="text" id="expiry_date" name="expiry_date" placeholder="11/26" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="cvv" name="cvv" placeholder="3 digits on the back of your card" required>
                            <i class="fa-regular fa-eye icon-right"></i>
                        </div>
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