<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../middleware/auth.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../repositories/order-repository.php";
requireRole(["admin"]);

$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('Location: index.php');
    exit;
}

$orderId = (int) $id;
$order = getOrderById($orderId);
if (!$order) {
    header('Location: index.php');
    exit;
}

if (!empty($order['order_number']) && !array_key_exists('payment_method', $order) && function_exists('getOrderByNumber')) {
    $orderWithPayment = getOrderByNumber($order['order_number']);
    if (is_array($orderWithPayment)) {
        $order = array_merge($orderWithPayment, $order);
    }
}

$orderItems = getOrderItemsByOrderId($orderId);

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

$itemsSubtotal = 0;
foreach ($orderItems as $item) {
    $price = (float) ($item['price'] ?? 0);
    $qty   = (int) ($item['quantity'] ?? 1);
    $sub   = isset($item['subtotal']) ? (float) $item['subtotal'] : ($price * $qty);
    $itemsSubtotal += $sub;
}

$currentStatus = strtolower(trim((string) ($order['order_status'] ?? 'pending')));

$approveLabel = null;
switch ($currentStatus) {
    case 'pending':
        $approveLabel = 'Approve (Processing)';
        break;
    case 'processing':
        $approveLabel = 'Approve (Shipped)';
        break;
    case 'shipped':
        $approveLabel = 'Approve (Completed)';
        break;
}

$canReject = in_array($currentStatus, ['pending', 'processing', 'shipped'], true);

