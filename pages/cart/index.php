<?php
session_start();
require_once __DIR__ . "/../../repositories/product-repository.php";

// unset($_SESSION['cart']);
// exit;

$cart = $_SESSION['cart'] ?? [];

// echo json_encode($cart);
// exit;

$subtotal = 0;
$totalItems = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - My Cart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS kustom -->
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/pages/cart.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php" ?>

    <!-- MAIN CONTENT / CART SECTION -->
    <main class="cart-main-container">
        <!-- Judul Halaman -->
        <div class="cart-header-title">
            <div>
                <h1>My Cart</h1>
                <p>Review your items and proceed to checkout</p>
            </div>
            <a href="../landing/index.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="cart-layout">
            <!-- SISI KIRI: TABEL KERANJANG -->
            <section class="cart-items-section">
                <div class="cart-table-header">
                    <div class="col-check"><input type="checkbox" checked></div>
                    <div class="col-select-all">Select All</div>
                    <div class="col-product">Product</div>
                    <div class="col-price">Price</div>
                    <div class="col-qty">Quantity</div>
                    <div class="col-subtotal">Subtotal</div>
                    <div class="col-action"></div>
                </div>

                <?php if (empty($cart)): ?>
                    <div style="padding: 40px 20px; text-align: center; color: var(--text-muted, #666);">
                        <p style="margin-bottom: 12px; font-size: 16px;">Keranjang belanja Anda masih kosong.</p>
                        <a href="../landing/index.php" style="color: var(--primary-blue, #0d6efd); text-decoration: none; font-weight: 600;">
                            <i class="fa-solid fa-arrow-left"></i> Mulai Belanja
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart as $item): ?>
                        <?php
                        // Ambil ID dari product_id atau id
                        $productId = $item['product_id'] ?? $item['id'] ?? null;
                        if (!$productId) continue;

                        $product = getProduct($productId);

                        // JIKA PRODUK TIDAK DITEMUKAN DI DATABASE, LEWATKAN AGAR TIDAK ERROR
                        if (!$product) {
                            continue;
                        }

                        $quantity = $item['quantity'] ?? 1;
                        $price = $product['price'] ?? 0; // Pastikan ada fallback ke 0
                        $itemSubtotal = $price * $quantity;

                        $subtotal += $itemSubtotal;
                        $totalItems += $quantity;
                        ?>
                        <div class="cart-row">
                            <div class="col-check"><input type="checkbox" checked></div>
                            <div class="col-product">
                                <div class="product-thumb">
                                    <img src="../../assets/img/<?= htmlspecialchars($product['picture'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Produk') ?>">
                                </div>
                                <span class="prod-name"><?= htmlspecialchars($product['name'] ?? 'Produk Tidak Dikenal') ?></span>
                            </div>
                            <div class="col-price"><?= formatRupiah($price) ?></div>
                            <div class="col-qty">
                                <div class="qty-control">
                                    <button type="button">-</button>
                                    <span><?= $quantity ?></span>
                                    <button type="button">+</button>
                                </div>
                            </div>
                            <div class="col-subtotal"><?= formatRupiah($itemSubtotal) ?></div>
                            <div class="col-action">
                                <button class="btn-delete" type="button"><i class="fa-regular fa-trash-can"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Footer Tabel / Remove All -->
                <div class="cart-footer-actions">
                    <button class="btn-remove-all"><i class="fa-regular fa-trash-can"></i> Remove all</button>
                </div>
            </section>

            <!-- SISI KANAN: RINGKASAN & KEUNTUNGAN -->
            <aside class="cart-sidebar">
                <!-- Order Summary Card -->
                <div class="summary-card">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal (<?= $totalItems ?> Items)</span>
                        <strong><?= formatRupiah($subtotal) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <strong>Rp 0</strong>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span class="total-price"><?= formatRupiah($subtotal) ?></span>
                    </div>
                    <button class="btn-checkout" type="button">
                        <i class="fa-solid fa-lock"></i> Proceed to Checkout
                    </button>
                </div>

                <!-- Info Benefits Card 1 -->
                <div class="benefit-card">
                    <i class="fa-solid fa-truck-fast"></i>
                    <div>
                        <strong>Free Shipping</strong>
                        <p>For orders over Rp 500.000</p>
                    </div>
                </div>

                <!-- Info Benefits Card 2 -->
                <div class="benefit-card">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>Official Warranty</strong>
                        <p>100% Original & Guaranteed Products</p>
                    </div>
                </div>

                <!-- Info Benefits Card 3 -->
                <div class="benefit-card">
                    <i class="fa-solid fa-rotate"></i>
                    <div>
                        <strong>Secure Payment</strong>
                        <p>100% Protected Transactions</p>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../components/landing/footer.php" ?>
</body>

</html>