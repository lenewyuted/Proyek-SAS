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

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'] = [
    "products" => [
        "id" => $productId,
        "quantity" => $quantity
    ]
];



// if (isset($_SESSION['cart'][$productId])) {
//     $_SESSION['cart'][$productId] += $quantity;
// } else {
//     $_SESSION['cart'][$productId] = $quantity;
// }

$_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke keranjang.';


echo "<script type='text/javascript'>
        setTimeout(function(){ location.href='/pages/products/show.php?id=$productId'; }, 100);
       </script>";
exit;