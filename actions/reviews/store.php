<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/review-repository.php";
require_once __DIR__ . "/../../validations/review-validation.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/landing/index.php');
    exit;
}

$productId   = (int) ($_POST['product_id'] ?? 0);
$redirectUrl = "../../pages/products/show.php?id=" . $productId . "#reviews-section";

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk memberikan rating dan ulasan.';
    header("Location: $redirectUrl");
    exit;
}

$userId      = (int) $_SESSION['user']['id'];
$isAdminUser = (($_SESSION['user']['role'] ?? '') === 'admin');
$rating      = (int) ($_POST['rating'] ?? 0);
$comment     = (string) ($_POST['comment'] ?? '');

$product = $productId > 0 ? getProduct($productId) : null;
if (!$product) {
    header('Location: ../../pages/landing-page/index.php');
    exit;
}

$errors = validateProductReview($productId, $rating, $comment);
if (!empty($errors)) {
    $_SESSION['flash_error'] = $errors[0];
    header("Location: $redirectUrl");
    exit;
}

if ($isAdminUser || !hasUserPurchasedProduct($userId, $productId)) {
    $_SESSION['flash_error'] = 'Anda hanya dapat mengulas produk yang sudah Anda beli dan selesaikan.';
    header("Location: $redirectUrl");
    exit;
}

try {
    createProductReview($productId, $userId, $rating, $comment);
    $_SESSION['flash_success'] = 'Ulasan dan rating berhasil ditambahkan.';
} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Gagal menyimpan ulasan: ' . $e->getMessage();
}

header("Location: $redirectUrl");
exit;
