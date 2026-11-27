<?php
session_start();
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../repositories/order-repository.php";
require_once __DIR__ . "/../../../validations/order-data-validation.php";

if (!isset($_SESSION['user']['id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk melihat riwayat pesanan.';
    header('Location: ../../auth/login.php');
    exit;
}

$userId       = (int) $_SESSION['user']['id'];
$statusFilter = validateOrderStatusFilter($_GET['status'] ?? null);
$orders       = getOrdersByUserId($userId, $statusFilter);

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Order History</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS kustom -->
    <link rel="stylesheet" href="../../../styles/components/landing/header.css">
    <link rel="stylesheet" href="../../../styles/components/landing/footer.css">
    <link rel="stylesheet" href="../../../styles/pages/order-history.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../../components/landing/header.php" ?>

    <main class="history-main-container">
        <!-- Judul Halaman -->
        <div class="history-header-title">
            <div>
                <h1>Order History</h1>
                <p>Track, manage, and review all your past hardware purchases</p>
            </div>
            <a href="../../landing-page/index.php" class="back-link">Back <span>&rarr;</span></a>
        </div>

        <?php if ($flashSuccess): ?>
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert-box alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <!-- Tab Filter Status Pesanan -->
        <div class="status-filter-tabs">
            <a href="?status=all" class="filter-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">All Orders</a>
            <a href="?status=pending" class="filter-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?status=processing" class="filter-tab <?= $statusFilter === 'processing' ? 'active' : '' ?>">Processing</a>
            <a href="?status=shipped" class="filter-tab <?= $statusFilter === 'shipped' ? 'active' : '' ?>">Shipped</a>
            <a href="?status=completed" class="filter-tab <?= $statusFilter === 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="?status=cancelled" class="filter-tab <?= $statusFilter === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
        </div>

        <!-- Daftar Riwayat Pesanan -->
        <section class="history-list-section">
            <?php if (empty($orders)): ?>
                <div class="empty-history-card">
                    <i class="fa-solid fa-box-open"></i>
                    <h3>Belum Ada Riwayat Pesanan</h3>
                    <p>Anda belum memiliki transaksi<?= $statusFilter !== 'all' ? ' dengan status ' . ucfirst($statusFilter) : '' ?>.</p>
                    <a href="../../landing/index.php" class="btn-shop-now">
                        <i class="fa-solid fa-arrow-left"></i> Mulai Belanja
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $orderId     = (int) $order['id'];
                    $orderNumber = $order['order_number'] ?? ('#' . $orderId);
                    $orderDate   = !empty($order['created_at']) ? date('d M Y, H:i', strtotime($order['created_at'])) : '-';
                    $statusRaw   = strtolower(trim((string) ($order['order_status'] ?? 'pending')));
                    $statusLabel = strtoupper($statusRaw);

                    $payMethod   = str_replace('_', ' ', strtoupper((string) ($order['payment_method'] ?? '-')));
                    $payChannel  = !empty($order['payment_channel']) ? ' (' . strtoupper($order['payment_channel']) . ')' : '';
                    $shipMethod  = ucfirst((string) ($order['shipping_method'] ?? 'regular'));

                    $items = function_exists('getOrderItemsByOrderId') ? getOrderItemsByOrderId($orderId) : [];
                    $totalItemCount = 0;
                    foreach ($items as $it) {
                        $totalItemCount += (int) ($it['quantity'] ?? 1);
                    }
                    ?>
                    <article class="order-card">
                        <!-- Header Card Order -->
                        <div class="order-card-header">
                            <div class="order-meta-left">
                                <span class="order-id-badge">
                                    <i class="fa-solid fa-receipt"></i> <?= htmlspecialchars($orderNumber) ?>
                                </span>
                                <span class="order-date">
                                    <i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($orderDate) ?>
                                </span>
                            </div>
                            <div class="order-meta-right">
                                <span class="status-pill status-<?= htmlspecialchars($statusRaw) ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Daftar Produk di Dalam Order -->
                        <div class="order-card-body">
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <?php
                                    $itemName  = $item['product_name'] ?? ('Product #' . ($item['product_id'] ?? '-'));
                                    $itemPic   = $item['product_picture'] ?? 'default.png';
                                    $itemPrice = (float) ($item['price'] ?? 0);
                                    $itemQty   = (int) ($item['quantity'] ?? 1);
                                    $itemSub   = isset($item['subtotal']) ? (float) $item['subtotal'] : ($itemPrice * $itemQty);
                                    ?>
                                    <div class="order-item-row">
                                        <div class="order-item-info">
                                            <div class="product-thumb">
                                                <img src="../../../assets/img/<?= htmlspecialchars($itemPic) ?>" alt="<?= htmlspecialchars($itemName) ?>">
                                            </div>
                                            <div>
                                                <div class="prod-name"><?= htmlspecialchars($itemName) ?></div>
                                                <div class="prod-qty-price"><?= $itemQty ?> x <?= formatRupiah($itemPrice) ?></div>
                                            </div>
                                        </div>
                                        <div class="order-item-subtotal">
                                            <?= formatRupiah($itemSub) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-items-text">Detail item tidak tersedia.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Footer Card Order -->
                        <div class="order-card-footer">
                            <div class="order-shipping-payment">
                                <span><i class="fa-solid fa-truck-fast"></i> <?= htmlspecialchars($shipMethod) ?> (<?= formatRupiah($order['shipping_cost'] ?? 0) ?>)</span>
                                <span class="dot-sep">&bull;</span>
                                <span><i class="fa-solid fa-credit-card"></i> <?= htmlspecialchars($payMethod . $payChannel) ?></span>
                            </div>

                            <div class="order-total-action">
                                <div class="order-grand-total">
                                    <span>Total (<?= $totalItemCount ?> Items):</span>
                                    <strong><?= formatRupiah($order['total_amount'] ?? 0) ?></strong>
                                </div>

                                <?php if ($statusRaw === 'pending'): ?>
                                    <form action="../../../actions/orders/cancel.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan <?= htmlspecialchars($orderNumber) ?>?');">
                                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                                        <button type="submit" class="btn-cancel-order">
                                            <i class="fa-solid fa-xmark"></i> Cancel Order
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="detail.php?order=<?= urlencode($orderNumber) ?>" class="btn-order-detail">
                                    View Detail <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <?php require_once __DIR__ . "/../../../components/landing/footer.php" ?>
</body>

</html>