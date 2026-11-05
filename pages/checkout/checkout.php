<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/styles/components/header.css">
    <link rel="stylesheet" href="/styles/pages/checkout.css">
</head>
<body>

    <!-- bar bagian atas -->
    <div class="top-bar">
       <span>Discover Premium PC Components Built for Performance, Speed, and Reliability</span>
    </div>

<!-- main navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <img src="/assets/img/Logo.png" alt="TechHub Logo">
            </div>
        </a>

        <!-- search bar -->
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search for parts to build your dream PC..." required>
            <button type="submit" class="search-btn" title="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <!-- tombol aksi kanan -->
        <div class="nav-icons">
            <a href="thumbs-up.php" class="icon-link" title="Likes">
                <i class="fa-solid fa-thumbs-up"></i>
            </a>

            <a href="compare.php" class="icon-link" title="Compare">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>

            <a href="cart.php" class="icon-link" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </a>

            <a href="account.php" class="icon-link" title="User Account">
                <i class="fa-regular fa-user"></i>
            </a>
        </div>
        
    </div>
</nav>

    <main>
        <div class="page-header">
            <div class="page-title-group">
                <h1>Checkout</h1>
                <p>Complete your details to finish checkout</p>
            </div>
            <!-- Tombol Back berfungsi kembali ke halaman sebelumnya / Cart -->
            <a href="cart.php" onclick="if(history.length > 1) { history.back(); return false; }" class="back-link">
                <span>Back</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <form id="checkoutForm" onsubmit="handleCheckout(event)" class="checkout-grid">
            <div>
                <div class="card">
                    <h2 class="card-title">Shipping Address</h2>
                    
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>

                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <div class="form-group">
                        <label for="province">Province</label>
                        <input type="text" id="province" name="province" required>
                    </div>

                    <div class="form-group">
                        <label for="postalCode">Postal Code</label>
                        <input type="text" id="postalCode" name="postalCode" required>
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2 class="card-title">Shipping Method</h2>

                    <label class="option-item selected">
                        <div class="option-left">
                            <input type="radio" name="shipping" value="regular" checked style="display:none;" onchange="updateOptionSelection(this)">
                            <div class="custom-checkbox"><i class="fa-solid fa-check"></i></div>
                            <span class="option-label">Regular (2–5 days)</span>
                        </div>
                        <span class="option-price">Rp 35.000</span>
                    </label>

                    <label class="option-item">
                        <div class="option-left">
                            <input type="radio" name="shipping" value="express" style="display:none;" onchange="updateOptionSelection(this)">
                            <div class="custom-checkbox"></div>
                            <span class="option-label">Express (1–2 days)</span>
                        </div>
                        <span class="option-price">Rp 45.000</span>
                    </label>
                </div>

                <div class="card">
                    <h2 class="card-title">Payment Method</h2>

                    <label class="option-item">
                        <div class="option-left">
                            <input type="radio" name="payment" value="card" required style="display:none;" onchange="updateOptionSelection(this)">
                            <div class="custom-checkbox"></div>
                            <span class="option-label">Credit / Debit Card</span>
                        </div>
                    </label>

                    <label class="option-item">
                        <div class="option-left">
                            <input type="radio" name="payment" value="bank" required style="display:none;" onchange="updateOptionSelection(this)">
                            <div class="custom-checkbox"></div>
                            <span class="option-label">Bank Transfer (BCA, BNI, BRI, Mandiri)</span>
                        </div>
                    </label>

                    <label class="option-item">
                        <div class="option-left">
                            <input type="radio" name="payment" value="ewallet" required style="display:none;" onchange="updateOptionSelection(this)">
                            <div class="custom-checkbox"></div>
                            <span class="option-label">E–Wallet (OVO, GoPay, DANA, LinkAja)</span>
                        </div>
                    </label>
                </div>

                <button type="submit" class="checkout-btn">Checkout</button>
            </div>
        </form>
    </main>

    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fa-solid fa-desktop"></i>
                    </div>
                    <span>TECHHUB</span>
                </a>
                <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
            </div>

            <div class="footer-col">
                <h3>Shop</h3>
                <ul>
                    <li><a href="search.php?category=processors">Processors</a></li>
                    <li><a href="search.php?category=motherboards">Motherboards</a></li>
                    <li><a href="search.php?category=gpu">Graphics Cards</a></li>
                    <li><a href="search.php?category=ram_storage">RAM & Storage</a></li>
                    <li><a href="search.php?category=psu">Power Supplies</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Support</h3>
                <ul>
                    <li><a href="faq.php">How to Order</a></li>
                    <li><a href="shipping.php">Shipping</a></li>
                    <li><a href="payment.php">Payment Methods</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>My Account</h3>
                <ul>
                    <li><a href="account.php">My Profile</a></li>
                    <li><a href="account.php?tab=recommendation">Recommendation</a></li>
                    <li><a href="compare.php">Compare Products</a></li>
                    <li><a href="cart.php">Shopping Cart</a></li>
                    <li><a href="search.php">Search Products</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Follow Us</h3>
                <div class="social-grid">
                    <a href="https://instagram.com" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://facebook.com" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://youtube.com" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
                    <a href="https://wa.me/" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="https://tiktok.com" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://x.com" target="_blank" rel="noopener" class="social-btn"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>

        <div class="copyright">
            © 2026 TechHub. All rights reserved.
        </div>
    </footer>

    <script>
        // Fungsi untuk memperbarui tampilan radio button kustom (Shipping & Payment)
        function updateOptionSelection(input) {
            const name = input.name;
            const groupInputs = document.querySelectorAll(`input[name="${name}"]`);
            
            groupInputs.forEach(item => {
                const label = item.closest('.option-item');
                const checkbox = label.querySelector('.custom-checkbox');
                
                if (item.checked) {
                    label.classList.add('selected');
                    checkbox.innerHTML = '<i class="fa-solid fa-check"></i>';
                } else {
                    label.classList.remove('selected');
                    checkbox.innerHTML = '';
                }
            });
        }

        // Handler saat form submit / tombol Checkout diklik
        function handleCheckout(e) {
            e.preventDefault();

            const form = document.getElementById('checkoutForm');
            const selectedPayment = form.querySelector('input[name="payment"]:checked');

            if (!selectedPayment) {
                alert('Silakan pilih Metode Pembayaran (Payment Method) terlebih dahulu.');
                return;
            }

            alert('Checkout berhasil dikonfirmasi!');
            // form.submit(); // Buka komentar ini jika terhubung ke backend server
        }

        // Inisialisasi tampilan awal saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const checkedInputs = document.querySelectorAll('input[type="radio"]:checked');
            checkedInputs.forEach(input => updateOptionSelection(input));
        });
    </script>
</body>
</html>