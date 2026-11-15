<?php
session_start();

// Hanya menerima request POST untuk keamanan aksi hapus
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/cart/index.php');
    exit;
}

// Opsi A: Hapus Semua Barang di Keranjang (Remove All)
if (isset($_POST['remove_all'])) {
    unset($_SESSION['cart']);
    $_SESSION['flash_success'] = 'Semua produk berhasil dihapus dari keranjang.';
    header('Location: ../../pages/cart/index.php');
    exit;
}

// Opsi B: Hapus 1 Produk Spesifik
$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if ($productId && isset($_SESSION['cart'][$productId])) {

    unset($_SESSION['cart'][$productId]);
    $_SESSION['flash_success'] = 'Produk berhasil dihapus dari keranjang.';
}

header('Location: ../../pages/cart/index.php');
exit;
