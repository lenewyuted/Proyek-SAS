<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/review-repository.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/landing/index.php');
    exit;
}

$productId   = (int) ($_POST['product_id'] ?? 0);
$reviewId    = (int) ($_POST['review_id'] ?? 0);
$redirectUrl = "../../pages/products/show.php?id=" . $productId . "#reviews-section";

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
    header("Location: $redirectUrl");
    exit;
}

if ($productId <= 0 || $reviewId <= 0) {
    $_SESSION['flash_error'] = 'Data ulasan tidak valid.';
    header("Location: $redirectUrl");
    exit;
}

$userId  = (int) $_SESSION['user']['id'];
$isAdmin = (($_SESSION['user']['role'] ?? '') === 'admin');

try {
    $deleted = deleteProductReview($reviewId, $userId, $isAdmin);

    if ($deleted) {
        $_SESSION['flash_success'] = 'Ulasan berhasil dihapus.';
    } else {
        $_SESSION['flash_error'] = 'Ulasan tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya.';
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Gagal menghapus ulasan: ' . $e->getMessage();
}

header("Location: $redirectUrl");
exit;
