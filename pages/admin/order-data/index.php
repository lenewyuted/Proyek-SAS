<?php
require_once __DIR__ . "/../../../repositories/product-repository.php";
require_once __DIR__ . "/../../../repositories/category-repository.php";
require_once __DIR__ . "/../../../repositories/order-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$orders = getOrders();
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
    <link rel="stylesheet" href="../../../styles/admin/categories.css">
    <link rel="stylesheet" href="../../../styles/admin/pagination.css">

    <style>
        .main-content {
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .table-card {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            padding: 0 !important;
        }

        .table-card .product-table {
            display: table !important;
            width: max-content !important;
            min-width: 100% !important;
            table-layout: auto !important;
            border-collapse: collapse !important;
        }

        .table-card .product-table thead,
        .table-card .product-table tbody,
        .table-card .product-table tr {
            display: table-row-group;
        }

        .table-card .product-table tr {
            display: table-row !important;
        }

        .table-card .product-table th,
        .table-card .product-table td {
            display: table-cell !important;
            position: static !important;
            float: none !important;
            white-space: nowrap !important;
            padding: 14px 18px !important;
            text-align: left !important;
            vertical-align: middle !important;
        }
    </style>
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
                <p>Add, edit, and delete order data</p>
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
                                <td class="order-description"><?= htmlspecialchars($order['shipping_cost']); ?></td>
                                <td class="order-description"><?= htmlspecialchars($order['total_amount']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Tambahkan event onclick.stopPropagation() agar tombol Edit & Delete tidak ikut membuka show.php -->
                                        <a href="edit.php?id=<?= $order['id'] ?>" class="btn-edit" onclick="event.stopPropagation();">Edit</a>
                                        <a href="../../../actions/categories/destroy.php?id=<?= $order['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Yakin ingin menghapus produk ini?')">Delete</a>
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