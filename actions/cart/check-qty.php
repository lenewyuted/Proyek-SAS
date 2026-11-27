<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";

// 1. Pastikan hanya menerima request dengan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid.'
    ]);
    exit;
}

// 2. Ambil data dari request
$productId  = (int) ($_POST['product_id'] ?? 0);
$currentQty = (int) ($_POST['quantity'] ?? 1);
$change     = (int) ($_POST['change'] ?? 0); // Nilai +1 atau -1

// 3. Validasi produk di database
if ($productId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk tidak valid.'
    ]);
    exit;
}

$product = getProduct($productId);
if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Data produk tidak ditemukan di database.'
    ]);
    exit;
}

// 4. Hitung jumlah baru
$newQty = $currentQty + $change;

// 5. Validasi batas minimum (jumlah tidak boleh kurang dari 1)
if ($newQty < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Jumlah minimal adalah 1.'
    ]);
    exit;
}

// 6. Validasi batas maksimum pembelian
if ($newQty > 99) {
    echo json_encode([
        'success' => false,
        'message' => 'Jumlah maksimal pembelian adalah 99.'
    ]);
    exit;
}

// 7. Validasi stok tersedia (termasuk yang sudah ada di keranjang)
$availableStock = (int) ($product['stock'] ?? 0);
$currentInCart  = (int) ($_SESSION['cart'][$productId]['quantity'] ?? 0);

if (($currentInCart + $newQty) > $availableStock) {
    echo json_encode([
        'success' => false,
        'message' => 'Jumlah melebihi stok yang tersedia (Sisa stok: ' . $availableStock . ').'
    ]);
    exit;
}

// 8. Kembalikan response JSON ke frontend
echo json_encode([
    'success'  => true,
    'quantity' => $newQty,
]);
exit;
