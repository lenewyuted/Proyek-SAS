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

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
$formErrors   = $_SESSION['form_errors'] ?? [];
$oldInput     = $_SESSION['old_input'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['form_errors'], $_SESSION['old_input']);

$fullName       = $oldInput['full_name'] ?? ($order['full_name'] ?? '');
$phoneNumber    = $oldInput['phone_number'] ?? ($order['phone_number'] ?? '');
$address        = $oldInput['address'] ?? ($order['address'] ?? '');
$city           = $oldInput['city'] ?? ($order['city'] ?? '');
$province       = $oldInput['province'] ?? ($order['province'] ?? '');
$postalCode     = $oldInput['postal_code'] ?? ($order['postal_code'] ?? '');
$shippingMethod = strtolower(trim($oldInput['shipping_method'] ?? ($order['shipping_method'] ?? 'regular')));
$shippingCost   = $oldInput['shipping_cost'] ?? ($order['shipping_cost'] ?? 35000);
$totalAmount    = $oldInput['total_amount'] ?? ($order['total_amount'] ?? 0);

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
    <title>TechHub Admin - Edit Order</title>

    <!-- Font & icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/components/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/components/admin/topbar.css">
    <link rel="stylesheet" href="../../../styles/admin/products.css">
    <link rel="stylesheet" href="../../../styles/admin/order-data.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>

    <div class="admin-container">
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>

        <main class="main-content">
            <!-- Header Halaman -->
            <div class="page-header-flex" style="margin-bottom: 25px;">
                <div class="header-col">
                    <h1>Edit Order</h1>
                    <p>Update customer shipping information, delivery method, and payment total</p>
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

            <form action="../../../actions/orders/update.php" method="POST" class="product-form">
                <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?= htmlspecialchars($fullName) ?>"
                            required>
                        <?php if (!empty($formErrors['full_name'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['full_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input
                            type="text"
                            id="phone_number"
                            name="phone_number"
                            value="<?= htmlspecialchars($phoneNumber) ?>"
                            required>
                        <?php if (!empty($formErrors['phone_number'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['phone_number']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea
                        id="address"
                        name="address"
                        required><?= htmlspecialchars($address) ?></textarea>
                    <?php if (!empty($formErrors['address'])): ?>
                        <div class="error-text"><?= htmlspecialchars($formErrors['address']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="<?= htmlspecialchars($city) ?>"
                            required>
                        <?php if (!empty($formErrors['city'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['city']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="province">Province</label>
                        <input
                            type="text"
                            id="province"
                            name="province"
                            value="<?= htmlspecialchars($province) ?>"
                            required>
                        <?php if (!empty($formErrors['province'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['province']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input
                            type="text"
                            id="postal_code"
                            name="postal_code"
                            value="<?= htmlspecialchars($postalCode) ?>"
                            required>
                        <?php if (!empty($formErrors['postal_code'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['postal_code']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="shipping_method">Shipping Method</label>
                        <select id="shipping_method" name="shipping_method">
                            <option value="regular" <?= $shippingMethod === 'regular' ? 'selected' : '' ?>>Regular</option>
                            <option value="express" <?= $shippingMethod === 'express' ? 'selected' : '' ?>>Express</option>
                        </select>
                        <?php if (!empty($formErrors['shipping_method'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['shipping_method']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="shipping_cost">Shipping Cost (Rp)</label>
                        <input
                            type="text"
                            id="shipping_cost"
                            name="shipping_cost"
                            value="<?= htmlspecialchars(formatRupiah($shippingCost)) ?>"
                            required>
                        <?php if (!empty($formErrors['shipping_cost'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['shipping_cost']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="total_amount">Total Amount (Rp)</label>
                        <input
                            type="text"
                            id="total_amount"
                            name="total_amount"
                            value="<?= htmlspecialchars(formatRupiah($totalAmount)) ?>"
                            required>
                        <?php if (!empty($formErrors['total_amount'])): ?>
                            <div class="error-text"><?= htmlspecialchars($formErrors['total_amount']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions-center">
                    <button type="submit" class="btn-submit-add">Save Changes</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>

</html>