$paymentMethodLabel = str_replace('_', ' ', strtoupper((string) ($order['payment_method'] ?? '-')));
$paymentChannelText = !empty($order['payment_channel']) ? ' (' . strtoupper((string) $order['payment_channel']) . ')' : '';
$paymentStatusText  = str_replace('_', ' ', strtoupper((string) ($order['payment_status'] ?? '-')));

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Order Detail</title>

    <!-- Font & icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/admin/topbar.css">
    <link rel="stylesheet" href="../../../styles/admin/products.css">
    <link rel="stylesheet" href="../../../styles/admin/order-data.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>

    <div class="admin-container">
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>

        <main class="main-content">

            <div class="page-header-flex" style="margin-bottom: 25px;">
                <div class="header-col">
                    <h1>Detail Order</h1>
                    <p>Order Number: <strong><?= htmlspecialchars($order['order_number'] ?? ('#' . $order['id'])) ?></strong></p>
                </div>
                <div>
                    <a href="index.php" class="back-link">
                        Back &rarr;
                    </a>
                </div>
            </div>

            <?php if ($flashSuccess): ?>
                <div class="flash-success">
                    <?= htmlspecialchars($flashSuccess) ?>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="flash-error">
                    <?= htmlspecialchars($flashError) ?>
                </div>
            <?php endif; ?>

            <!-- Informasi Order -->
            <div class="order-show-section">
                <div class="order-detail-card plain-section">

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Order Number</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['order_number'] ?? ('#' . $order['id'])) ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Order Status</label>
                            <div class="show-field-value"><?= htmlspecialchars(strtoupper($order['order_status'] ?? 'pending')) ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Full Name</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['full_name'] ?? '-') ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Phone Number</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['phone_number'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="show-field-group">
                        <label>Address</label>
                        <div class="show-field-value textarea-value">
                            <?= nl2br(htmlspecialchars($order['address'] ?? '-')) ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>City</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['city'] ?? '-') ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Province</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['province'] ?? '-') ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Postal Code</label>
                            <div class="show-field-value"><?= htmlspecialchars($order['postal_code'] ?? '-') ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Shipping Method</label>
                            <div class="show-field-value"><?= htmlspecialchars(ucfirst($order['shipping_method'] ?? 'regular')) ?></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="show-field-group">
                            <label>Shipping Cost</label>
                            <div class="show-field-value"><?= formatRupiah($order['shipping_cost'] ?? 0) ?></div>
                        </div>
                        <div class="show-field-group">
                            <label>Total Amount</label>
                            <div class="show-field-value"><?= formatRupiah($order['total_amount'] ?? 0) ?></div>
                        </div>
                    </div>

                    <?php if (isset($order['payment_method']) || isset($order['payment_status']) || isset($order['account_holder']) || isset($order['account_number']) || isset($order['payment_proof'])): ?>
                        <div class="form-row">
                            <div class="show-field-group">
                                <label>Payment Method</label>
                                <div class="show-field-value"><?= htmlspecialchars($paymentMethodLabel . $paymentChannelText) ?></div>
                            </div>
                            <div class="show-field-group">
                                <label>Payment Status</label>
                                <div class="show-field-value"><?= htmlspecialchars($paymentStatusText) ?></div>
                            </div>
                        </div>

                        <?php if (!empty($order['account_holder']) || !empty($order['account_number'])): ?>
                            <div class="form-row">
                                <div class="show-field-group">
                                    <label>Account Holder</label>
                                    <div class="show-field-value"><?= htmlspecialchars($order['account_holder'] ?? '-') ?></div>
                                </div>
                                <div class="show-field-group">
                                    <label>Account / Card / Phone Number</label>
                                    <div class="show-field-value"><?= htmlspecialchars($order['account_number'] ?? '-') ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order['payment_proof'])): ?>
                            <div class="show-field-group">
                                <label>Payment Proof</label>
                                <?php
                                $proofUrl = '../../../assets/img/payments/' . rawurlencode($order['payment_proof']);
                                $isPdf    = strtolower(pathinfo($order['payment_proof'], PATHINFO_EXTENSION)) === 'pdf';
                                ?>
                                <a href="<?= $proofUrl ?>" target="_blank" class="payment-proof-box">
                                    <?php if ($isPdf): ?>
                                        <i class="bi bi-file-earmark-pdf" style="font-size: 36px; color: #ef4444;"></i>
                                    <?php else: ?>
                                        <img src="<?= $proofUrl ?>" alt="Payment Proof">
                                    <?php endif; ?>
                                    <span>Klik untuk melihat bukti pembayaran (<?= htmlspecialchars($order['payment_proof']) ?>)</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Daftar Item Order (order_items) -->
                <div class="order-detail-card" style="padding: 0; overflow: hidden;">
                    <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0;">
                        <h2 style="margin-bottom: 0;">Ordered Items (<?= count($orderItems) ?>)</h2>
                    </div>

                    <?php if (empty($orderItems)): ?>
                        <div style="padding: 32px 24px; text-align: center; color: var(--text-muted);">
                            Tidak ada item pada pesanan ini.
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="order-items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <?php
                                        $itemName  = $item['product_name'] ?? ('Product #' . ($item['product_id'] ?? '-'));
                                        $itemPic   = $item['product_picture'] ?? 'default.png';
                                        $itemPrice = (float) ($item['price'] ?? 0);
                                        $itemQty   = (int) ($item['quantity'] ?? 1);
                                        $itemSub   = isset($item['subtotal']) ? (float) $item['subtotal'] : ($itemPrice * $itemQty);
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="order-item-product">
                                                    <div class="order-item-thumb">
                                                        <img src="../../../assets/img/<?= htmlspecialchars($itemPic) ?>" alt="<?= htmlspecialchars($itemName) ?>">
                                                    </div>
                                                    <span style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($itemName) ?></span>
                                                </div>
                                            </td>
                                            <td><?= formatRupiah($itemPrice) ?></td>
                                            <td><?= $itemQty ?></td>
                                            <td style="font-weight: 700;"><?= formatRupiah($itemSub) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="padding: 0 24px 20px;">
                            <div class="order-summary-total">
                                <div class="order-summary-row">
                                    <span style="color: var(--text-muted);">Items Subtotal</span>
                                    <strong><?= formatRupiah($itemsSubtotal) ?></strong>
                                </div>
                                <div class="order-summary-row">
                                    <span style="color: var(--text-muted);">Shipping Cost</span>
                                    <strong><?= formatRupiah($order['shipping_cost'] ?? 0) ?></strong>
                                </div>
                                <div class="order-summary-row order-summary-grand">
                                    <span>Grand Total</span>
                                    <span><?= formatRupiah($order['total_amount'] ?? ($itemsSubtotal + (float) ($order['shipping_cost'] ?? 0))) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($approveLabel !== null || $canReject): ?>
                <div class="order-actions-bottom">
                    <?php if ($approveLabel !== null): ?>
                        <form action="../../../actions/orders/update-status.php" method="POST">
                            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn-submit-add">
                                <?= htmlspecialchars($approveLabel) ?>
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($canReject): ?>
                        <form action="../../../actions/orders/update-status.php" method="POST" onsubmit="return confirm('Yakin ingin menolak/membatalkan pesanan ini?');">
                            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn-reject-order">
                                Reject
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>