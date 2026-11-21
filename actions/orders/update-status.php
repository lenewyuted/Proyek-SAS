<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/order-repository.php";
requireRole(["admin"]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/order-data/index.php');
    exit;
}

$orderId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$action  = strtolower(trim($_POST['action'] ?? ''));

if ($orderId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    $_SESSION['flash_error'] = 'Permintaan perubahan status tidak valid.';
    header('Location: ../../pages/admin/order-data/index.php');
    exit;
}

$order = getOrderById($orderId);
if (!$order) {
    $_SESSION['flash_error'] = 'Pesanan tidak ditemukan.';
    header('Location: ../../pages/admin/order-data/index.php');
    exit;
}

$currentStatus = strtolower(trim($order['order_status'] ?? 'pending'));
$nextStatus = null;

if ($action === 'reject') {
    if (in_array($currentStatus, ['pending', 'processing', 'shipped'], true)) {
        $nextStatus = 'cancelled';
    }
} elseif ($action === 'approve') {
    $nextStatus = match ($currentStatus) {
        'pending'    => 'processing',
        'processing' => 'shipped',
        'shipped'    => 'completed',
        default      => null,
    };
}

if ($nextStatus === null) {
    $_SESSION['flash_error'] = "Status '{$currentStatus}' tidak dapat diubah dengan aksi '{$action}'.";
    header("Location: ../../pages/admin/order-data/show.php?id={$orderId}");
    exit;
}

if (!function_exists('updateOrderStatus')) {
    $_SESSION['flash_error'] = 'Fungsi updateOrderStatus belum tersedia di order-repository.php.';
    header("Location: ../../pages/admin/order-data/show.php?id={$orderId}");
    exit;
}

if (updateOrderStatus($orderId, $nextStatus)) {
    $_SESSION['flash_success'] = "Status pesanan berhasil diubah menjadi '{$nextStatus}'.";
} else {
    $_SESSION['flash_error'] = 'Gagal memperbarui status pesanan.';
}

header("Location: ../../pages/admin/order-data/show.php?id={$orderId}");
exit;
