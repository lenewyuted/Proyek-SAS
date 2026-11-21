<?php
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";
require_once __DIR__ . "/../../../repositories/order-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$orders = getOrders();

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Manage Orders</title>

    <!-- Font & icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/admin/topbar.css">
    <link rel="stylesheet" href="../../../styles/admin/order-data.css">
    <link rel="stylesheet" href="../../../styles/admin/pagination.css">
</head>

<body>
    <!-- Topbar -->
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
        <main class="main-content">
            <div class="page-header">
                <h1>Manage Orders</h1>
                <p>View, edit, and delete order data</p>
            </div>
            <div class="table-card">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Province</th>
                            <th>Postal Code</th>
                            <th>Shipping Method</th>
                            <th>Shipping Cost</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr onclick="window.location.href='show.php?id=<?= $order['id'] ?>'" style="cursor: pointer;">
                                <td class="order-name"><?= htmlspecialchars($order['full_name']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['phone_number']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['address']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['city']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['province']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['postal_code']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['shipping_method']); ?></td>
                                <td class="order-description"><?= formatRupiah($order['shipping_cost'] ?? 0); ?></td>
                                <td class="order-description"><?= formatRupiah($order['total_amount'] ?? 0); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Tambahkan event onclick.stopPropagation() agar tombol Edit & Delete tidak ikut membuka show.php -->
                                        <a href="edit.php?id=<?= $order['id'] ?>" class="btn-edit" onclick="event.stopPropagation();">Edit</a>
                                        <a href="../../../actions/orders/destroy.php?id=<?= $order['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus orderan ini?')">Delete</a>
                                        <a href="show.php?id=<?= $order['id'] ?>" class="btn-edit" onclick="event.stopPropagation();"><i class="bi bi-eye-fill"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>