<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/cart-repository.php";
require_once __DIR__ . "/../../repositories/order-repository.php";
require_once __DIR__ . "/../../validations/order-data-validation.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/checkout/index.php');
    exit;
}

// 1. Validasi Autentikasi User
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk checkout.';
    header('Location: ../../pages/auth/login.php');
    exit;
}

$userId    = (int) $_SESSION['user']['id'];
$cartItems = $_SESSION['cart'] ?? [];

// 2. Jalankan Validasi Stok & Keranjang
$cartValidation = validateCartAndStock($cartItems);
if (!empty($cartValidation['errors'])) {
    $_SESSION['flash_error'] = $cartValidation['errors'][0];
    header('Location: ../../pages/cart/index.php');
    exit;
}

// 3. Jalankan Validasi Form Input & File Upload
$formValidation = validateCheckoutData($_POST, $_FILES);
if (!empty($formValidation['errors'])) {
    $_SESSION['flash_error'] = $formValidation['errors'][0];
    header('Location: ../../pages/checkout/index.php');
    exit;
}

// Ambil data bersih hasil validasi
$cleanData          = $formValidation['data'];
$orderItems         = $cartValidation['items'];
$totalProductAmount = $cartValidation['total_amount'];

// 4. Upload Bukti Pembayaran (Aman dijalankan di sini karena semua validasi sudah lolos)
$paymentProof = null;
if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
    $ext       = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
    $safeName  = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $uploadDir = __DIR__ . '/../../assets/img/payments/';

    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadDir . $safeName)) {
        $paymentProof = $safeName;
    }
}

// 5. Hitung Ongkir & Grand Total
$shippingCost = ($cleanData['shipping_method'] === 'express') ? 45000 : 35000;
$grandTotal   = $totalProductAmount + $shippingCost;
$orderNumber  = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

$orderData = [
    'user_id'         => $userId,
    'order_number'    => $orderNumber,
    'full_name'       => $cleanData['full_name'],
    'phone_number'    => $cleanData['phone_number'],
    'address'         => $cleanData['address'],
    'city'            => $cleanData['city'],
    'province'        => $cleanData['province'],
    'postal_code'     => $cleanData['postal_code'],
    'shipping_method' => $cleanData['shipping_method'],
    'shipping_cost'   => $shippingCost,
    'total_amount'    => $grandTotal,
];

$paymentData = [
    'payment_method'  => $cleanData['payment_method'],
    'payment_channel' => $cleanData['payment_channel'],
    'account_number'  => $cleanData['account_number'],
    'account_holder'  => $cleanData['account_holder'],
    'expiry_date'     => $cleanData['expiry_date'],
    'cvv'             => $cleanData['cvv'],
    'payment_proof'   => $paymentProof,
];

try {
    // 6. Simpan Transaksi ke Database
    createOrder($orderData, $orderItems, $paymentData);

    // 7. Bersihkan Keranjang
    unset($_SESSION['cart']);
    clearUserCartFromDb($userId);

    // 8. Perbarui Profil Alamat User
    $updateUserQuery = "UPDATE users SET 
                        phone_number = :phone, 
                        address = :address, 
                        city = :city, 
                        province = :province, 
                        postal_code = :postal_code 
                        WHERE id = :id";

    $updateUserStmt = $pdo->prepare($updateUserQuery);
    $updateUserStmt->execute([
        'phone'       => $cleanData['phone_number'],
        'address'     => $cleanData['address'],
        'city'        => $cleanData['city'],
        'province'    => $cleanData['province'],
        'postal_code' => $cleanData['postal_code'],
        'id'          => $userId,
    ]);

    // 9. Redirect ke Halaman Sukses
    header("Location: ../../pages/checkout/success.php?order=" . urlencode($orderNumber));
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem saat memproses pesanan: ' . $e->getMessage();
    header('Location: ../../pages/checkout/index.php');
    exit;
}
