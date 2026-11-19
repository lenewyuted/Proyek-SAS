<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/cart-repository.php";
require_once __DIR__ . "/../../repositories/order-repository.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/checkout/index.php');
    exit;
}

// 1. Validasi Autentikasi User (Wajib Login)
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk checkout.';
    header('Location: ../../pages/auth/login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];
$cartItems   = $_SESSION['cart'] ?? [];

// 2. Validasi Keranjang Belanja
if (empty($cartItems)) {
    $_SESSION['flash_error'] = 'Keranjang belanja Anda masih kosong.';
    header('Location: ../../pages/cart/index.php');
    exit;
}

// 3. Validasi Form Input Alamat
$fullName       = trim($_POST['full_name'] ?? '');
$phoneNumber    = trim($_POST['phone_number'] ?? '');
$address        = trim($_POST['address'] ?? '');
$city           = trim($_POST['city'] ?? '');
$province       = trim($_POST['province'] ?? '');
$postalCode     = trim($_POST['postal_code'] ?? '');
$shippingMethod = trim($_POST['shipping_method'] ?? 'regular');
$paymentMethod  = trim($_POST['payment_method'] ?? 'card');

if (empty($fullName) || empty($phoneNumber) || empty($address) || empty($city) || empty($province) || empty($postalCode)) {
    $_SESSION['flash_error'] = 'Harap lengkapi semua data alamat pengiriman.';
    header('Location: ../../pages/checkout/index.php');
    exit;
}

// 4. Hitung Tarif Ongkir Resmi di Server
$shippingCost = ($shippingMethod === 'express') ? 45000 : 35000;

// 5. Validasi Stok & Hitung Subtotal Asli dari Database
$totalProductAmount = 0;
$orderItems = [];

foreach ($cartItems as $cartItem) {
    $pId = (int) $cartItem['product_id'];
    $qty = (int) $cartItem['quantity'];

    $product = getProduct($pId);
    if (!$product) {
        $_SESSION['flash_error'] = "Produk dengan ID $pId tidak ditemukan.";
        header('Location: ../../pages/cart/index.php');
        exit;
    }

    if ($product['stock'] < $qty) {
        $_SESSION['flash_error'] = "Stok untuk produk {$product['name']} tidak mencukupi (Sisa stok: {$product['stock']}).";
        header('Location: ../../pages/cart/index.php');
        exit;
    }

    $subtotal = $product['price'] * $qty;
    $totalProductAmount += $subtotal;

    $orderItems[] = [
        'product_id' => $pId,
        'quantity'   => $qty,
        'price'      => (int) $product['price'],
        'subtotal'   => $subtotal,
    ];
}

$grandTotal = $totalProductAmount + $shippingCost;

// 6. Buat Kode Pesanan Unik (Format: ORD-YYYYMMDD-XXXXX)
$orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

$orderData = [
    'user_id'         => $userId,
    'order_number'    => $orderNumber,
    'full_name'       => $fullName,
    'phone_number'    => $phoneNumber,
    'address'         => $address,
    'city'            => $city,
    'province'        => $province,
    'postal_code'     => $postalCode,
    'shipping_method' => $shippingMethod,
    'shipping_cost'   => $shippingCost,
    'total_amount'    => $grandTotal,
];

$paymentData = [
    'payment_method' => $paymentMethod,
];

try {
    // 7. Simpan Transaksi Lengkap ke Database (orders, order_items, payments, kurangi stok)
    createOrder($orderData, $orderItems, $paymentData);

    // 8. Bersihkan Keranjang di Session & Database (Hybrid Cart Cleanup)
    unset($_SESSION['cart']);
    clearUserCartFromDb($userId);

    // 9. Simpan / Perbarui data alamat ke profil user agar otomatis terisi di checkout berikutnya
    $updateUserQuery = "UPDATE users SET 
                        phone_number = :phone, 
                        address = :address, 
                        city = :city, 
                        province = :province, 
                        postal_code = :postal_code 
                        WHERE id = :id";
                        
    $updateUserStmt = $pdo->prepare($updateUserQuery);
    $updateUserStmt->execute([
        'phone'       => $phoneNumber,
        'address'     => $address,
        'city'        => $city,
        'province'    => $province,
        'postal_code' => $postalCode,
        'id'          => $userId,
    ]);

    // 10. Redirect ke Halaman Sukses Transaksi
    header("Location: ../../pages/checkout/success.php?order=" . urlencode($orderNumber));
    exit;

} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem saat memproses pesanan: ' . $e->getMessage();
    header('Location: ../../pages/checkout/index.php');
    exit;
}
