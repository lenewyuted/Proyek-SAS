<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/order-repository.php";
require_once __DIR__ . "/../../validations/order-data-validation.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/order-history/index.php');
    exit;
}

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
    header('Location: ../../pages/auth/login.php');
    exit;
}

$userId  = (int) $_SESSION['user']['id'];
$orderId = (int) ($_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    $_SESSION['flash_error'] = 'ID pesanan tidak valid.';
    header('Location: ../../pages/order-history/index.php');
    exit;
}

$order = getOrderById($orderId);
$validationError = validateCancelOrder($order, $userId);

if ($validationError !== null) {
    $_SESSION['flash_error'] = $validationError;
    header('Location: ../../pages/order-history/index.php');
    exit;
}

try {
    $isCancelled = cancelOrder($orderId, $userId);

    if ($isCancelled) {
        $_SESSION['flash_success'] = "Pesanan {$order['order_number']} berhasil dibatalkan.";
    } else {
        $_SESSION['flash_error'] = 'Gagal membatalkan pesanan karena statusnya sudah berubah.';
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
}

header('Location: ../../pages/cart/order-history.php');
exit;
