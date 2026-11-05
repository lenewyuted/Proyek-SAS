<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang TechHub</title>
    <link rel="stylesheet" href="/styles/pages/keranjang.css">
    <link rel="stylesheet" href="/styles/components/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <img src="/assets/img/Logo.png" alt="">
                </div>
            </a>

            <!-- search bar -->
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search for parts to build your dream PC..." required>
                <button type="submit" class="search-btn">
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

    <main class="cart-page-container">
        <div class="cart-header-title">
            <div>
                <h2>My Cart</h2>
                <p>Review your items and produced to checkout</p>
            </div>
            <a href="index.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="cart-layout">
            <!-- tabel produk keranjang -->
            <div class="cart-items-wrapper">
                <div class="cart-table-header">
                    <div class="col-check"><input type="checkbox" id="selectAll" checked> 
                        <label for="selectAll">Select All</label>
                    </div>
                    <div class="col-prod">Product</div>
                    <div class="col-price">Price</div>
                    <div class="col-qty">Quantity</div>
                    <div class="col-sub">Subtotal</div>
                    <div class="col-action"></div>
                </div>

                <!-- item contoh di keranjang -->
                <div class="cart-item-row">
                    <div class="col-check"><input type="checkbox" checked></div>
                    <div class="col-prod">
                        <div class="cart-img-box"><img src="/assets/img/category/Motherboard (2).png" alt="Product"></div>
                        <span class="prod-name">ASUS ROG Strix B650</span>
                    </div>
                    <div class="col-price">Rp 2.499.000</div>
                    <div class="col-qty">
                        <div class="qty-control">
                            <button type="button" class="minButton">-</button>
                            <span id="qty">1</span>
                            <button type="button" class="plusButton">+</button>
                        </div>
                    </div>
                    <div class="col-sub">Rp 2.499.000</div>
                    <div class="col-action"><button class="delete-btn"><i class="fa-regular fa-trash-can"></i></button></div>
                </div>

                <div class="cart-item-row">
                    <div class="col-check"><input type="checkbox" checked></div>
                    <div class="col-prod">
                        <div class="cart-img-box"><img src="/assets/img/category/Motherboard (2).png" alt="Product"></div>
                        <span class="prod-name">MSI B650 TOMAHAWK</span>
                    </div>
                    <div class="col-price">Rp 4.299.000</div>
                    <div class="col-qty">
                        <div class="qty-control">
                            <button type="button" class="minButton">-</button>
                            <span id="qty">1</span>
                            <button type="button" class="plusButton">+</button>
                        </div>
                    </div>
                    <div class="col-sub">Rp 4.299.000</div>
                    <div class="col-action"><button class="delete-btn"><i class="fa-regular fa-trash-can"></i></button></div>
                </div>

                <div class="cart-item-row">
                    <div class="col-check"><input type="checkbox" checked></div>
                    <div class="col-prod">
                        <div class="cart-img-box"><img src="/assets/img/products/B650.png" alt="Product"></div>
                        <span class="prod-name">GIGABYTE B650 AORUS</span>
                    </div>
                    <div class="col-price">Rp 3.999.000</div>
                    <div class="col-qty">
                        <div class="qty-control">
                            <button type="button" class="minButton">-</button>
                            <span id="qty">1</span>
                            <button type="button" class="plusButton">+</button>
                        </div>
                    </div>
                    <div class="col-sub">Rp 3.999.000</div>
                    <div class="col-action"><button class="delete-btn"><i class="fa-regular fa-trash-can"></i></button></div>
                </div>

                <div class="cart-item-row">
                    <div class="col-check"><input type="checkbox" checked></div>
                    <div class="col-prod">
                        <div class="cart-img-box"><img src="/assets/img/products/AORUS.png" alt="Product"></div>
                        <span class="prod-name">ASRock B650M Pro RS</span>
                    </div>
                    <div class="col-price">Rp 2.400.000</div>
                    <div class="col-qty">
                        <div class="qty-control">
                            <button type="button" class="minButton">-</button>
                            <span id="qty">1</span>
                            <button type="button" class="plusButton">+</button>
                        </div>
                    </div>  
                    <div class="col-sub">Rp 2.400.000</div>
                    <div class="col-action"><button class="delete-btn"><i class="fa-regular fa-trash-can"></i></button></div>
                </div>

                
                <div class="cart-footer-actions">
                    <button class="remove-all-btn"><i class="fa-regular fa-trash-can"></i> Remove all</button>
                </div>
            </div>


            <!-- order summary -->
            <div class="cart-sidebar">
                <div class="order-summary-card">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal (1 Items)</span>
                        <span class="summary-val">Rp 2.499.000</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span class="summary-val">Rp 0</span>
                    </div>
                    <div class="summary-total-row">
                        <span>Total</span>
                        <span class="total-price">Rp 2.499.000</span>
                    </div>
                    <button class="checkout-btn">
                        <i class="fa-solid fa-lock"></i> Proceed to Checkout
                    </button>
                </div>

                <!-- fitur informasi tambahan -->
            <div class="info-card">
                <div class="info-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="info-text">
                    <h4>Free Shipping</h4>
                    <p>For orders over Rp 500.000</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
                <div class="info-text">
                    <h4>Official Warranty</h4>
                    <p>100% Original & Guaranteed Products</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="info-text">
                    <h4>Secure Payment</h4>
                    <p>100% Protected Transactions</p>
                </div>
            </div>
        </div>
    </div>

        <script>
            const minBtn = document.querySelector(".minButton");
            if(minBtn) {
                minBtn.addEventListener("click", function() {
                    // tombol minus quantity di sini kedepannya
                });
            }
        </script>
    </main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // format angka ke format Rupiah
    function formatRupiah(number) {
        return "Rp " + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // menghitung ulang total belanjaan dan jumlah item
    function updateGlobalSummary() {
        let totalSum = 0;
        let totalItemCount = 0;

        document.querySelectorAll(".cart-item-row").forEach(row => {
            const checkbox = row.querySelector(".col-check input[type='checkbox']");
            if (!checkbox || checkbox.checked) {
                const subtotalText = row.querySelector(".col-sub").textContent;
                const subtotalVal = parseInt(subtotalText.replace(/[^0-9]/g, "")) || 0;
                const qtyVal = parseInt(row.querySelector("#qty, .qty-value").textContent) || 0;
                
                totalSum += subtotalVal;
                totalItemCount += qtyVal;
            }
        });

        // update tampilan summary
        const summaryTextElement = document.querySelector(".summary-row span");
        if (summaryTextElement) {
            summaryTextElement.textContent = `Subtotal (${totalItemCount} Items)`;
        }

        const summaryValElements = document.querySelectorAll(".summary-val");
        if (summaryValElements.length > 0) {
            summaryValElements[0].textContent = formatRupiah(totalSum);
        }
        
        const totalPriceElement = document.querySelector(".total-price");
        if (totalPriceElement) {
            totalPriceElement.textContent = formatRupiah(totalSum);
        }

        //checkbox "select all"
        const selectAllCheckbox = document.getElementById("selectAll");
        const allCheckboxes = document.querySelectorAll(".cart-item-row .col-check input[type='checkbox']");
        if (selectAllCheckbox && allCheckboxes.length > 0) {
            const checkedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;
            selectAllCheckbox.checked = checkedCount === allCheckboxes.length;
        }
    }

    function bindRowEvents(row) {
        const minusBtn = row.querySelector(".minButton");
        const plusBtn = row.querySelector(".plusButton");
        const qtySpan = row.querySelector("#qty") || row.querySelector(".qty-value");
        const priceElement = row.querySelector(".col-price");
        const subtotalElement = row.querySelector(".col-sub");
        const checkbox = row.querySelector(".col-check input[type='checkbox']");
        const deleteBtn = row.querySelector(".delete-btn");

        const unitPrice = parseInt(priceElement.textContent.replace(/[^0-9]/g, "")) || 0;

        function updateSubtotal(quantity) {
            const subtotal = unitPrice * quantity;
            subtotalElement.textContent = formatRupiah(subtotal);
            updateGlobalSummary();
        }

        // tombol tambah (+)
        if (plusBtn && qtySpan) {
            plusBtn.addEventListener("click", function () {
                let currentQty = parseInt(qtySpan.textContent) || 1;
                currentQty++;
                qtySpan.textContent = currentQty;
                updateSubtotal(currentQty);
            });
        }

        // tombol kurang (-)
        if (minusBtn && qtySpan) {
            minusBtn.addEventListener("click", function () {
                let currentQty = parseInt(qtySpan.textContent) || 1;
                if (currentQty > 1) {
                    currentQty--;
                    qtySpan.textContent = currentQty;
                    updateSubtotal(currentQty);
                }
            });
        }

        // checkbox per item
        if (checkbox) {
            checkbox.addEventListener("change", updateGlobalSummary);
        }

        // hapus produk per baris
        if (deleteBtn) {
            deleteBtn.addEventListener("click", function () {
                const isConfirmed = confirm("Are you sure you want to delete this item???");
                if (isConfirmed) {
                    row.remove();
                    updateGlobalSummary();
                }
            });
        }
    }

    document.querySelectorAll(".cart-item-row").forEach(bindRowEvents);

    // checkbox "select all"
    const selectAllCheckbox = document.getElementById("selectAll");
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const isChecked = this.checked;
            document.querySelectorAll(".cart-item-row .col-check input[type='checkbox']").forEach(cb => {
                cb.checked = isChecked;
            });
            updateGlobalSummary();
        });
    }

    const removeAllBtn = document.querySelector(".remove-all-btn");
    if (removeAllBtn) {
        removeAllBtn.addEventListener("click", function () {
            const isConfirmed = confirm("Are you sure you want to remove all items from your cart?");
            if (isConfirmed) {
                document.querySelectorAll(".cart-item-row").forEach(row => row.remove());
                updateGlobalSummary();
            }
        });
    }

    updateGlobalSummary();
});
</script>

    <footer class="main-footer">
    <div class="footer-container">
        
        <div class="footer-col brand-info">
            <img src="/assets/img/Logo.png" alt="TechHub Logo" class="footer-logo">
            <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
        </div>

        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="#">Processors</a></li>
                <li><a href="#">Motherboards</a></li>
                <li><a href="#">Graphics Cards</a></li>
                <li><a href="#">RAM &amp; Storage</a></li>
                <li><a href="#">Power Supplies</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Support</h4>
            <ul>
                <li><a href="#">How to Order</a></li>
                <li><a href="#">Shipping</a></li>
                <li><a href="#">Payment Methods</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>My Account</h4>
            <ul>
                <li><a href="#">My Profile</a></li>
                <li><a href="#">Recommendation</a></li>
                <li><a href="#">Compare Products</a></li>
                <li><a href="#">Shopping Cart</a></li>
                <li><a href="#">Search Products</a></li>
            </ul>
        </div>

        <div class="footer-col social-col">
            <h4>Follow Us</h4>
            <div class="social-links">
                <a href="#" class="social-card ig" aria-label="Instagram">
                    <div class="social-icon"><i class="fa-brands fa-instagram"></i></div>
                </a>
                <a href="#" class="social-card fb" aria-label="Facebook">
                    <div class="social-icon"><i class="fa-brands fa-facebook"></i></div>
                </a>
                <a href="#" class="social-card yt" aria-label="YouTube">
                    <div class="social-icon"><i class="fa-brands fa-youtube"></i></div>
                </a>
                <a href="#" class="social-card wa" aria-label="WhatsApp">
                    <div class="social-icon"><i class="fa-brands fa-whatsapp"></i></div>
                </a>
                <a href="#" class="social-card tt" aria-label="TikTok">
                    <div class="social-icon"><i class="fa-brands fa-tiktok"></i></div>
                </a>
                <a href="#" class="social-card xt" aria-label="X">
                    <div class="social-icon"><i class="fa-brands fa-x"></i></div>
                </a>
            </div>
        </div>
</body>
</html>