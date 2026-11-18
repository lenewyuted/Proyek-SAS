<?php
session_start();
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/cart-repository.php";

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

$product = getProduct($productId);
if (!$product) {
    $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
    header('Location: ../../pages/landing-page/index.php');
    exit;
}

$currentInCart = $_SESSION['cart'][$productId]['quantity'] ?? 0;
if (($currentInCart + $quantity) > $product['stock']) {
    $_SESSION['flash_error'] = 'Jumlah melebihi stok yang tersedia (Sisa stok: ' . $product['stock'] . ').';
    header('Location: ../../pages/products/show.php?id=' . $productId);
    exit;
}

// 1. Inisialisasi session cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 2. Simpan ke session (Hybrid Cart: Session Layer)
if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$productId] = [
        'product_id' => $productId,
        'quantity'   => $quantity
    ];
}

// 3. JIKA user sudah login, sinkronkan juga ke database (Hybrid Cart: DB Layer)
if (isset($_SESSION['user']['id'])) {
    syncItemToDbCart($_SESSION['user']['id'], $productId, $quantity);
}

$_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke keranjang.';

// Redirect kembali ke halaman detail produk
header('Location: ../../pages/products/show.php?id=' . $productId);
exit;