<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../repositories/order-repository.php";
requireRole(["admin"]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/orders/index.php');
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ../../pages/admin/orders/index.php');
    exit;
}

$data = [
    'full_name'       => trim($_POST['full_name'] ?? ''),
    'phone_number'    => trim($_POST['phone_number'] ?? ''),
    'address'         => trim($_POST['address'] ?? ''),
    'city'            => trim($_POST['city'] ?? ''),
    'province'        => trim($_POST['province'] ?? ''),
    'postal_code'     => trim($_POST['postal_code'] ?? ''),
    'shipping_method' => trim($_POST['shipping_method'] ?? ''),
    'shipping_cost'   => $_POST['shipping_cost'] ?? '',
    'total_amount'    => $_POST['total_amount'] ?? '',
];

$errors = [];

if ($data['full_name'] === '')       $errors['full_name'] = 'Full name is required.';
if ($data['phone_number'] === '')    $errors['phone_number'] = 'Phone number is required.';
if ($data['address'] === '')         $errors['address'] = 'Address is required.';
if ($data['city'] === '')            $errors['city'] = 'City is required.';
if ($data['province'] === '')        $errors['province'] = 'Province is required.';
if ($data['postal_code'] === '')     $errors['postal_code'] = 'Postal code is required.';
if ($data['shipping_method'] === '') $errors['shipping_method'] = 'Shipping method is required.';
if ($data['shipping_cost'] === '' || !is_numeric($data['shipping_cost'])) {
    $errors['shipping_cost'] = 'Valid shipping cost is required.';
}
if ($data['total_amount'] === '' || !is_numeric($data['total_amount'])) {
    $errors['total_amount'] = 'Valid total amount is required.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = array_merge(['id' => $id], $data);
    header("Location: ../../pages/admin/orders/edit.php?id=" . urlencode($id));
    exit;
}

updateOrder($id, $data);

header('Location: ../../pages/admin/order-data/index.php');
exit;
