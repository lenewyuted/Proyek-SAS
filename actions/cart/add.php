<?php
session_start();

$baseUrl = $_SERVER['HTTP_HOST'];


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $baseUrl . '/pages/landing-page/index.php');
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$productId || $productId < 1 || !$quantity || $quantity < 1) {
    $_SESSION['flash_error'] = 'Data produk tidak valid.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $baseUrl . '/index.php'));
    exit;
}


$quantity = min($quantity, 99);

// 1. Inisialisasi session cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// 2. Simpan ke session dengan struktur yang jelas (ada label field):
// Jika produk sudah ada di keranjang, cukup tambahkan quantity-nya
if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] += $quantity;
} else {
    // Jika belum ada, buat item baru dengan detail product_id dan quantity
    $_SESSION['cart'][$productId] = [
        'product_id' => $productId,
        'quantity'   => $quantity
    ];
}

$_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke keranjang.';

// Redirect kembali ke halaman detail produk
header('Location: ../../pages/products/show.php?id=' . $productId);
exit;