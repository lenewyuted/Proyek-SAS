<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/cart-repository.php";

// Pastikan hanya menerima request dengan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid.'
    ]);
    exit;
}

$productId = (int) ($_POST['product_id'] ?? 0);
$change    = (int) ($_POST['change'] ?? 0); // Nilai +1 atau -1

// 1. Validasi keberadaan produk di dalam keranjang session
if ($productId <= 0 || !isset($_SESSION['cart'][$productId])) {
    echo json_encode([
        'success' => false,
        'message' => 'Produk tidak ditemukan di keranjang.'
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

$currentQuantity = (int) $_SESSION['cart'][$productId]['quantity'];
$newQuantity     = $currentQuantity + $change;

// 2. Validasi batas minimum (jumlah tidak boleh kurang dari 1)
if ($newQuantity < 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Jumlah minimal adalah 1. Gunakan tombol hapus (ikon tong sampah) jika ingin membuang produk dari keranjang.'
    ]);
    exit;
}

// 3. Validasi batas stok maksimum dari database
$availableStock = (int) ($product['stock'] ?? 0);
if ($newQuantity > $availableStock) {
    echo json_encode([
        'success' => false,
        'message' => 'Jumlah melebihi stok yang tersedia (Sisa stok: ' . $availableStock . ').'
    ]);
    exit;
}

// 4. Perbarui jumlah produk pada Session
$_SESSION['cart'][$productId]['quantity'] = $newQuantity;

// 5. Jika user sedang login, sinkronkan perubahan ke Database
$userId = (int) ($_SESSION['user']['id'] ?? 0);
if ($userId > 0) {
    updateItemQuantityInDbCart($userId, $productId, $newQuantity);
}

// 6. Hitung ulang total item dan total belanja seluruh keranjang
$totalSubtotal = 0;
$totalItems    = 0;

foreach ($_SESSION['cart'] as $item) {
    $itemProductId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
    $itemProduct   = getProduct($itemProductId);

    if ($itemProduct) {
        $qty = (int) ($item['quantity'] ?? 1);
        $totalSubtotal += ($itemProduct['price'] ?? 0) * $qty;
        $totalItems    += $qty;
    }
}

// Subtotal untuk produk yang baru saja diubah
$itemSubtotal = ($product['price'] ?? 0) * $newQuantity;

// 7. Kembalikan response JSON yang lengkap ke frontend
echo json_encode([
    'success'                  => true,
    'item_quantity'            => $newQuantity,
    'item_subtotal_formatted'  => formatRupiah($itemSubtotal),
    'total_items'              => $totalItems,
    'total_subtotal_formatted' => formatRupiah($totalSubtotal),
]);
exit;
